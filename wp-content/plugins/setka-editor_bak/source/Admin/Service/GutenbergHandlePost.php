<?php
namespace Setka\Editor\Admin\Service;

use Korobochkin\WPKit\PostMeta\PostMetaInterface;
use Setka\Editor\PostMetas\UseEditorPostMeta;
use Setka\Editor\Service\DataFactory;
use Setka\Editor\Service\EditorGutenbergModule;

class GutenbergHandlePost
{
    /**
     * @var DataFactory
     */
    protected $dataFactory;

    /**
     * @var \WP_Screen
     */
    protected $screen;

    /**
     * @var EditorGutenbergModule
     */
    protected $editorGutenbergModule;

    /**
     * GutenbergHandlePost constructor.
     * @param DataFactory $dataFactory
     * @param \WP_Screen $screen
     * @param $editorGutenbergModule EditorGutenbergModule
     */
    public function __construct(DataFactory $dataFactory, $screen, $editorGutenbergModule)
    {
        $this->dataFactory           = $dataFactory;
        $this->screen                = $screen;
        $this->editorGutenbergModule = $editorGutenbergModule;
    }

    /**
     * Fires once a post has been saved.
     *
     * @param int $postId Post ID.
     * @param \WP_Post $post Post object.
     * @param bool $update Whether this is an existing post being updated or not.
     *
     * @throws \Exception If Gutenberg not activated.
     *
     * @return $this For chain calls.
     */
    public function save($postId, $post, $update)
    {
        $postBlocks = parse_blocks($post->post_content);

        $setkaEditorExists = false;

        foreach ($postBlocks as $postBlock)
        {
            if(isset($postBlock['blockName']) && 'setka-editor/setka-editor' === $postBlock['blockName']) {
                $setkaEditorExists = true;
                break;
            }
        }

        /**
         * @var $meta PostMetaInterface
         */
        $meta = $this->dataFactory->create(UseEditorPostMeta::class);
        $meta->setPostId($postId);

        if($setkaEditorExists) {
            $meta->updateValue(true);
        } else {
            $meta->delete();
        }

        return $this;
    }

    /**
     * @param \WP_REST_Response $response The response object.
     * @param \WP_Post          $post     Post object.
     * @param \WP_REST_Request  $request  Request object.
     *
     * @return \WP_REST_Response
     */
    public function maybeConvertClassicEditorPost($response, $post, $request)
    {
        if(!$this->screen->is_block_editor()) {
            return $response;
        }

        try {
            $unRendered = $this->editorGutenbergModule->unRenderFromClassicEditor($post);

            $data                             = $response->get_data();
            $data['content']['raw']           = $unRendered['raw'];
            $data['content']['rendered']      = $unRendered['rendered'];
            $data['content']['block_version'] = 1;

            $response->set_data($data);
        } catch (\Exception $exception) {
            return $response;
        }

        return $response;
    }
}
