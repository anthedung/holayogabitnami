import { isEmpty } from 'lodash';
import { dispatch } from '@wordpress/data';

function prepareNotices() {
    if(isEmpty(setkaEditorGutenbergModules.notices)) {
        return;
    }

    setkaEditorGutenbergModules.notices.forEach((notice) => {
        dispatch('core/notices').createNotice(
            notice.status,
            notice.content,
            {
                speak: false,
                __unstableHTML: true,
                isDismissible: notice.isDismissible,
            }
        );
    });
}
prepareNotices();
