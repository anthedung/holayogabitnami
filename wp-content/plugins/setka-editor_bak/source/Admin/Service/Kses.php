<?php
namespace Setka\Editor\Admin\Service;

use Setka\Editor\Admin\User\Capabilities\UseEditorCapability;

/**
 * Class Kses adds custom data attributes as allowed.
 *
 * IMPORTANT: call $this->allowedHTML() method only if WordPress users methods already loaded to prevent fatal errors.
 */
class Kses
{
    /**
     * @var array List of required HTML tags for Setka Editor.
     */
    protected $tags = array(
        'p',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'a',
        'b',
        'strong',
        'i',
        'em',
        'u',
        'del',
        'sup',
        'div',
        'span',
        'figure',
        'figcaption',
        'code',
        'img',
        'hr',
        'style',
    );

    /**
     * @var array List of HTML tag attributes for Setka Editor.
     */
    protected $attributes = array(
        'data-ce-tag'           => true,
        'data-col-width'        => true,
        'data-ui-id'            => true,
        'data-editor-version'   => true,
        'data-reset-type'       => true,
        'data-layout'           => true,
        'data-anim-type'        => true,
        'data-anim-name'        => true,
        'data-anim-hash'        => true,
        'data-anim'             => true,
        'data-anim-direction'   => true,
        'data-anim-zoom'        => true,
        'data-anim-shift'       => true,
        'data-anim-rotation'    => true,
        'data-anim-opacity'     => true,
        'data-anim-duration'    => true,
        'data-anim-delay'       => true,
        'data-anim-trigger'     => true,
        'data-anim-loop'        => true,
        'data-embed-mode'       => true,
        'data-embed-link'       => true,
        'data-embed-responsive' => true,
        'style'                 => true,
    );

    /**
     * @var array Received HTML tags.
     */
    protected $incomeTags;

    /**
     * Setka Editor requires additional data-attributes and tags in HTML markup for posts.
     * We just add it to current WordPress list.
     *
     * @see current_user_can()
     *
     * @param $allowedPostTags array The list of html tags and their attributes.
     * @param $context string The name of context.
     *
     * @return array Array with required tags and attributes for Setka Editor.
     */
    public function allowedHTML($allowedPostTags, $context)
    {
        if('post' === $context && current_user_can(UseEditorCapability::NAME)) {
            $this->incomeTags =& $allowedPostTags;
            $this->addRequiredTagsAndAttributes();
            return $this->incomeTags;
        }
        return $allowedPostTags;
    }

    /**
     * Adds required tags and attributes.
     *
     * @return $this
     */
    public function addRequiredTagsAndAttributes()
    {
        foreach($this->tags as $tag) {
            if(isset($this->incomeTags[$tag])) {
                $this->incomeTags[$tag] = array_merge($this->incomeTags[$tag], $this->attributes);
            } else {
                $this->incomeTags[$tag] = $this->attributes;
            }
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     * @return $this
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }
}
