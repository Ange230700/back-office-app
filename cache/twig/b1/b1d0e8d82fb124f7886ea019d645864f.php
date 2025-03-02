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

/* base.twig */
class __TwigTemplate_2ecb01cd837d55a8ea1c8bf1c84c3026 extends Template
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
            'title' => [$this, 'block_title'],
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 2
        yield "<!DOCTYPE html>
<html lang=\"fr\">
  <head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>";
        // line 7
        yield from $this->unwrap()->yieldBlock('title', $context, $blocks);
        yield "</title>
    <script src=\"https://cdn.tailwindcss.com\"></script>
    <link href=\"https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css\" rel=\"stylesheet\">
    <link rel=\"stylesheet\" href=\"/back-office-app/styles/style.css\">
  </head>
  <body class=\"bg-gray-100 text-gray-900\">
    <div class=\"flex h-screen\">
      ";
        // line 14
        yield from $this->loadTemplate("navbar.twig", "base.twig", 14)->unwrap()->yield($context);
        // line 15
        yield "      <main class=\"flex-1 p-8 overflow-y-auto\">
        ";
        // line 16
        yield from $this->unwrap()->yieldBlock('content', $context, $blocks);
        // line 17
        yield "      </main>
    </div>
    <script src=\"/back-office-app/javascript/collection.js\"></script>
  </body>
</html>
";
        yield from [];
    }

    // line 7
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_title(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield "Mon Application";
        yield from [];
    }

    // line 16
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "base.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  89 => 16,  78 => 7,  68 => 17,  66 => 16,  63 => 15,  61 => 14,  51 => 7,  44 => 2,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{# templates/base.twig #}
<!DOCTYPE html>
<html lang=\"fr\">
  <head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>{% block title %}Mon Application{% endblock %}</title>
    <script src=\"https://cdn.tailwindcss.com\"></script>
    <link href=\"https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css\" rel=\"stylesheet\">
    <link rel=\"stylesheet\" href=\"/back-office-app/styles/style.css\">
  </head>
  <body class=\"bg-gray-100 text-gray-900\">
    <div class=\"flex h-screen\">
      {% include 'navbar.twig' %}
      <main class=\"flex-1 p-8 overflow-y-auto\">
        {% block content %}{% endblock %}
      </main>
    </div>
    <script src=\"/back-office-app/javascript/collection.js\"></script>
  </body>
</html>
", "base.twig", "C:\\xampp\\htdocs\\back-office-app\\templates\\base.twig");
    }
}
