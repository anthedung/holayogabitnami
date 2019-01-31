import SetkaEditor from './SetkaEditor';
import EditorAssets from './EditorAssets';
import AdminMenu from './AdminMenu';
import SetkaIcon from './SetkaIcon';

import { RawHTML } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

const alignVariants = ['left', 'center', 'right', 'wide', 'full'];

const assets = new EditorAssets(setkaEditorGutenbergModules.settings.editorConfig);
assets.setAssetsState(
    assets.startLoading(setkaEditorGutenbergModules.settings.themeData)
);

const adminMenu = new AdminMenu();

wp.blocks.registerBlockType('setka-editor/setka-editor', {

    title: __('Setka Editor', 'setka-editor'),

    description: __('Setka Editor allows content teams to create unique layouts that perfectly fit each story without having to code. It also allows you to customize your design elements so your brand style can shine through.', 'setka-editor'),

    keywords: [
        __('template', 'setka-editor'),
        __('column', 'setka-editor'),
        __('animation', 'setka-editor'),
    ],

    icon: SetkaIcon,

    category: 'layout',

    attributes: {
        content: {
            type: 'string',
            source: 'html',
            selector: 'div',
        },
        align: {
            type: 'string',
            default: 'full',
        },
        setkaEditorTheme: {
            type: 'string',
        },
        setkaEditorLayout: {
            type: 'string',
        },
        setkaEditorTypeKitId: {
            type: 'string',
        },
        assets: {
            type: 'object',
            default: assets,
        },
        adminMenu: {
            type: 'object',
            default: adminMenu,
        },
    },

    supports: {
        multiple: false,
        className: false,
        customClassName: false,
        align: ['wide', 'full'],
    },

    edit: SetkaEditor,

    getEditWrapperProps(attributes) {
        const { align } = attributes;

        if (alignVariants.includes(align)) {
            return {'data-align': align};
        }
    },

    save({ attributes }) {
        return <RawHTML>{ attributes.content }</RawHTML>;
    },
});
