<?php
namespace Setka\Editor\Service\AMP;

class AnimationSanitizer extends \AMP_Base_Sanitizer
{
    /**
     * @var array Current animation config.
     */
    protected $animation;

    /**
     * @var array HTML attributes should be removed.
     */
    protected $attributesToRemove = array(
        'data-anim',
        'data-anim-delay',
        'data-anim-direction',
        'data-anim-duration',
        'data-anim-loop',
        'data-anim-opacity',
        'data-anim-rotation',
        'data-anim-shift',
        'data-anim-trigger',
        'data-anim-zoom',
        'data-anim-played',
    );

    /**
     * @var \DOMElement HTML <head>.
     */
    protected $headElement;

    /**
     * @var bool True if animations exists on the page.
     */
    protected $animationsExists = false;

    /**
     * @inheritdoc
     */
    public function sanitize()
    {
        try {
            $this->sanitizeHandler();
        } catch (\Exception $exception) {
            // Do nothing.
        }
    }

    /**
     * @see sanitize()
     */
    protected function sanitizeHandler()
    {
        /**
         * @var $nodes \DOMNodeList
         * @var $node \DOMElement
         * @var $commonAnimationStyles \DOMElement
         */
        $xpath             = new \DOMXPath($this->dom);
        $nodes             = $xpath->query('//*[@data-anim="true"]'); // All elements with data-anim=true.
        $this->headElement = $this->dom->getElementsByTagName('head')->item(0);

        foreach ($nodes as $key => $node) {
            $animation = $this->prepareAnimationConfig($node, $key);

            $cssClasses  = $node->getAttribute('class');
            $cssClasses .= ' stk-anim ' . $animation['id'];
            $node->setAttribute('class', $cssClasses);

            $id = $node->hasAttribute('id') ? $node->getAttribute('id') : 'target-' . $animation['id'];
            $node->setAttribute('id', $id);

            $this->removeAttributes($node);

            $this->animation = $animation;

            try {
                $positionObserver       = $this->createPositionObserver($id);
                $animationElement       = $this->createAnimationElement();
                $animationStylesElement = $this->createAnimationStylesElement($key);

                $this->root_element->appendChild($positionObserver);
                $this->root_element->appendChild($animationElement);
                $this->root_element->appendChild($animationStylesElement);
                $this->animationsExists = true;
            } catch (\Exception $exception) {
                // Silently skip this animation element.
            }
        }

        if($this->animationsExists) {
            $commonAnimationStyles = $this->dom->createElement('style');
            $commonAnimationStyles->setAttribute('type', 'text/css');
            $commonAnimationStyles->textContent = '.stk-post.stk-post .stk-anim.stk-anim { transform: translateX(var(--stk-shift-x, 0)) translateY(var(--stk-shift-y, 0)) rotate(var(--stk-rotation, 0)) scale(var(--stk-zoom, 1)); opacity: var(--stk-opacity, 1); }';
            $this->headElement->appendChild($commonAnimationStyles);
            $this->args['setka_amp_service']->setAnimations(true);
        }
    }

    /**
     * @param \DOMElement $node
     * @param int $number Index of node.
     */
    public function prepareAnimationConfig(\DOMElement $node, $number)
    {
        $animation = array();

        $animation['id']       = $this->generateUniqueClassForAnimation($number);
        $animation['selector'] = '.' . $animation['id'];
        $animation['duration'] = (float) $node->getAttribute('data-anim-duration') * 1000;
        $animation['delay']    = (float) $node->getAttribute('data-anim-delay') * 1000;

        $direction = $node->getAttribute('data-anim-direction');
        $shift     = (int) $node->getAttribute('data-anim-shift');

        if('bottom' === $direction || 'right' === $direction) {
            $shift = $shift * -1;
        }

        $shift = (string) $shift . 'px';

        if('top' === $direction || 'bottom' === $direction) {
            $animation['--stk-shift-y'] = $shift;
            $animation['--stk-shift-x'] = '0px';
        } else {
            $animation['--stk-shift-x'] = $shift;
            $animation['--stk-shift-y'] = '0px';
        }

        $animation['--stk-zoom']     = (float) $node->getAttribute('data-anim-zoom') / 100;
        $animation['--stk-rotation'] = (float) $node->getAttribute('data-anim-rotation') * -1 . 'deg';
        $animation['--stk-opacity']  = (float) $node->getAttribute('data-anim-opacity') / 100;

        $animation['keyframes'] = array(
            'transform' => array(
                'translateX(var(--stk-shift-x, 0)) translateY(var(--stk-shift-y, 0)) rotate(var(--stk-rotation, 0)) scale(var(--stk-zoom, 1))',
                'translate(0, 0) scale(1)'
            ),
            'opacity' => array('var(--stk-opacity, 1)', 1)
        );

        return $animation;
    }

