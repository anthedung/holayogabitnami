import {__, sprintf} from "@wordpress/i18n";

/**
 * If Setka Editor assets object is invalid.
 */
export default class AssetsObjectInvalidError extends Error {

    constructor() {
        super(sprintf(
            '<p>' + __('Setka Editor can\'t be launched. There are no styles or grid systems in <code>.json</code> file. Please contact <a href="%s" target="_blank">Setka Editor team</a>.', 'setka-editor') + '</p>',
            'https://editor.setka.io/support'
        ));
    }
}
