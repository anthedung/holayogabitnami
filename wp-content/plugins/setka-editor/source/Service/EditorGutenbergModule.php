<?php
namespace Setka\Editor\Service;

use Setka\Editor\PostMetas\PostLayoutPostMeta;
use Setka\Editor\PostMetas\PostThemePostMeta;
use Setka\Editor\PostMetas\TypeKitIDPostMeta;
use Setka\Editor\PostMetas\UseEditorPostMeta;

class EditorGutenbergModule
{
    /**
     * @var ScriptStyles
     */
    protected $scriptStyles;

    /**
     * @var DataFactory
     */
    protected $dataFactory;

    /**
     * EditorGutenbergModule constructor.
     * @param ScriptStyles $scriptStyles
     */
    public function __construct(ScriptStyles $scriptStyles, DataFactory $dataFactory)
    {
        $this->scriptStyles = $scriptStyles;
        $this->dataFactory  = $dataFactory;
    }

    /**
     * Setup additional things for Setka Editor posts.
     *
     * @param $attributes array Additional configuration for Setka Editor post.
     * @param $content string Post content.
     * @return string Post content. without any changes.
     */
    public function render(array $attributes, $content)
    {
        if(isset($attributes['setkaEditorTypeKitId']) && is_string($attributes['setkaEditorTypeKitId'])) {
            $this->scriptStyles->addTypeKitId($attributes['setkaEditorTypeKitId']);
        }

        if(isset($attributes['setkaEditorTheme']) && is_string($attributes['setkaEditorTheme'])) {
            $this->scriptStyles->getAmp()->requireTheme($attributes['setkaEditorTheme']);
        }

        if(isset($attributes['setkaEditorLayout']) && is_string($attributes['setkaEditorLayout'])) {
            $this->scriptStyles->getAmp()->requireLayout($attributes['setkaEditorLayout']);
        }

        return $content;
    }

    /**
     * @param \WP_Post $post
     *
     * @throws \UnexpectedValueException If post not created in Setka Editor or already created in Gutenberg.
     * @throws \RuntimeException If JSON decode error occurs.
     *
     * @return array Post created in Setka Editor (TinyMCE integration) prepared for Gutenberg.
     */
    public function unRenderFromClassicEditor(\WP_Post $post)
    {
        /**
         * @var $meta UseEditorPostMeta
         */
        $meta = $this->dataFactory->create(UseEditorPostMeta::class);

        if(!$meta->setPostId($post->ID)->get()) {
            throw new \UnexpectedValueException();
        }

        $hasBlocks = has_blocks($post);

        if($hasBlocks) {
            throw new \UnexpectedValueException();
        }

        /**
         * @var $layoutMeta PostLayoutPostMeta
         * @var $themeMeta PostThemePostMeta
         * @var $typeKitMeta TypeKitIDPostMeta
         */
        $typeKitMeta = $this->dataFactory->create(TypeKitIDPostMeta::class);
        $layoutMeta  = $this->dataFactory->create(PostLayoutPostMeta::class);
        $themeMeta   = $this->dataFactory->create(PostThemePostMeta::class);

        $attributes = array(
            'setkaEditorTypeKitId' => $typeKitMeta->setPostId($post->ID)->get(),
            'setkaEditorLayout' => $layoutMeta->setPostId($post->ID)->get(),
            'setkaEditorTheme' => $themeMeta->setPostId($post->ID)->get(),
        );

        $attributesString = wp_json_encode($attributes);

        if(!is_string($attributesString)) {
            throw new \RuntimeException();
        }

        return array(
            'raw' => sprintf(
                '<!-- wp:setka-editor/setka-editor %s --><div class="alignfull">%s</div><!-- /wp:setka-editor/setka-editor -->',
                $attributesString,
                $post->post_content
            ),
            'rendered' => sprintf('<div class="alignfull">%s</div>', $post->post_content),
        );
    }
}
