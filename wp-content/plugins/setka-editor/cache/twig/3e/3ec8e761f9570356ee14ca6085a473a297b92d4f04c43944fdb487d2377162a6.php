<?php

/* admin/settings/common/page-title.html.twig */
class __TwigTemplate_7abe59f06a2cf79fc12e6c705e2549fd19754cd94254603448de160085214fd1 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'wordpress_page_title' => array($this, 'block_wordpress_page_title'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        $this->displayBlock('wordpress_page_title', $context, $blocks);
    }

    public function block_wordpress_page_title($context, array $blocks = array())
    {
        // line 2
        if ($this->getAttribute(($context["page"] ?? null), "getPageTitle", array())) {
            // line 3
            echo "<h2>";
            echo twig_escape_filter($this->env, $this->getAttribute(($context["page"] ?? null), "getPageTitle", array()), "html", null, true);
            echo "</h2>";
        }
    }

    public function getTemplateName()
    {
        return "admin/settings/common/page-title.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  28 => 3,  26 => 2,  20 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "admin/settings/common/page-title.html.twig", "/opt/bitnami/apps/wordpress/htdocs/wp-content/plugins/setka-editor/twig-templates/admin/settings/common/page-title.html.twig");
    }
}
