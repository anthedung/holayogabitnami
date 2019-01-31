import { findLayout, findTheme } from './utils';
import AssetsNotFoundError from './errors/AssetsNotFoundError';
import ThemeNotFoundError from "./errors/ThemeNotFoundError";
import LayoutNotFoundError from './errors/LayoutNotFoundError';

import { Warning, PlainText } from '@wordpress/editor';
import { select, dispatch, subscribe } from '@wordpress/data';
//import Button from '@wordpress/components'; TODO: refactor without global variable usage
import { Component, createElement, RawHTML } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

const BLOCK_SELECTED_HTML_CLASS = 'setka-editor-selected';

export default class SetkaEditor extends Component {

    /**
     * @var {EditorAssets}
     */
    assets = null;

    /**
     * @var {AdminMenu}
     */
    adminMenu = null;

    /**
     * Store the setInterval ID which saves the content from Setka to Gutenberg.
     */
    saveIntervalId = null;

    state = {
        assetsError: false,
        renderComponent: 'editor', // 'html-editor' | 'warning'
    };

    isEditorSidebarOpened = null;

    _storageChangesUnsubscribe = null;

    /**
     * Store previous content for Setka Editor (used in componentDidUpdate).
     */
    contentSavedIntoProps;

    constructor(props) {
        super(props);
        this.initialize = this.initialize.bind(this);
        this.saveContent = this.saveContent.bind(this);
        this.onChange = this.onChange.bind(this);

        this.assets = props.attributes.assets;
        delete props.attributes.assets;

        this.adminMenu = props.attributes.adminMenu;
        delete props.attributes.adminMenu;
    }

    componentDidMount() {
        if (document.readyState === 'complete') {
            this.assets.assetsState.then(() => {
                this.initialize();
            })
        } else {
            window.addEventListener('DOMContentLoaded', () => {
                this.assets.assetsState.then(() => {
                    this.initialize();
                })
            });
        }
    }

    componentWillUnmount() {
        //window.removeEventListener('DOMContentLoaded', this.initialize);
        //wp.oldEditor.remove( `editor-${ this.props.id }` );

        this.disableEditorAndAutoSave();
    }

    initialize() {
        // Fold admin menu (left column) if page less than 1340px and greater than 782 in width
        // On medium screens all panels automatically collapse.
        if(document.body.clientWidth < 1340 && select('core/viewport').isViewportMatch('>= medium')) {
            this.adminMenu.fold();

            if(document.body.clientWidth < 1216) {
                dispatch('core/edit-post').closeGeneralSidebar();
            }
        }

        try {
            if('undefined' === typeof this.props.attributes.content || '' === this.props.attributes.content) {
                this.enableEditorAndAutoSave();
            } else {
                let assets = this.prepareAssetsFromAttributes();
                this.enableEditorAndAutoSave();
                this.replaceContentAndAssets(this.props.attributes.content, assets);
            }
            this.setupIsSelected();
        } catch (error) {
            this.showErrors(error);
        }

        return this;
    }

    onChange() {
        if (this.isEditorSidebarOpened !== select('core/edit-post').isEditorSidebarOpened()) {
            setTimeout(() => {
                window.SetkaEditor.trigger('editor:width');
            }, 0);
            this.isEditorSidebarOpened = select('core/edit-post').isEditorSidebarOpened();
        }
    }

    enableEditorAndAutoSave() {
        this.contentSavedIntoProps = this.props.attributes.content;
        window.SetkaEditor.start(this.assets.config, this.assets.assets);
        this.saveIntervalId = setInterval(this.saveContent, 5000);

        this.isEditorSidebarOpened = select('core/edit-post').isEditorSidebarOpened();
        this._storageChangesUnsubscribe = subscribe(this.onChange);

        return this;
    }

    disableEditorAndAutoSave() {
        clearInterval(this.saveIntervalId);
        this.saveIntervalId = null;

        if(this.is2Version()) {
            window.SetkaEditor.stop();
        }

        if('function' === typeof this._storageChangesUnsubscribe) {
            this._storageChangesUnsubscribe();
            this._storageChangesUnsubscribe = null;
        }

        return this;
    }

    /**
     * Find Setka Editor assets from block attributes.
     *
     * @throws {AssetsNotFoundError} If required theme or layout not found in assets storage.
     *
     * @return {{theme: (*|attributes.setkaEditorTheme|{type}), layout: (*|attributes.setkaEditorLayout|{type})}}
     */
    prepareAssetsFromAttributes() {
        let complexError = new AssetsNotFoundError();

        try {
            this.assets.getThemeById(this.props.attributes.setkaEditorTheme);
        } catch (error) {
            complexError.theme = error;
        }

        try {
            this.assets.getLayoutById(this.props.attributes.setkaEditorLayout);
        } catch (error) {
            complexError.layout = error;
        }

        if(complexError.theme || complexError.layout) {
            throw complexError;
        }

        return {
            theme: this.props.attributes.setkaEditorTheme,
            layout: this.props.attributes.setkaEditorLayout,
        };
    }

    /**
     * Find Setka Editor assets from content string.
     *
     * @param content {string} HTML markup.
     *
     * @throws {AssetsNotFoundError} If required theme or layout not found in assets storage.
     *
     * @return {Object} Assets
     */
    prepareAssetsFromContent(content) {
        let themeClassName,
            layoutClassName,
            assets = {
                theme: null,
                layout: null,
            };

        try {
            themeClassName  = findTheme(content);
            layoutClassName = findLayout(content);
        } catch(error) {}

        let complexError = new AssetsNotFoundError();

        if(themeClassName) {
            try {
                assets.theme = this.assets.getThemeByClassName(themeClassName).id;
            } catch (error) {
                complexError.theme = error;
            }
        } else {
            complexError.theme = new ThemeNotFoundError('UKNOWN');
        }

        if(layoutClassName) {
            try {
                assets.layout = this.assets.getLayoutByClassName(layoutClassName).id;
            } catch (error) {
                complexError.layout = error;
            }
        } else {
            complexError.layout = new LayoutNotFoundError('UKNOWN');
        }

        if(complexError.theme || complexError.layout) {
            throw complexError;
        }

        return assets;
    }

