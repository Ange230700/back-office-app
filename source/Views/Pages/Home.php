<?php

namespace Kouak\BackOfficeApp\Views\Pages;

class Home
{
    public static function render()
    {
        $pageTitle = "Accueil";
        $pageHeader = "Bienvenue chez 'Littoral propre' !";
        ob_start();
?>
        <p>Il faut se connecter pour accéder aux informations de l'association.</p>
<?php
        $content = ob_get_clean();

        Main::render($pageTitle, $pageHeader, $content);
    }
}
