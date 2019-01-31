<?php
namespace Setka\Editor\Service\AMP;

use Setka\Editor\Admin\Options\AMP\AMPCssOption;
use Setka\Editor\Admin\Options\AMP\AMPFontsOption;
use Setka\Editor\Admin\Options\AMP\AMPStylesOption;
use Setka\Editor\Admin\Options\AMP\UseAMPStylesOption;
use Setka\Editor\Plugin;
use Setka\Editor\PostMetas\PostLayoutPostMeta;
use Setka\Editor\PostMetas\PostThemePostMeta;
use Setka\Editor\PostMetas\UseEditorPostMeta;
use Setka\Editor\Service\PostStatuses;
use Setka\Editor\Service\SetkaPostTypes;

/**
 * Class AMP makes modifications for AMP pages.
 */
class AMP
{
    /**
     * @var bool True if AMP plugin active.
     */
    protected $ampSupport = false;

    /**
     * @var string Name of AMP mode (classic, native, paired).
     */
    protected $mode;

    /**
     * @var AMPCssOption CSS for AMP pages.
     */
    protected $ampCssOption;

    /**
     * @var AMPFontsOption Font urls for AMP pages.
     */
    protected $ampFontsOption;

    /**
     * @var AMPStylesOption
     */
    protected $ampStylesOption;

    /**
     * @var UseAMPStylesOption
     */
    protected $useAMPStylesOption;

    /**
     * @var array List of required themes.
     */
    protected $requiredThemes = array();

    /**
     * @var array List of required layouts
     */
    protected $requiredLayouts = array();

    /**
     * @var string CSS styles for current page.
     */
    protected $css;

    /**
     * @var array List of URL of fonts for current page.
     */
    protected $fonts = array();

    /**
     * @var UseEditorPostMeta
     */
    protected $useEditorPostMeta;

    /**
     * @var PostThemePostMeta
     */
    protected $postThemePostMeta;

    /**
     * @var PostLayoutPostMeta
     */
    protected $postLayoutPostMeta;

    /**
     * @var bool True if we should add animation JS components in classic mode.
     */
    protected $animations = false;

    /**
     * AMP constructor.
     * @param bool $ampSupport
     * @param string $mode
     * @param AMPCssOption $ampCssOption
     * @param AMPFontsOption $ampFontsOption
     * @param AMPStylesOption $ampStylesOption
     * @param UseAMPStylesOption $useAMPStylesOption
     * @param UseEditorPostMeta $useEditorPostMeta
     * @param PostThemePostMeta $postThemePostMeta
     * @param PostLayoutPostMeta $postLayoutPostMeta
     */
    public function __construct(
        $ampSupport,
        $mode,
        AMPCssOption $ampCssOption,
        AMPFontsOption $ampFontsOption,
        AMPStylesOption $ampStylesOption,
        UseAMPStylesOption $useAMPStylesOption,
        UseEditorPostMeta $useEditorPostMeta,
        PostThemePostMeta $postThemePostMeta,
        PostLayoutPostMeta $postLayoutPostMeta
    ) {
        $this->ampSupport = $ampSupport;
        $this->mode       = $mode;

        $this->ampCssOption       = $ampCssOption;
        $this->ampFontsOption     = $ampFontsOption;
        $this->ampStylesOption    = $ampStylesOption;
        $this->useAMPStylesOption = $useAMPStylesOption;

        $this->useEditorPostMeta  = $useEditorPostMeta;
        $this->postThemePostMeta  = $postThemePostMeta;
        $this->postLayoutPostMeta = $postLayoutPostMeta;
    }

    /**
     * Modify config for AMP template.
     *
     * Classic mode.
     *
     * @param array $data Config for AMP template.
     * @param \WP_Post $post WordPress post object.
     *
     * @return array Modified data.
     */
    public function classicTemplateData(array $data, \WP_Post $post)
    {
        return $this
            ->setupThemeAndLayoutFromPostMeta($post)
            ->setupFonts()
            ->updateData($data, $post);
    }

    /**
     * Return CSS for post.
     *
     * Classic mode.
     *
     * @param \WP_Post $post WordPress post object.
     *
     * @return string CSS styles for post.
     */
    public function classicTemplateCss(\WP_Post $post)
    {
        return $this
            ->setupThemeAndLayoutFromPostMeta($post)
            ->setupCSS()
            ->getCss();
    }

    /**
     * Add custom fonts on AMP pages and require animations.
     *
     * Classic mode.
     *
     * @param $data array \AMP_Post_Template config.
     * @param $post \WP_Post WordPress post object.
     *
     * @return array Modified data.
     */
    public function updateData(array $data, \WP_Post $post)
    {
        if ($this->fonts) {
            $data['font_urls'] = array_merge($data['font_urls'], $this->fonts);
        }

        if ($this->animations) {
            $data['amp_component_scripts']['amp-animation']         = true;
            $data['amp_component_scripts']['amp-position-observer'] = true;
        }

        return $data;
    }

    /**
     * @param \WP_Post $post Post which theme and layout will be used.
     * @return $this For chain calls.
     */
    public function setupThemeAndLayoutFromPostMeta(\WP_Post $post)
    {
        $this->useEditorPostMeta->setPostId($post->ID)->deleteLocal();

        if(!$this->useEditorPostMeta->get()) {
            return $this;
        }

        $this->postThemePostMeta->setPostId($post->ID)->deleteLocal();
        $this->postLayoutPostMeta->setPostId($post->ID)->deleteLocal();

        $theme  = $this->postThemePostMeta->get();
        $layout = $this->postLayoutPostMeta->get();

        if($theme) {
            $this->requireTheme($theme);
        }

        if($layout) {
            $this->requireLayout($layout);
        }

        return $this;
    }

