<?php

/* admin/settings/setka-editor/page.html.twig */
class __TwigTemplate_35147501528f1bc52ee58a3e854731f385acd14b48d25729ac834307ef4f49db extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<div class=\"wrap\">
    ";
        // line 2
        $this->loadTemplate("admin/settings/common/page-title.html.twig", "admin/settings/setka-editor/page.html.twig", 2)->display($context);
        // line 3
        echo "
    ";
        // line 4
        $this->loadTemplate("admin/settings/common/page-tabs.html.twig", "admin/settings/setka-editor/page.html.twig", 4)->display($context);
        // line 5
        echo "
    ";
        // line 6
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["form"] ?? null), 'form_start');
        echo "

    ";
        // line 8
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock(($context["form"] ?? null), 'errors');
        echo "

    <p class=\"setka-editor-intro-message\">";
        // line 10
        echo twig_escape_filter($this->env, $this->getAttribute(($context["translations"] ?? null), "start", array()), "html", null, true);
        echo "</p>

    <div id=\"";
        // line 12
        echo twig_escape_filter($this->env, ($this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "accountType", array()), "vars", array()), "id", array()) . "_wrapper"), "html", null, true);
        echo "\">
        <ul class=\"setka-editor-inline-list\">
            ";
        // line 14
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["form"] ?? null), "accountType", array()));
        foreach ($context['_seq'] as $context["_key"] => $context["type"]) {
            // line 15
            echo "                <li>";
            // line 16
            echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($context["type"], 'widget');
            // line 17
            echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($context["type"], 'label');
            // line 18
            echo "</li>

            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['type'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 21
        echo "        </ul>
        ";
        // line 22
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["form"] ?? null), "accountType", array()));
        foreach ($context['_seq'] as $context["_key"] => $context["type"]) {
            // line 23
            echo "            ";
            if ( !$this->getAttribute($this->getAttribute($context["type"], "vars", array()), "valid", array())) {
                // line 24
                echo "                <div class=\"setka-editor-form-errors\">";
                // line 25
                echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($context["type"], 'errors');
                // line 26
                echo "</div>
            ";
            }
            // line 28
            echo "        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['type'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 29
        echo "    </div>

    <table class=\"form-table setka-editor-form-table\">
        <tbody>
            <tr id=\"";
        // line 33
        echo twig_escape_filter($this->env, ($this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "email", array()), "vars", array()), "id", array()) . "_wrapper"), "html", null, true);
        echo "\">
                <th scope=\"row\">
                    ";
        // line 35
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "email", array()), 'label');
        echo "
                    <span class=\"setka-editor-sub-caption\">";
        // line 36
        echo twig_escape_filter($this->env, $this->getAttribute(($context["translations"] ?? null), "email_sub_label", array()), "html", null, true);
        echo "</span>
                </th>
                <td>
                    ";
        // line 39
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "email", array()), 'widget');
        echo "
                    ";
        // line 40
        if ( !$this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "email", array()), "vars", array()), "valid", array())) {
            // line 41
            echo "                        <div class=\"setka-editor-form-errors\">
                            ";
            // line 42
            echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "email", array()), 'errors');
            echo "
                        </div>
                    ";
        }
        // line 45
        echo "                </td>
            </tr>

            <tr id=\"";
        // line 48
        echo twig_escape_filter($this->env, ($this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "firstName", array()), "vars", array()), "id", array()) . "_wrapper"), "html", null, true);
        echo "\">
                <th scope=\"row\">
                    ";
        // line 50
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "firstName", array()), 'label');
        echo "
                </th>
                <td>
                    ";
        // line 53
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "firstName", array()), 'widget');
        echo "
                    ";
        // line 54
        if ( !$this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "firstName", array()), "vars", array()), "valid", array())) {
            // line 55
            echo "                        <div class=\"setka-editor-form-errors\">
                            ";
            // line 56
            echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "firstName", array()), 'errors');
            echo "
                        </div>
                    ";
        }
        // line 59
        echo "                </td>
            </tr>

            <tr id=\"";
        // line 62
        echo twig_escape_filter($this->env, ($this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "lastName", array()), "vars", array()), "id", array()) . "_wrapper"), "html", null, true);
        echo "\">
                <th scope=\"row\">
                    ";
        // line 64
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "lastName", array()), 'label');
        echo "
                </th>
                <td>
                    ";
        // line 67
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "lastName", array()), 'widget');
        echo "
                    ";
        // line 68
        if ( !$this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "lastName", array()), "vars", array()), "valid", array())) {
            // line 69
            echo "                        <div class=\"setka-editor-form-errors\">
                            ";
            // line 70
            echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "lastName", array()), 'errors');
            echo "
                        </div>
                    ";
        }
        // line 73
        echo "                </td>
            </tr>

            <tr id=\"";
        // line 76
        echo twig_escape_filter($this->env, ($this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "region", array()), "vars", array()), "id", array()) . "_wrapper"), "html", null, true);
        echo "\">
                <th scope=\"row\">
                    ";
        // line 78
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "region", array()), 'label');
        echo "
                </th>
                <td>
                    ";
        // line 81
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "region", array()), 'widget');
        echo "
                    ";
        // line 82
        if ( !$this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "region", array()), "vars", array()), "valid", array())) {
            // line 83
            echo "                        <div class=\"setka-editor-form-errors\">
                            ";
            // line 84
            echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "region", array()), 'errors');
            echo "
                        </div>
                    ";
        }
        // line 87
        echo "                </td>
            </tr>

            <tr id=\"";
        // line 90
        echo twig_escape_filter($this->env, ($this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "companyDomain", array()), "vars", array()), "id", array()) . "_wrapper"), "html", null, true);
        echo "\">
                <th scope=\"row\">
                    ";
        // line 92
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "companyDomain", array()), 'label');
        echo "
                </th>
                <td>
                    ";
        // line 95
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "companyDomain", array()), 'widget');
        echo "
                    ";
        // line 96
        if ( !$this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "companyDomain", array()), "vars", array()), "valid", array())) {
            // line 97
            echo "                        <div class=\"setka-editor-form-errors\">
                            ";
            // line 98
            echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "companyDomain", array()), 'errors');
            echo "
                        </div>
                    ";
        }
        // line 101
        echo "                </td>
            </tr>

            <tr id=\"";
        // line 104
        echo twig_escape_filter($this->env, ($this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "companyName", array()), "vars", array()), "id", array()) . "_wrapper"), "html", null, true);
        echo "\">
                <th scope=\"row\">
                    ";
        // line 106
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "companyName", array()), 'label');
        echo "
                </th>
                <td>
                    ";
        // line 109
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "companyName", array()), 'widget');
        echo "
                    ";
        // line 110
        if ( !$this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "companyName", array()), "vars", array()), "valid", array())) {
            // line 111
            echo "                        <div class=\"setka-editor-form-errors\">
                            ";
            // line 112
            echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "companyName", array()), 'errors');
            echo "
                        </div>
                    ";
        }
        // line 115
        echo "                </td>
            </tr>

            <tr id=\"";
        // line 118
        echo twig_escape_filter($this->env, ($this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "companySize", array()), "vars", array()), "id", array()) . "_wrapper"), "html", null, true);
        echo "\">
                <th scope=\"row\">
                    ";
        // line 120
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "companySize", array()), 'label');
        echo "
                </th>
                <td>
                    ";
        // line 123
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "companySize", array()), 'widget');
        echo "
                    ";
        // line 124
        if ( !$this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "companySize", array()), "vars", array()), "valid", array())) {
            // line 125
            echo "                        <div class=\"setka-editor-form-errors\">
                            ";
            // line 126
            echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "companySize", array()), 'errors');
            echo "
                        </div>
                    ";
        }
        // line 129
        echo "                </td>
            </tr>

            <tr id=\"";
        // line 132
        echo twig_escape_filter($this->env, ($this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "companyDepartment", array()), "vars", array()), "id", array()) . "_wrapper"), "html", null, true);
        echo "\">
                <th scope=\"row\">
                    ";
        // line 134
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "companyDepartment", array()), 'label');
        echo "
                </th>
                <td>
                    ";
        // line 137
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "companyDepartment", array()), 'widget');
        echo "
                    ";
        // line 138
        if ( !$this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "companyDepartment", array()), "vars", array()), "valid", array())) {
            // line 139
            echo "                        <div class=\"setka-editor-form-errors\">
                            ";
            // line 140
            echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "companyDepartment", array()), 'errors');
            echo "
                        </div>
                    ";
        }
        // line 143
        echo "                </td>
            </tr>

            <tr id=\"";
        // line 146
        echo twig_escape_filter($this->env, ($this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "password", array()), "vars", array()), "id", array()) . "_wrapper"), "html", null, true);
        echo "\">
                <th scope=\"row\">
                    ";
        // line 148
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "password", array()), 'label');
        echo "
                    <span class=\"setka-editor-sub-caption\">";
        // line 149
        echo twig_escape_filter($this->env, $this->getAttribute(($context["translations"] ?? null), "password_sub_label", array()), "html", null, true);
        echo "</span>
                </th>
                <td>
                    ";
        // line 152
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "password", array()), 'widget');
        echo "
                    ";
        // line 153
        if ( !$this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "password", array()), "vars", array()), "valid", array())) {
            // line 154
            echo "                        <div class=\"setka-editor-form-errors\">
                            ";
            // line 155
            echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "password", array()), 'errors');
            echo "
                        </div>
                    ";
        }
        // line 158
        echo "                </td>
            </tr>

            <tr id=\"";
        // line 161
        echo twig_escape_filter($this->env, ($this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "token", array()), "vars", array()), "id", array()) . "_wrapper"), "html", null, true);
        echo "\">
                <th scope=\"row\">
                    ";
        // line 163
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "token", array()), 'label');
        echo "
                </th>
                <td>
                    ";
        // line 166
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "token", array()), 'widget');
        echo "
                    ";
        // line 167
        if ( !$this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "token", array()), "vars", array()), "valid", array())) {
            // line 168
            echo "                        <div class=\"setka-editor-form-errors\">
                            ";
            // line 169
            echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "token", array()), 'errors');
            echo "
                        </div>
                    ";
        }
        // line 172
        echo "                    ";
        if (($context["signedIn"] ?? null)) {
            // line 173
            echo "                        <div class=\"setka-editor-width-like-input-regular-text\">
                            <div class=\"notice setka-editor-notice notice-success setka-editor-notice-success inline\">
                                <p>";
            // line 175
            echo twig_escape_filter($this->env, $this->getAttribute(($context["translations"] ?? null), "already_signed_in", array()), "html", null, true);
            echo "</p>
                            </div>
                        </div>
                    ";
        }
        // line 179
        echo "                </td>
            </tr>

            <tr id=\"";
        // line 182
        echo twig_escape_filter($this->env, ($this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "termsAndConditions", array()), "vars", array()), "id", array()) . "_wrapper"), "html", null, true);
        echo "\">
                <th scope=\"row\">

                </th>
                <td>
                    ";
        // line 187
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "termsAndConditions", array()), 'widget');
        echo "
                    ";
        // line 188
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "termsAndConditions", array()), 'label');
        echo "
                    <div>
                        ";
        // line 190
        echo $this->getAttribute(($context["translations"] ?? null), "terms_and_conditions", array());
        echo "
                    </div>
                    <div>
                        ";
        // line 193
        echo $this->getAttribute(($context["translations"] ?? null), "privacy_policy", array());
        echo "
                    </div>
                    ";
        // line 195
        if ( !$this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "termsAndConditions", array()), "vars", array()), "valid", array())) {
            // line 196
            echo "                        <div class=\"setka-editor-form-errors\">
                            ";
            // line 197
            echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "termsAndConditions", array()), 'errors');
            echo "
                        </div>
                    ";
        }
        // line 200
        echo "                </td>
            </tr>

            <tr id=\"";
        // line 203
        echo twig_escape_filter($this->env, ($this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "whiteLabel", array()), "vars", array()), "id", array()) . "_wrapper"), "html", null, true);
        echo "\">
                <th scope=\"row\">

                </th>
                <td>
                    ";
        // line 208
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "whiteLabel", array()), 'widget');
        echo "
                    ";
        // line 209
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "whiteLabel", array()), 'label');
        echo "
                    ";
        // line 210
        if ( !$this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "whiteLabel", array()), "vars", array()), "valid", array())) {
            // line 211
            echo "                        <div class=\"setka-editor-form-errors\">
                            ";
            // line 212
            echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "whiteLabel", array()), 'errors');
            echo "
                        </div>
                    ";
        }
        // line 215
        echo "                </td>
            </tr>

            <tr id=\"";
        // line 218
        echo twig_escape_filter($this->env, ($this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "submitToken", array()), "vars", array()), "id", array()) . "_wrapper"), "html", null, true);
        echo "\">
                <th scope=\"row\">

                </th>
                <td>
                    ";
        // line 223
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "submitToken", array()), 'row');
        echo "
                </td>
            </tr>
        </tbody>
    </table>

    <div id=\"";
        // line 229
        echo twig_escape_filter($this->env, ($this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "submit", array()), "vars", array()), "id", array()) . "_wrapper"), "html", null, true);
        echo "\" class=\"setka-editor-form-submit\">
        ";
        // line 230
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "submit", array()), 'row');
        echo "
    </div>

    <div id=\"";
        // line 233
        echo twig_escape_filter($this->env, ($this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "submitCompany", array()), "vars", array()), "id", array()) . "_wrapper"), "html", null, true);
        echo "\" class=\"setka-editor-form-submit\">
        ";
        // line 234
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "submitCompany", array()), 'row');
        echo "
    </div>

    ";
        // line 237
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["form"] ?? null), 'form_end');
        echo "
