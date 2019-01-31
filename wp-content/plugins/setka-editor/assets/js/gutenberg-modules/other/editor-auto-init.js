import { parse as queryParse } from  'query-string';
import { dispatch, select, subscribe } from '@wordpress/data';
import { createBlock } from '@wordpress/blocks';

let query = queryParse(window.location.search);

if('object' === typeof query && 'undefined' !== typeof query[setkaEditorGutenbergModules.name + '-auto-init']) {
    let callback = () => {
        if(select('core/editor').isEditedPostEmpty()
            &&
            select('core/editor').isEditedPostNew()
        ) {
            unsubscribe();
            let block = wp.blocks.createBlock('setka-editor/setka-editor');
            dispatch('core/editor').insertBlocks(block);
        }
    };

    let unsubscribe = subscribe(callback);
}