    /**
     * Setup fonts from all sources.
     *
     * @return $this For chain calls.
     */
    public function setupFonts()
    {
        if(!$this->useAMPStylesOption->get()) {
            return $this;
        }

        if(!empty($this->fonts)) {
            $this->fonts = array();
        }

        $this->setupFontsFromOption()->setupFontsForThemes();

        return $this;
    }

    /**
     * @return $this
     */
    public function setupFontsFromOption()
    {
        foreach ($this->ampFontsOption->get() as $key => $url) {
            $this->fonts[Plugin::NAME . '-' . $key] = $url;
        }
        return $this;
    }

    /**
     * Setup all fonts for current themes.
     *
     * @return $this For chain calls.
     */
    public function setupFontsForThemes()
    {
        $sections = $this->ampStylesOption->get();
        foreach ($this->requiredThemes as $id => $value) {
            $id = (string) $id;
            foreach ($sections['themes'] as $themeConfig) {
                if($themeConfig['id'] !== $id) {
                    continue;
                }

                if(isset($themeConfig['fonts'])) {
                    foreach ($themeConfig['fonts'] as $key => $url) {
                        $this->fonts[Plugin::NAME . '-' . $id . '-' . $key] = $url;
                    }
                }

                break;
            }
        }
        return $this;
    }

    /**
     * Setup CSS for used themes and layouts.
     *
     * @return $this For chain calls.
     */
    public function setupCSS()
    {
        if(!$this->useAMPStylesOption->get()) {
            return $this;
        }

        $this->css = $this->ampCssOption->get();

        $this->setupCssForThemeAndLayout();

        return $this;
    }

    /**
     * Generate CSS for given post (common + theme + layout).
     *
     * @return $this For chain calls.
     */
    protected function setupCssForThemeAndLayout()
    {
        $configuration = $this->ampStylesOption->get();
        $ids           = array();

        $section = 'common';
        if(isset($configuration[$section])) {
            $this->iterateSection($ids, $configuration, $section);
        }

        $section = 'themes';
        if(isset($configuration[$section])) {
            $this->iterateSection($ids, $configuration, $section, $this->requiredThemes);
        }

        $section = 'layouts';
        if(isset($configuration[$section])) {
            $this->iterateSection($ids, $configuration, $section, $this->requiredLayouts);
        }

        unset($configuration, $section);

        if(empty($ids)) {
            return $this;
        }

        $query = new \WP_Query(array(
            'post_type' => array(SetkaPostTypes::AMP_COMMON, SetkaPostTypes::AMP_THEME, SetkaPostTypes::AMP_LAYOUT),
            'post_status' => PostStatuses::ANY,
            'post__in' => $ids,
            'orderby' => 'post__in',
            'posts_per_page' => count($ids),
        ));

        if($query->have_posts()) {
            foreach ($query->posts as $post) {
                $this->css .= $post->post_content;
            }
        }

        return $this;
    }

    /**
     * Find WordPress posts IDs for given CSS section.
     *
     * @param $ids array Founded ids will be added to this array.
     * @param $configuration array CSS files list.
     * @param $section string Name of the required section.
     * @param $required array|null List of required themes or layouts.
     *
     * @return $this For chain calls.
     */
    protected function iterateSection(array &$ids, array &$configuration, &$section, &$required = null)
    {
        foreach ($configuration[$section] as $file) {
            if(is_array($required)) {
                if(isset($required[$file['id']])) {
                    $ids[] = $file['wp_id'];
                }
            } else {
                $ids[] = $file['wp_id'];
            }
        }
        return $this;
    }

    /**
     * AMP plugin enabled or disabled.
     *
     * @return bool True if AMP plugin active.
     */
    public function isAmpSupport()
    {
        return $this->ampSupport;
    }

    /**
     * Set AMP plugin enabled or disabled.
     *
     * @param bool $ampSupport AMP plugin status.
     * @return $this For chain calls.
     */
    public function setAmpSupport($ampSupport)
    {
        $this->ampSupport = $ampSupport;
        return $this;
    }

    /**
     * Return one of three allowed AMP plugin mode.
     *
     * Allowed mode names: classic, paired, native.
     *
     * @return string AMP plugin mode.
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Set current AMP plugin mode.
     *
     * @param string $mode AMP plugin mode name.
     * @return $this For chain calls.
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * @param $theme string
     * @return $this
     */
    public function requireTheme($theme)
    {
        $this->requiredThemes[$theme] = true;
        return $this;
    }

    /**
     * @param $layout string
     * @return $this
     */
    public function requireLayout($layout)
    {
        $this->requiredLayouts[$layout] = true;
        return $this;
    }

    /**
     * Return CSS for <style amp-custom> section.
     *
     * @return string Post theme and layout styles.
     */
    public function getCss()
    {
        return $this->css;
    }

    /**
     * Return fonts.
     *
     * @return array List of required fonts.
     */
    public function getFonts()
    {
        return $this->fonts;
    }

    /**
     * Return true if Setka animations exists on the page.
     *
     * @return bool True if Setka animations exists on the page.
     */
    public function hasAnimations()
    {
        return $this->animations;
    }

    /**
     * Set animation status (existence) on current page.
     *
     * @param bool $animations Animations status.
     * @return $this For chain calls.
     */
    public function setAnimations($animations)
    {
        $this->animations = $animations;
        return $this;
    }
}
