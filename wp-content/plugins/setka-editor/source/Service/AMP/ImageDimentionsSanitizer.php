<?php
namespace Setka\Editor\Service\AMP;

use Setka\Editor\Plugin;

class ImageDimentionsSanitizer extends \AMP_Base_Sanitizer
{
    /**
     * @var \DOMElement Current image.
     */
    protected $currentImg;

    /**
     * @var int Id of current image (attachment).
     */
    protected $currentImgId;

    /**
     * @inheritdoc
     */
    public function sanitize()
    {
        /**
         * @var \DOMNodeList $nodes
         */
        $nodes = $this->dom->getElementsByTagName('img');

        foreach ($nodes as $node) {
            if (!is_a($node, \DOMElement::class)) {
                continue;
            }

            $this
                ->setCurrentImg($node)
                ->detectCurrentImageId()
                ->removeStkReset()
                ->widthAndHeightAttributes()
                ->srcSet();
        }
    }

    /**
     * Detect current img id.
     *
     * @return $this
     */
    public function detectCurrentImageId()
    {
        $idAttr = $this->getCurrentImg()->getAttribute('id');

        $id = filter_var($idAttr, FILTER_SANITIZE_NUMBER_INT);

        if(is_string($id) && !empty($id)) {
            $this->currentImgId = absint($id);
        }

        return $this;
    }

    /**
     * Remove stk-reset CSS class.
     *
     * @return $this
     */
    public function removeStkReset()
    {
        $node    = $this->currentImg;
        $classes = trim($node->getAttribute('class'));
        if (!empty($classes)) {
            $classes = explode(' ', $classes);

            if (!empty($classes)) {
                $index = array_search('stk-reset', $classes, true);

                if (false !== $index) {
                    unset($classes[$index]);

                    if (empty($classes)) {
                        $node->removeAttribute('class');
                    } else {
                        $node->setAttribute('class', implode(' ', $classes));
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Add width and height attributes for img.
     *
     * @return $this
     */
    public function widthAndHeightAttributes()
    {
        $node = $this->currentImg;

        if (!$this->currentImgId || $node->hasAttribute('width') || $node->hasAttribute('height')) {
            return $this;
        }

        $meta = wp_get_attachment_metadata($this->currentImgId);

        if(isset($meta['width']) && isset($meta['height'])) {
            $node->setAttribute('width', $meta['width']);
            $node->setAttribute('height', $meta['height']);
        }

        return $this;
    }

    /**
     * Add srcset attribute for img.
     *
     * @return $this
     */
    public function srcSet()
    {
        $node = $this->currentImg;

        if($node->hasAttribute('srcset') || !$this->currentImgId) {
            return $this;
        }

        $srcSet = wp_get_attachment_image_srcset($this->currentImgId, Plugin::NAME . '-1000');

        if(is_string($srcSet)) {
            $node->setAttribute('srcset', $srcSet);
        }

        return $this;
    }

    /**
     * @return \DOMElement
     */
    public function getCurrentImg()
    {
        return $this->currentImg;
    }

    /**
     * @param \DOMElement $currentImg
     * @return $this
     */
    public function setCurrentImg(\DOMElement $currentImg)
    {
        $this->currentImg   = $currentImg;
        $this->currentImgId = null;
        return $this;
    }
}