    /**
     * @param \DOMElement $node
     *
     * @return $this For chain calls.
     */
    public function removeAttributes($node)
    {
        foreach ($this->attributesToRemove as $attribute) {
            $node->removeAttribute($attribute);
        }
        return $this;
    }

    /**
     * @param $id string Unique id of DOMElement which will be animated.
     *
     * @throws \RuntimeException If element was not created.
     *
     * @return \DOMElement New element.
     */
    public function createPositionObserver($id)
    {
        if(!isset($this->animation) || !isset($this->animation['id'])) {
            throw new \RuntimeException();
        }

        /**
         * @var $node \DOMElement
         */
        $node = $this->dom->createElement('amp-position-observer');
        $node->setAttribute('on', 'enter:' . $this->animation['id'] . '.start;');
        $node->setAttribute('intersection-ratios', '0 0.5');
        $node->setAttribute('layout', 'nodisplay');
        $node->setAttribute('target', $id);
        return $node;
    }

    /**
     * Generates unique CSS class for element.
     *
     * @param $index int Unique index of element.
     * @return string Unique CSS class for element.
     */
    public function generateUniqueClassForAnimation($index)
    {
        return 'stk-anim-' . absint($index);
    }

    /**
     * @throws \RuntimeException If element was not created.
     *
     * @return \DOMElement
     */
    public function createAnimationElement()
    {
        if(!isset($this->animation) || !isset($this->animation['id'])) {
            throw new \RuntimeException();
        }

        $config = array(
            'fill' => 'both',
            'easing' => 'ease',
            'iterations' => 1,
            'animations' => array($this->animation),
        );

        $json = wp_json_encode($config);

        if(is_string($json)) {
            /**
             * @var $node \DOMElement
             * @var $script \DOMElement
             */
            $node = $this->dom->createElement('amp-animation');
            $node->setAttribute('id', $this->animation['id']);
            $node->setAttribute('layout', 'nodisplay');

            $script = $this->dom->createElement('script');
            $script->setAttribute('type', 'application/json');
            $script->textContent = $json;

            $node->appendChild($script);

            return $node;
        }

        throw new \RuntimeException();
    }

    /**
     * Generates <style> element for single animation.
     *
     * @param $index string|int ID of this element on the page.
     *
     * @throws \RuntimeException
     *
     * @return \DOMElement
     */
    public function createAnimationStylesElement($index)
    {
        if(!isset($this->animation)) {
            throw new \RuntimeException();
        }

        $properties = array(
            '--stk-shift-y'  => null,
            '--stk-shift-x'  => null,
            '--stk-zoom'     => null,
            '--stk-rotation' => null,
            '--stk-opacity'  => null,
        );

        foreach ($properties as $name => $value) {
            if(isset($this->animation[$name])) {
                $properties[$name] = $this->animation[$name];
            } else {
                unset($properties[$name]);
            }
        }

        unset($name, $value);

        $css = '';

        foreach ($properties as $property => $value) {
            $css .= $property . ':' . $value . ';';
        }

        $css = sprintf(
            '.stk-anim.stk-anim-%s {%s}',
            $index,
            $css
        );

        /**
         * @var $node \DOMElement
         */
        $node = $this->dom->createElement('style');
        $node->setAttribute('type', 'text/css');
        $node->textContent = $css;

        return $node;
    }
}
