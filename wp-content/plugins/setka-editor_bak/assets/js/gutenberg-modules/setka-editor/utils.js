/**
 * Finds theme name in the HTML markup.
 *
 * @param html {string}
 *
 * @throws {Error} If theme class name not found.
 *
 * @return {string} Class name of Setka Editor theme.
 */
export function findTheme(html) {
    // Find a div with class 'stk-theme_*'
    const expression = /<div[^>]*class\s*=\s*['\"][\w-_\s]*(stk-theme_[\w]+)['\"\w\s\.\-\=]*>/;

    return findInHTML(html, expression, 'Couldn\'t found the theme in the markup.');
}

/**
 * Finds layout name in the HTML markup.
 *
 * @param html {string}
 *
 * @throws {Error} If layout class name not found.
 *
 * @return {string} Class name of Setka Editor layout.
 */
export function findLayout(html) {
    // Find a div with class 'stk-layout_*'
    const expression = /<div[^>]*class\s*=\s*['\"][\w-_\s]*(stk-layout_[\w]+)['\"\w\s\.\-\=]*>/;

    return findInHTML(html, expression, 'Couldn\'t found the layout in the markup.');
}

/**
 * Internal method for execute regular expressions.
 *
 * @param html {string}
 * @param expression {RegExp}
 * @param errorMessage {string}
 *
 * @throws {Error} If not found.
 *
 * @return {string} Result of group[1] in regular expression.
 */
function findInHTML(html, expression, errorMessage) {
    let result = html.match(expression);

    if('object' === typeof result && 'undefined' !== typeof result[1]) {
        return result[1];
    }
    throw new Error(errorMessage);
}
