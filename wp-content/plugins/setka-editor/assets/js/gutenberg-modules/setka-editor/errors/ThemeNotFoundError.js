import { __, sprintf } from '@wordpress/i18n';

/**
 * If Setka Editor theme was not found.
 */
export default class ThemeNotFoundError extends Error {

    constructor(requiredThemeClassName) {
        super(sprintf(
            '<p>' + __('Style "<code>%s</code>" was removed from Style Manager or you’ve changed your license key. Please contact <a href="%s" target="_blank">Setka Editor team</a>.', 'setka-editor') + '</p>',
            requiredThemeClassName,
            'https://editor.setka.io/support'
        ));
        this.requiredThemeClassName = requiredThemeClassName;
    }
}
