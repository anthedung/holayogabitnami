<?php

/* admin/settings/common/page-tabs.html.twig */
class __TwigTemplate_d2e70b12580da66b3f5f3c41c15b12711cf0bd0d4435d25be188830044053179 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'wordpress_page_tabs' => array($this, 'block_wordpress_page_tabs'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        $this->displayBlock('wordpress_page_tabs', $context, $blocks);
    }

    public function block_wordpress_page_tabs($context, array $blocks = array())
    {
        // line 2
        if ($this->getAttribute(($context["page"] ?? null), "getTabs", array())) {
            // line 3
            echo "<h2 class=\"nav-tab-wrapper wp-clearfix\">";
            // line 4
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["page"] ?? null), "getTabs", array()));
            foreach ($context['_seq'] as $context["_key"] => $context["tab"]) {
                // line 5
                echo "<a href=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["tab"], "getUrl", array()), "html", null, true);
                echo "\" id=\"\" class=\"nav-tab";
                echo (($this->getAttribute($context["tab"], "isActive", array())) ? (" nav-tab-active") : (""));
                echo "\">";
                echo twig_escape_filter($this->env, $this->getAttribute($context["tab"], "getTitle", array()), "html", null, true);
                echo "</a>";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tab'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 7
            echo "</h2>";
        }
    }

    public function getTemplateName()
    {
        return "admin/settings/common/page-tabs.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  46 => 7,  34 => 5,  30 => 4,  28 => 3,  26 => 2,  20 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "admin/settings/common/page-tabs.html.twig", "/opt/bitnami/apps/wordpress/htdocs/wp-content/plugins/setka-editor/twig-templates/admin/settings/common/page-tabs.html.twig");
    }
}
