<?php
namespace Setka\Editor\Admin\Prototypes\Notices;

//use Setka\Editor\Admin\Notices\Prototypes\View;

class Notice implements NoticeInterface
{

    /**
     * @var Attributes
     */
    protected $attributes;

    /**
     * @var \Setka\Editor\Admin\Prototypes\Notices\Views\Notice
     */
    protected $view;

    /**
     * @var string Notice prefix.
     */
    protected $prefix = '';

    /**
     * @var string string Notice unique name.
     */
    protected $name = '';

    /**
     * @var string HTML content of notice.
     */
    protected $content = '';

    /**
     * @var RelevantStorageInterface
     */
    protected $relevantStorage;

    public function __construct($prefix, $name)
    {
        $this->setPrefix($prefix);
        $this->setName($name);
    }

    public function lateConstruct()
    {
        $this->attributes = new Attributes();
        $this->view       = new Views\Notice();
    }

    public function setPrefix($prefix)
    {
        $this->prefix = (string) $prefix;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function setName($name)
    {
        $this->name = (string) $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPrefixedName()
    {
        return $this->getDismissUrlArg();
    }

    public function setAttribute($key, $value)
    {
        $this->attributes->setAttribute($key, $value);
    }

    public function getAttribute($key)
    {
        return $this->attributes->getAttribute($key);
    }

    public function getAllAttributes()
    {
        return $this->attributes->getAllAttributes();
    }

    public function addClass($class)
    {
        $this->attributes->addClass($class);
        return $this;
    }

    public function removeClass($class)
    {
        $this->attributes->removeClass($class);
    }

    public function setClasses(array $classes)
    {
        $this->attributes->setClasses($classes);
    }

    public function getClasses()
    {
        return $this->attributes->getClasses();
    }

    /**
     * Dismiss managed by simply adding class to full control under notice via setClasses(), etc.
     */
    public function addDismissible()
    {
        $this->attributes->addClass('is-dismissible');
    }

    public function removeDismissible()
    {
        $this->attributes->removeClass('is-dismissible');
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getRelevantStorage()
    {
        return $this->relevantStorage;
    }

    public function setRelevantStorage(RelevantStorageInterface $relevantStorage)
    {
        $this->relevantStorage = $relevantStorage;
    }

    public function hasRelevantStorage()
    {
        if(is_a($this->relevantStorage, '\Setka\Editor\Admin\Prototypes\Notices\RelevantStorageInterface')) {
            return true;
        }
        return false;
    }

    public function getView()
    {
        return $this->view;
    }

    public function render()
    {
        $view = $this->getView();
        echo $view->render($this); // WPCS: XSS ok.
    }

    public function handleRequest()
    {
    }

    public function isRelevant()
    {
        if($this->hasRelevantStorage()) {
            return $this->isRelevantAccordingToTheStorage();
        }
        return true;
    }

    public function isRelevantAccordingToTheStorage()
    {
        if($this->hasRelevantStorage()) {
            return $this->getRelevantStorage()->isRelevant($this);
        }
        return false;
    }

    public function dismiss()
    {
    }

    public function getDismissUrlArg()
    {
        return $this->getPrefix() . '-notice-' . $this->getName();
    }

    public function getDismissUrl()
    {
        return add_query_arg($this->getDismissUrlArg(), '0');
    }

    public function redirectAfterDismiss()
    {
        global $wp;
        $current_url = add_query_arg($wp->query_string, '', admin_url($wp->request));
        $current_url = remove_query_arg($this->getDismissUrlArg(), $current_url);
        wp_safe_redirect($current_url);
    }
}
