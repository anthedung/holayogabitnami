import ThemeNotFoundError from './errors/ThemeNotFoundError';
import LayoutNotFoundError from './errors/LayoutNotFoundError';
import AssetsObjectInvalidError from './errors/AssetsObjectInvalidError';

const DESKTOP_HEADER_TOP_OFFSET = 88;

/**
 * Setka Editor assets.
 */
export default class EditorAssets {

    assets = null

    config = null

    assetsStatus = false

    assetsState = null

    constructor(config) {
        this.config = { ...config };
        this.config.headerTopOffset = DESKTOP_HEADER_TOP_OFFSET;
    }

    /**
     * Initializing loading JSON config for Setka Editor.
     *
     * @return {Promise}
     */
    startLoading(url) {
        return new Promise((resolve, reject) => {
            fetch(url)
                .then(response => {
                    if(200 === response.status) {
                        return response.json();
                    } else {
                        reject();
                    }
                })
                .then(fetchedConfig => {
                    this.config = { ...this.config, ...fetchedConfig.config };
                    this.assets = { ...fetchedConfig.assets };
                    this.assetsStatus = true;
                    resolve(fetchedConfig);
                })
                .catch(exception => {
                    reject(exception);
                })
        });
    }

    /**
     * Finds theme by classname.
     *
     * @param className {string} CSS class name.
     *
     * @throws {Error} If theme not found.
     *
     * @return {object} Theme object.
     */
    getThemeByClassName(className) {
        if('object' !== typeof this.assets.themes) {
            throw new AssetsObjectInvalidError();
        }

        let result = this.assets.themes.find(theme => theme.class_name === className);

        if(result) {
            return result
        }
        throw new ThemeNotFoundError(className);
    }

    /**
     * Finds layout by classname.
     *
     * @param className {string} CSS class name.
     *
     * @throws {Error} If layout not found.
     *
     * @return {object} Layout object.
     */
    getLayoutByClassName(className) {
        if('object' !== typeof this.assets.layouts) {
            throw new AssetsObjectInvalidError();
        }

        let result = this.assets.layouts.find(layout => layout.class_name === className);

        if(result) {
            return result
        }
        throw new LayoutNotFoundError(className);
    }

    /**
     * Finds theme by ID.
     *
     * @param id {string} CSS class name.
     *
     * @throws {Error} If theme not found.
     *
     * @return {object} Theme object.
     */
    getThemeById(id) {
        if('object' !== typeof this.assets.themes) {
            throw new AssetsObjectInvalidError();
        }

        let result = this.assets.themes.find(theme => theme.id === id);

        if(result) {
            return result
        }
        throw new ThemeNotFoundError(id);
    }

    /**
     * Finds layout by ID.
     *
     * @param id {string} CSS class name.
     *
     * @throws {Error} If layout not found.
     *
     * @return {object} Layout object.
     */
    getLayoutById(id) {
        if('object' !== typeof this.assets.layouts) {
            throw new AssetsObjectInvalidError();
        }

        let result = this.assets.layouts.find(layout => layout.id === id);

        if(result) {
            return result
        }
        throw new LayoutNotFoundError(id);
    }

    /**
     * Sets assets promise.
     *
     * @param promise {Promise} A promise which indicates the state of assets loading.
     *
     * @return {this} For chain calls.
     */
    setAssetsState(promise) {
        this.assetsState = promise;
        return this;
    }

    /**
     * Sets assets status then assets ready to use.
     *
     * @param status {bool} State of assets. True if ready to use.
     *
     * @return {this} For chain calls.
     */
    setAssetsStatus(status) {
        this.assetsStatus = status;
        return this;
    }

    /**
     * Returns assets status.
     *
     * @return {bool} True if assets ready to use.
     */
    getAssetsStatus() {
        return this.assetsStatus;
    }
}
