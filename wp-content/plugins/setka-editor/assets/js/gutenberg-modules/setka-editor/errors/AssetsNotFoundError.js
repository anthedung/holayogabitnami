/**
 * If Setka Editor assets was not found.
 */
export default class AssetsNotFoundError extends Error {

    constructor() {
        super();
        this.theme  = null;
        this.layout = null;
    }
}
