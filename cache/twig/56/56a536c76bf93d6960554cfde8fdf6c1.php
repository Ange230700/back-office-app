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

/* navbar.twig */
class __TwigTemplate_54db8a91f9ba9c46ef153fd687ddd784 extends Template
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
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 2
        yield "<nav role=\"navigation\" class=\"bg-cyan-950 text-white w-64 p-6\">
  <h2 class=\"text-2xl font-bold mb-6\">Littoral propre</h2>
  ";
        // line 4
        if (( !CoreExtension::getAttribute($this->env, $this->source, ($context["session"] ?? null), "user_id", [], "any", true, true, false, 4) || Twig\Extension\CoreExtension::testEmpty(CoreExtension::getAttribute($this->env, $this->source, ($context["session"] ?? null), "user_id", [], "any", false, false, false, 4)))) {
            // line 5
            yield "    <div class=\"mt-6 flex flex-col items-center\">
      <a href=\"/back-office-app/index.php?route=login\" class=\"w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg shadow-md text-center\">
        Connexion
      </a>
    </div>
  ";
        } else {
            // line 11
            yield "    <ul>
      <li>
        <a href=\"/back-office-app/index.php?route=collection-list\" class=\"flex items-center py-2 px-3 hover:bg-blue-600 rounded-lg\">
          <i class=\"fas fa-tachometer-alt mr-3\"></i> Tableau de bord
        </a>
      </li>
      ";
            // line 17
            if ((CoreExtension::getAttribute($this->env, $this->source, ($context["session"] ?? null), "role", [], "any", false, false, false, 17) == "admin")) {
                // line 18
                yield "        <li>
          <a href=\"/back-office-app/index.php?route=collection-add\" class=\"flex items-center py-2 px-3 hover:bg-blue-600 rounded-lg\">
            <i class=\"fas fa-plus-circle mr-3\"></i> Ajouter une collecte
          </a>
        </li>
      ";
            }
            // line 24
            yield "      <li>
        <a href=\"/back-office-app/index.php?route=volunteer-list\" class=\"flex items-center py-2 px-3 hover:bg-blue-600 rounded-lg\">
          <i class=\"fa-solid fa-list mr-3\"></i> Liste des bénévoles
        </a>
      </li>
      ";
            // line 29
            if ((CoreExtension::getAttribute($this->env, $this->source, ($context["session"] ?? null), "role", [], "any", false, false, false, 29) == "admin")) {
                // line 30
                yield "        <li>
          <a href=\"/back-office-app/index.php?route=volunteer-add\" class=\"flex items-center py-2 px-3 hover:bg-blue-600 rounded-lg\">
            <i class=\"fas fa-user-plus mr-3\"></i> Ajouter un bénévole
          </a>
        </li>
      ";
            }
            // line 36
            yield "      <li>
        <a href=\"/back-office-app/index.php?route=my-account\" class=\"flex items-center py-2 px-3 hover:bg-blue-600 rounded-lg\">
          <i class=\"fas fa-cogs mr-3\"></i> Mon compte
        </a>
      </li>
    </ul>
    <div class=\"mt-6 flex flex-col items-center\">
      <a href=\"/back-office-app/index.php?route=logout\" class=\"w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg shadow-md text-center\">
        Déconnexion
      </a>
    </div>
  ";
        }
        // line 48
        yield "</nav>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "navbar.twig";
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
        return array (  105 => 48,  91 => 36,  83 => 30,  81 => 29,  74 => 24,  66 => 18,  64 => 17,  56 => 11,  48 => 5,  46 => 4,  42 => 2,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{# templates/navbar.twig #}
<nav role=\"navigation\" class=\"bg-cyan-950 text-white w-64 p-6\">
  <h2 class=\"text-2xl font-bold mb-6\">Littoral propre</h2>
  {% if session.user_id is not defined or session.user_id is empty %}
    <div class=\"mt-6 flex flex-col items-center\">
      <a href=\"/back-office-app/index.php?route=login\" class=\"w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg shadow-md text-center\">
        Connexion
      </a>
    </div>
  {% else %}
    <ul>
      <li>
        <a href=\"/back-office-app/index.php?route=collection-list\" class=\"flex items-center py-2 px-3 hover:bg-blue-600 rounded-lg\">
          <i class=\"fas fa-tachometer-alt mr-3\"></i> Tableau de bord
        </a>
      </li>
      {% if session.role == 'admin' %}
        <li>
          <a href=\"/back-office-app/index.php?route=collection-add\" class=\"flex items-center py-2 px-3 hover:bg-blue-600 rounded-lg\">
            <i class=\"fas fa-plus-circle mr-3\"></i> Ajouter une collecte
          </a>
        </li>
      {% endif %}
      <li>
        <a href=\"/back-office-app/index.php?route=volunteer-list\" class=\"flex items-center py-2 px-3 hover:bg-blue-600 rounded-lg\">
          <i class=\"fa-solid fa-list mr-3\"></i> Liste des bénévoles
        </a>
      </li>
      {% if session.role == 'admin' %}
        <li>
          <a href=\"/back-office-app/index.php?route=volunteer-add\" class=\"flex items-center py-2 px-3 hover:bg-blue-600 rounded-lg\">
            <i class=\"fas fa-user-plus mr-3\"></i> Ajouter un bénévole
          </a>
        </li>
      {% endif %}
      <li>
        <a href=\"/back-office-app/index.php?route=my-account\" class=\"flex items-center py-2 px-3 hover:bg-blue-600 rounded-lg\">
          <i class=\"fas fa-cogs mr-3\"></i> Mon compte
        </a>
      </li>
    </ul>
    <div class=\"mt-6 flex flex-col items-center\">
      <a href=\"/back-office-app/index.php?route=logout\" class=\"w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg shadow-md text-center\">
        Déconnexion
      </a>
    </div>
  {% endif %}
</nav>
", "navbar.twig", "C:\\xampp\\htdocs\\back-office-app\\templates\\navbar.twig");
    }
}
