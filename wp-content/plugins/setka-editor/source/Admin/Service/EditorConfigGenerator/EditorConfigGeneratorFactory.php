<?php
namespace Setka\Editor\Admin\Service\EditorConfigGenerator;

use Setka\Editor\Admin\Options;
use Setka\Editor\Admin\Options\ThemeResourceCSSLocal\ThemeResourceCSSLocalOption;
use Setka\Editor\Admin\Options\ThemeResourceJSLocal\ThemeResourceJSLocalOption;
use Setka\Editor\Admin\Options\Files\UseLocalFilesOption;
use Setka\Editor\Admin\Service\FilesSync\Filesystem;
use Setka\Editor\Admin\Service\FilesSync\FilesystemFactory;
use Setka\Editor\Admin\Service\WPQueryFactory;
use Setka\Editor\Entries\Meta\FileSubPathMeta;
use Setka\Editor\Service\Config\Files;

class EditorConfigGeneratorFactory
{

    public static function create(
        Filesystem $filesystem = null,
        FileSubPathMeta $fileSubPathMeta = null,
        Options\ThemeResourceJS\Option $themeResourceJSOption = null,
        Options\ThemeResourceCSS\Option $themeResourceCSSOption = null,
        UseLocalFilesOption $useLocalFilesOption = null,
        ThemeResourceJSLocalOption $themeResourceJSLocalOption = null,
        ThemeResourceCSSLocalOption $themeResourceCSSLocalOption = null
    ) {
        if(!$filesystem) {
            $filesystem = FilesystemFactory::create();
        }

        if(!$fileSubPathMeta) {
            $fileSubPathMeta = new FileSubPathMeta();
        }

        if(!$themeResourceJSOption) {
            $themeResourceJSOption = new Options\ThemeResourceJS\Option();
        }

        if(!$themeResourceCSSOption) {
            $themeResourceCSSOption = new Options\ThemeResourceCSS\Option();
        }


        $queryJSON = WPQueryFactory::createThemeJSON($themeResourceJSOption->getValue());
        $queryCSS  = WPQueryFactory::createThemeCSS($themeResourceCSSOption->getValue());

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