</div>";
    }

    public function getTemplateName()
    {
        return "admin/settings/setka-editor/page.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  545 => 237,  539 => 234,  535 => 233,  529 => 230,  525 => 229,  516 => 223,  508 => 218,  503 => 215,  497 => 212,  494 => 211,  492 => 210,  488 => 209,  484 => 208,  476 => 203,  471 => 200,  465 => 197,  462 => 196,  460 => 195,  455 => 193,  449 => 190,  444 => 188,  440 => 187,  432 => 182,  427 => 179,  420 => 175,  416 => 173,  413 => 172,  407 => 169,  404 => 168,  402 => 167,  398 => 166,  392 => 163,  387 => 161,  382 => 158,  376 => 155,  373 => 154,  371 => 153,  367 => 152,  361 => 149,  357 => 148,  352 => 146,  347 => 143,  341 => 140,  338 => 139,  336 => 138,  332 => 137,  326 => 134,  321 => 132,  316 => 129,  310 => 126,  307 => 125,  305 => 124,  301 => 123,  295 => 120,  290 => 118,  285 => 115,  279 => 112,  276 => 111,  274 => 110,  270 => 109,  264 => 106,  259 => 104,  254 => 101,  248 => 98,  245 => 97,  243 => 96,  239 => 95,  233 => 92,  228 => 90,  223 => 87,  217 => 84,  214 => 83,  212 => 82,  208 => 81,  202 => 78,  197 => 76,  192 => 73,  186 => 70,  183 => 69,  181 => 68,  177 => 67,  171 => 64,  166 => 62,  161 => 59,  155 => 56,  152 => 55,  150 => 54,  146 => 53,  140 => 50,  135 => 48,  130 => 45,  124 => 42,  121 => 41,  119 => 40,  115 => 39,  109 => 36,  105 => 35,  100 => 33,  94 => 29,  88 => 28,  84 => 26,  82 => 25,  80 => 24,  77 => 23,  73 => 22,  70 => 21,  62 => 18,  60 => 17,  58 => 16,  56 => 15,  52 => 14,  47 => 12,  42 => 10,  37 => 8,  32 => 6,  29 => 5,  27 => 4,  24 => 3,  22 => 2,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "admin/settings/setka-editor/page.html.twig", "/opt/bitnami/apps/wordpress/htdocs/wp-content/plugins/setka-editor/twig-templates/admin/settings/setka-editor/page.html.twig");
    }
}
