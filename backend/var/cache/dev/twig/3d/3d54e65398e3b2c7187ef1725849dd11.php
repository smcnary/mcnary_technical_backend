<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* @ApiPlatform/Graphiql/index.html.twig */
class __TwigTemplate_4442273f9378b19465e03e05ab11d26d extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'head_metas' => [$this, 'block_head_metas'],
            'title' => [$this, 'block_title'],
            'head_stylesheets' => [$this, 'block_head_stylesheets'],
            'head_javascript' => [$this, 'block_head_javascript'],
            'body_javascript' => [$this, 'block_body_javascript'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@ApiPlatform/Graphiql/index.html.twig"));

        // line 1
        yield "<!DOCTYPE html>
<html>
<head>
    ";
        // line 4
        yield from $this->unwrap()->yieldBlock('head_metas', $context, $blocks);
        // line 7
        yield "
    ";
        // line 8
        yield from $this->unwrap()->yieldBlock('title', $context, $blocks);
        // line 11
        yield "
    ";
        // line 12
        yield from $this->unwrap()->yieldBlock('head_stylesheets', $context, $blocks);
        // line 16
        yield "
    ";
        // line 17
        yield from $this->unwrap()->yieldBlock('head_javascript', $context, $blocks);
        // line 21
        yield "</head>

<body>
<div id=\"graphiql\">Loading...</div>

";
        // line 26
        yield from $this->unwrap()->yieldBlock('body_javascript', $context, $blocks);
        // line 32
        yield "
</body>
</html>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    // line 4
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_head_metas(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "head_metas"));

        // line 5
        yield "        <meta charset=\"UTF-8\">
    ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    // line 8
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_title(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "title"));

        // line 9
        yield "        <title>";
        if ((($tmp = (isset($context["title"]) || array_key_exists("title", $context) ? $context["title"] : (function () { throw new RuntimeError('Variable "title" does not exist.', 9, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["title"]) || array_key_exists("title", $context) ? $context["title"] : (function () { throw new RuntimeError('Variable "title" does not exist.', 9, $this->source); })()), "html", null, true);
            yield " - ";
        }
        yield "API Platform</title>
    ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    // line 12
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_head_stylesheets(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "head_stylesheets"));

        // line 13
        yield "        <link rel=\"stylesheet\" href=\"";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("bundles/apiplatform/graphiql/graphiql.css", (isset($context["assetPackage"]) || array_key_exists("assetPackage", $context) ? $context["assetPackage"] : (function () { throw new RuntimeError('Variable "assetPackage" does not exist.', 13, $this->source); })())), "html", null, true);
        yield "\">
        <link rel=\"stylesheet\" href=\"";
        // line 14
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("bundles/apiplatform/graphiql-style.css", (isset($context["assetPackage"]) || array_key_exists("assetPackage", $context) ? $context["assetPackage"] : (function () { throw new RuntimeError('Variable "assetPackage" does not exist.', 14, $this->source); })())), "html", null, true);
        yield "\">
    ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    // line 17
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_head_javascript(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "head_javascript"));

        // line 18
        yield "        ";
        // line 19
        yield "        <script id=\"graphiql-data\" type=\"application/json\">";
        yield json_encode((isset($context["graphiql_data"]) || array_key_exists("graphiql_data", $context) ? $context["graphiql_data"] : (function () { throw new RuntimeError('Variable "graphiql_data" does not exist.', 19, $this->source); })()), 65);
        yield "</script>
    ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    // line 26
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_body_javascript(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body_javascript"));

        // line 27
        yield "    <script src=\"";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("bundles/apiplatform/react/react.production.min.js", (isset($context["assetPackage"]) || array_key_exists("assetPackage", $context) ? $context["assetPackage"] : (function () { throw new RuntimeError('Variable "assetPackage" does not exist.', 27, $this->source); })())), "html", null, true);
        yield "\"></script>
    <script src=\"";
        // line 28
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("bundles/apiplatform/react/react-dom.production.min.js", (isset($context["assetPackage"]) || array_key_exists("assetPackage", $context) ? $context["assetPackage"] : (function () { throw new RuntimeError('Variable "assetPackage" does not exist.', 28, $this->source); })())), "html", null, true);
        yield "\"></script>
    <script src=\"";
        // line 29
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("bundles/apiplatform/graphiql/graphiql.min.js", (isset($context["assetPackage"]) || array_key_exists("assetPackage", $context) ? $context["assetPackage"] : (function () { throw new RuntimeError('Variable "assetPackage" does not exist.', 29, $this->source); })())), "html", null, true);
        yield "\"></script>
    <script src=\"";
        // line 30
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("bundles/apiplatform/init-graphiql.js", (isset($context["assetPackage"]) || array_key_exists("assetPackage", $context) ? $context["assetPackage"] : (function () { throw new RuntimeError('Variable "assetPackage" does not exist.', 30, $this->source); })())), "html", null, true);
        yield "\"></script>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "@ApiPlatform/Graphiql/index.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  206 => 30,  202 => 29,  198 => 28,  193 => 27,  183 => 26,  172 => 19,  170 => 18,  160 => 17,  150 => 14,  145 => 13,  135 => 12,  121 => 9,  111 => 8,  102 => 5,  92 => 4,  81 => 32,  79 => 26,  72 => 21,  70 => 17,  67 => 16,  65 => 12,  62 => 11,  60 => 8,  57 => 7,  55 => 4,  50 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!DOCTYPE html>
<html>
<head>
    {% block head_metas %}
        <meta charset=\"UTF-8\">
    {% endblock %}

    {% block title %}
        <title>{% if title %}{{ title }} - {% endif %}API Platform</title>
    {% endblock %}

    {% block head_stylesheets %}
        <link rel=\"stylesheet\" href=\"{{ asset('bundles/apiplatform/graphiql/graphiql.css', assetPackage) }}\">
        <link rel=\"stylesheet\" href=\"{{ asset('bundles/apiplatform/graphiql-style.css', assetPackage) }}\">
    {% endblock %}

    {% block head_javascript %}
        {# json_encode(65) is for JSON_UNESCAPED_SLASHES|JSON_HEX_TAG to avoid JS XSS #}
        <script id=\"graphiql-data\" type=\"application/json\">{{ graphiql_data|json_encode(65)|raw }}</script>
    {% endblock %}
</head>

<body>
<div id=\"graphiql\">Loading...</div>

{% block body_javascript %}
    <script src=\"{{ asset('bundles/apiplatform/react/react.production.min.js', assetPackage) }}\"></script>
    <script src=\"{{ asset('bundles/apiplatform/react/react-dom.production.min.js', assetPackage) }}\"></script>
    <script src=\"{{ asset('bundles/apiplatform/graphiql/graphiql.min.js', assetPackage) }}\"></script>
    <script src=\"{{ asset('bundles/apiplatform/init-graphiql.js', assetPackage) }}\"></script>
{% endblock %}

</body>
</html>
", "@ApiPlatform/Graphiql/index.html.twig", "/Users/seanmcnary/Desktop/repos/mcnary_technical_backend/backend/vendor/api-platform/symfony/Bundle/Resources/views/Graphiql/index.html.twig");
    }
}
