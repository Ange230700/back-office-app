<?php

namespace Kouak\BackOfficeApp\Views\Components;

use Kouak\BackOfficeApp\Utilities\Session;
use Kouak\BackOfficeApp\Utilities\View;

class Navbar
{
  /**
   * Render the navigation bar using Twig.
   */
  public static function render()
  {
    // Gather business logic data: user_id and role from the session.
    $navData = [
      'user_id' => Session::get('user_id'),
      'role'    => Session::get('role'),
    ];

    // Render the Twig template with the data.
    echo View::getTwig()->render('navbar.twig', ['session' => $navData]);
  }
}