    /**
     * This method created for support undo/redo events.
     *
     * @param prevProps {Object} Previous properties value.
     *
     * @see handleAssets
     */
    componentDidUpdate(prevProps) {
        this.setupIsSelected();

        if(!this.isEditorRuns()) {
            return;
        }

        // Because we save content every 5 seconds (not on every update) there is an opportunity
        // to start replacing content in the editor even if the editor itself contains a newer version of content.
        if('string' === typeof this.props.attributes.content
            &&
            this.contentSavedIntoProps !== this.props.attributes.content
        ) {
            try {
                this.replaceContentAndAssets(
                    this.props.attributes.content,
                    this.prepareAssetsFromContent(this.props.attributes.content)
                );
            } catch (error) {
                this.showErrors(error);
            }
        }
    }

    /**
     * Saves content from editor into Gutenberg (WordPress).
     *
     * @return {this} For chain calls.
     */
    saveContent() {
        let theme = window.SetkaEditor.getCurrentTheme();
        let attributes = {
            content: window.SetkaEditor.getHTML(),
            setkaEditorTheme: theme.id,
            setkaEditorLayout: window.SetkaEditor.getCurrentLayout().id,
        };

        if('string' === typeof theme.kit_id && '' !== theme.kit_id) {
            attributes.setkaEditorTypeKitId = theme.kit_id;
        }

        this.contentSavedIntoProps = attributes.content;
        this.props.setAttributes(attributes);

        return this;
    }

    /**
     * Replace content and assets in Setka Editor.
     *
     * @return {this} For chain calls.
     */
    replaceContentAndAssets(content, assets) {

        if('object' !== typeof assets) {
            return this;
        }

        if('string' !== typeof assets.theme || 'string' !== typeof assets.layout) {
            return this;
        }

        if(this.is2Version()) {
            window.SetkaEditor.setTheme(assets.theme);
            window.SetkaEditor.setLayout(assets.layout);
        } else {
            //window.SetkaEditor.post.setTheme(assets.theme);
            window.__store.setTheme(assets.theme);

            //window.SetkaEditor.post.setLayout(assets.layout);
            window.__store.setLayout(assets.layout);
        }

        window.SetkaEditor.replaceHTML(content);

        return this;
    }

    render() {
        switch (this.state.renderComponent) {
            case 'warning':
            default:
                return <wp.editor.Warning key="setka-warning" actions={[
                    <wp.components.Button key="show-html-editor" isLarge onClick={ this.showHtmlEditor }>{ __('Show HTML', 'setka-editor') }</wp.components.Button>,
                    <wp.components.Button key="support" isLarge onClick={ () => { window.open('https://editor-help.setka.io') } }>{ __('Help Center', 'setka-editor') }</wp.components.Button>,
                    <wp.components.Button key="settings" isLarge onClick={ () => { window.open(setkaEditorGutenbergModules.settingsUrl) } }>{ __('Plugin Settings', 'setka-editor') }</wp.components.Button>,
                ]}>
                    <RawHTML>{ sprintf(
                        __('Setka Editor can\'t be launched because Style or Grid System were removed from Style Manager or you’ve changed your license key. Please contact <a href="%s" target="_blank">Setka Editor team</a>.', 'setka-editor'),
                        'https://editor.setka.io/support'
                    ) }</RawHTML>
                </wp.editor.Warning>;

            case 'html-editor':
                return <PlainText
                    value={ this.props.attributes.content }
                    onChange={ (content) => this.props.setAttributes({ content }) } />;

            case 'editor':
                let typeKit = null;

                if ('string' === typeof this.props.attributes.setkaEditorTypeKitId && !this.is2Version()) {
                    typeKit = <link rel="stylesheet" href={'//use.typekit.net/' + this.props.attributes.setkaEditorTypeKitId + '.css'} />;
                }

                return <div>
                    <div key="editor" id="setka-editor" className="stk-editor"/>
                    { typeKit }
                </div>;
        }
    }

    showHtmlEditor = () => {
        this.setState({renderComponent: 'html-editor'});
    }

    showErrors(error) {
        this.disableEditorAndAutoSave();
        this.setState({renderComponent: 'warning', assetsError: true});

        if(error.theme) {
            dispatch('core/notices').createErrorNotice(error.theme.message, {__unstableHTML: true});
        }
        if(error.layout) {
            dispatch('core/notices').createErrorNotice(error.layout.message, {__unstableHTML: true});
        }

        return this;
    }

    is2Version() {
        return 'string' === typeof window.SetkaEditor.version;
    }

    getHtmlFromEditor() {
        return window.SetkaEditor.getHTML();
    }

    isEditorRuns() {
        return 'number' === typeof this.saveIntervalId;
    }

    /**
     * Setup additional CSS class for <html> tag if block selected.
     *
     * @return {this} For chain calls.
     */
    setupIsSelected() {
        if(this.props.isSelected) {
            jQuery(document.documentElement).addClass(BLOCK_SELECTED_HTML_CLASS);
        } else {
            jQuery(document.documentElement).removeClass(BLOCK_SELECTED_HTML_CLASS);
        }
        return this;
    }
}
