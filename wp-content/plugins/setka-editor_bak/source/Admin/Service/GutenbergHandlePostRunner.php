<?php
namespace Setka\Editor\Admin\Service;

use Korobochkin\WPKit\Runners\RunnerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GutenbergHandlePostRunner implements RunnerInterface
{
    /**
     * @var ContainerInterface Container with services.
     */
    protected static $container;

    /**
     * Returns the ContainerBuilder with services.
     *
     * @return ContainerInterface Container with services.
     */
    public static function getContainer()
    {
        return self::$container;
    }

    /**
     * Sets the ContainerBuilder with services.
     *
     * @param ContainerInterface $container Container with services.
     */
    public static function setContainer(ContainerInterface $container = null)
    {
        self::$container = $container;
    }

    /**
     * @inheritdoc
     */
    public static function run()
    {
    }

    /**
     * Fires once a post has been saved.
     *
     * @param int $postId Post ID.
     * @param \WP_Post $post Post object.
     * @param bool $update Whether this is an existing post being updated or not.
     */
    public static function runSave($postId, $post, $update)
    {
        try {
            self::getContainer()->get(GutenbergHandlePost::class)->save($postId, $post, $update);
        } catch (\Exception $exception) {
            // Do nothing.
        }
    }

    /**
     * @param \WP_REST_Response $response The response object.
     * @param \WP_Post          $post     Post object.
     * @param \WP_REST_Request  $request  Request object.
     *
     * @return \WP_REST_Response
     */
    public static function maybeConvertClassicEditorPost($response, $post, $request)
    {
        return self::getContainer()->get(GutenbergHandlePost::class)->maybeConvertClassicEditorPost($response, $post, $request);
    }
}
