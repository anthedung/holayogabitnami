<?php
namespace Setka\Editor\Service;

/**
 * Class SetkaPostTypes
 */
class SetkaPostTypes
{
    const FILE_POST_NAME = 'setka_editor_file';

    /**
     * Posts with CSS styles which uses for all Setka Editor AMP posts pages.
     */
    const AMP_COMMON = 'setka_editor_001';

    /**
     * Posts with CSS styles which holds styles for each Setka Editor theme.
     */
    const AMP_THEME = 'setka_editor_050';

    /**
     * Posts with CSS styles which holds styles for each Setka Editor layout.
     */
    const AMP_LAYOUT = 'setka_editor_100';

    /**
     * Posts with AMP config (array with links and theme and layout ids).
     */
    const AMP_CONFIG = 'setka_editor_150';

    /**
     * Returns post type name in WordPress DB.
     *
     * @param $section string section name (common, theme, layout).
     *
     * @throws \Exception If passed type not supported.
     *
     * @return string Post type name.
     */
    public static function getPostType($section)
    {
        switch ($section) {
            case 'common':
                return self::AMP_COMMON;
                break;

            case 'themes':
                return self::AMP_THEME;
                break;

            case 'layouts':
                return self::AMP_LAYOUT;
                break;

            default:
                throw new \Exception('Not supported name.');
        }
    }
}
