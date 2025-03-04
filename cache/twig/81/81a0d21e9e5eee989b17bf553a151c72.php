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

/* login.twig */
class __TwigTemplate_a012d19501d83932e6330fe47cc1f62a extends Template
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

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doGetParent(array $context): bool|string|Template|TemplateWrapper
    {
        // line 3
        return "base.twig";
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $this->parent = $this->loadTemplate("base.twig", "login.twig", 3);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 5
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_title(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield "Connexion";
        yield from [];
    }

    // line 7
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 8
        yield "<div class=\"flex justify-center items-center min-h-screen\">
  <div class=\"bg-white p-8 rounded-lg shadow-lg w-full sm:w-96\">
    <h1 class=\"text-3xl font-bold text-cyan-950 mb-6 text-center\">Connexion</h1>
    ";
        // line 11
        if ((array_key_exists("error", $context) && ($context["error"] ?? null))) {
            // line 12
            yield "      <div class=\"text-red-600 text-center mb-4\">
        ";
            // line 13
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["error"] ?? null), "html", null, true);
            yield "
      </div>
    ";
        }
        // line 16
        yield "    <form method=\"POST\" class=\"space-y-6\">
      <input type=\"hidden\" name=\"csrf_token\" value=\"";
        // line 17
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["csrf_token"] ?? null), "html", null, true);
        yield "\">
      <div>
        <label for=\"email\" class=\"block text-sm font-medium text-gray-700\">Email</label>
        <input type=\"email\" name=\"email\" id=\"email\" required class=\"w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500\">
      </div>
      <div>
        <label for=\"password\" class=\"block text-sm font-medium text-gray-700\">Mot de passe</label>
        <input type=\"password\" name=\"password\" id=\"password\" required class=\"w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500\">
      </div>
      <div class=\"flex justify-between items-center\">
        <a href=\"/back-office-app/index.php?route=home\" class=\"text-sm text-blue-600 hover:underline\">Retour à la page d'accueil</a>
        <button type=\"submit\" class=\"bg-cyan-950 hover:bg-blue-600 text-white px-6 py-2 rounded-lg shadow-md\">
          Se connecter
        </button>
      </div>
    </form>
  </div>
</div>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "login.twig";
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
        return array (  89 => 17,  86 => 16,  80 => 13,  77 => 12,  75 => 11,  70 => 8,  63 => 7,  52 => 5,  41 => 3,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{# templates/login.twig #}

{% extends 'base.twig' %}

{% block title %}Connexion{% endblock %}

{% block content %}
<div class=\"flex justify-center items-center min-h-screen\">
  <div class=\"bg-white p-8 rounded-lg shadow-lg w-full sm:w-96\">
    <h1 class=\"text-3xl font-bold text-cyan-950 mb-6 text-center\">Connexion</h1>
    {% if error is defined and error %}
      <div class=\"text-red-600 text-center mb-4\">
        {{ error }}
      </div>
    {% endif %}
    <form method=\"POST\" class=\"space-y-6\">
      <input type=\"hidden\" name=\"csrf_token\" value=\"{{ csrf_token }}\">
      <div>
        <label for=\"email\" class=\"block text-sm font-medium text-gray-700\">Email</label>
        <input type=\"email\" name=\"email\" id=\"email\" required class=\"w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500\">
      </div>
      <div>
        <label for=\"password\" class=\"block text-sm font-medium text-gray-700\">Mot de passe</label>
        <input type=\"password\" name=\"password\" id=\"password\" required class=\"w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500\">
      </div>
      <div class=\"flex justify-between items-center\">
        <a href=\"/back-office-app/index.php?route=home\" class=\"text-sm text-blue-600 hover:underline\">Retour à la page d'accueil</a>
        <button type=\"submit\" class=\"bg-cyan-950 hover:bg-blue-600 text-white px-6 py-2 rounded-lg shadow-md\">
          Se connecter
        </button>
      </div>
    </form>
  </div>
</div>
{% endblock %}
", "login.twig", "C:\\xampp\\htdocs\\back-office-app\\templates\\login.twig");
    }
}
