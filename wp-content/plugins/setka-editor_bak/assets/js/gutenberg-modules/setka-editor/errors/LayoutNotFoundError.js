import { __, sprintf } from '@wordpress/i18n';

/**
 * If Setka Editor layout was not found.
 */
export default class LayoutNotFoundError extends Error {

    constructor(requiredLayoutClassName) {
        super(sprintf(
            '<p>' + __('Grid System "<code>%s</code>" was removed from Style Manager or you’ve changed your license key. Please contact <a href="%s" target="_blank">Setka Editor team</a>.', 'setka-editor') + '</p>',
            requiredLayoutClassName,
            'https://editor.setka.io/support'
        ));
        this.requiredLayoutClassName = requiredLayoutClassName;
    }
}
