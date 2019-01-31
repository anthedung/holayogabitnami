<?php
namespace Setka\Editor\Admin\Service\EditorConfigGenerator;

use Setka\Editor\Admin\Options;
use Setka\Editor\Admin\Options\ThemeResourceCSSLocalOption;
use Setka\Editor\Admin\Options\ThemeResourceJSLocalOption;
use Setka\Editor\Admin\Options\Files\UseLocalFilesOption;
use Setka\Editor\Admin\Service\Filesystem\Filesystem;
use Setka\Editor\Admin\Service\Filesystem\FilesystemFactory;
use Setka\Editor\Admin\Service\WPQueryFactory;
use Setka\Editor\PostMetas\FileSubPathPostMeta;
use Setka\Editor\Service\Config\Files;

class EditorConfigGeneratorFactory
{

    /**
     * @param Filesystem|null $filesystem
     * @param FileSubPathPostMeta|null $fileSubPathMeta
     * @param Options\ThemeResourceJSOption|null $themeResourceJSOption
     * @param Options\ThemeResourceCSSOption|null $themeResourceCSSOption
     * @param UseLocalFilesOption|null $useLocalFilesOption
     * @param ThemeResourceJSLocalOption|null $themeResourceJSLocalOption
     * @param ThemeResourceCSSLocalOption|null $themeResourceCSSLocalOption
     * @return EditorConfigGenerator
     * @throws Exceptions\ConfigFileEntryException
     */
    public static function create(
        Filesystem $filesystem = null,
        FileSubPathPostMeta $fileSubPathMeta = null,
        Options\ThemeResourceJSOption $themeResourceJSOption = null,
        Options\ThemeResourceCSSOption $themeResourceCSSOption = null,
        UseLocalFilesOption $useLocalFilesOption = null,
        ThemeResourceJSLocalOption $themeResourceJSLocalOption = null,
        ThemeResourceCSSLocalOption $themeResourceCSSLocalOption = null
    ) {
        if(!$filesystem) {
            $filesystem = FilesystemFactory::create();
        }

        if(!$fileSubPathMeta) {
            $fileSubPathMeta = new FileSubPathPostMeta();
        }

        if(!$themeResourceJSOption) {
            $themeResourceJSOption = new Options\ThemeResourceJSOption();
        }

        if(!$themeResourceCSSOption) {
            $themeResourceCSSOption = new Options\ThemeResourceCSSOption();
        }


        $queryJSON = WPQueryFactory::createThemeJSON($themeResourceJSOption->get());
        $queryCSS  = WPQueryFactory::createThemeCSS($themeResourceCSSOption->get());

        if(!$useLocalFilesOption) {
            $useLocalFilesOption = new UseLocalFilesOption();
        }

        if(!$themeResourceJSLocalOption) {
            $themeResourceJSLocalOption = new ThemeResourceJSLocalOption();
        }

        if(!$themeResourceCSSLocalOption) {
            $themeResourceCSSLocalOption = new ThemeResourceCSSLocalOption();
        }

        return new EditorConfigGenerator(
            $filesystem,
            Files::getPath(),
            Files::getUrl(),
            $queryJSON,
            $queryCSS,
            $fileSubPathMeta,
            $useLocalFilesOption,
            $themeResourceJSLocalOption,
            $themeResourceCSSLocalOption
        );
    }
}
