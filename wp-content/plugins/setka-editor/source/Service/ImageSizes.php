<?php
namespace Setka\Editor\Service;

use Setka\Editor\Plugin;

class ImageSizes
{
    /**
     * @var array List of sizes.
     */
    protected $sizes = array(
        array(500,  0, false),
        array(1000, 0, false),
        array(1500, 0, false),
        array(2000, 0, false),
        array(2500, 0, false),
    );

    /**
     * Register additional image sizes.
     *
     * @return $this For chain calls.
     */
    public function register()
    {
        foreach ($this->sizes as $size) {
            add_image_size(Plugin::NAME . '-' . $size[0], $size[0], $size[1], $size[2]);
        }
        return $this;
    }

    /**
     * Return list of sizes.
     *
     * @return array List of sizes.
     */
    public function getSizes()
    {
        return $this->sizes;
    }

    /**
     * Set new list of sizes.
     *
     * @param array $sizes List of sizes.
     * @return $this For chain calls.
     */
    public function setSizes(array $sizes)
    {
        $this->sizes = $sizes;
        return $this;
    }
}
