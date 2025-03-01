<?php

namespace Kouak\BackOfficeApp\Views\Components;

use Kouak\BackOfficeApp\Utilities\Session;

class Navbar
{
  /**
   * Render the navigation bar.
   */
  public static function render()
  {
?>
    <nav role="navigation" class="bg-cyan-950 text-white w-64 p-6">
      <h2 class="text-2xl font-bold mb-6">Littoral propre</h2>
      <?php $userId = Session::get('user_id');
      if (!isset($userId)): ?>
        <div class="mt-6 flex flex-col items-center">
          <a href="/back-office-app/index.php?route=login" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg shadow-md text-center">
            Connexion
          </a>
        </div>
      <?php else: ?>
        <ul>
          <li>
            <a href="/back-office-app/index.php?route=collection-list" class="flex items-center py-2 px-3 hover:bg-blue-600 rounded-lg">
              <i class="fas fa-tachometer-alt mr-3"></i> Tableau de bord
            </a>
          </li>
          <?php $role = Session::get("role");
          if ($role === "admin"): ?>
            <li>
              <a href="/back-office-app/index.php?route=collection-add" class="flex items-center py-2 px-3 hover:bg-blue-600 rounded-lg">
                <i class="fas fa-plus-circle mr-3"></i> Ajouter une collecte
              </a>
            </li>
          <?php endif; ?>
          <li>
            <a href="/back-office-app/index.php?route=volunteer-list" class="flex items-center py-2 px-3 hover:bg-blue-600 rounded-lg">
              <i class="fa-solid fa-list mr-3"></i> Liste des bénévoles
            </a>
          </li>
          <?php $role = Session::get("role");
          if ($role === "admin"): ?>
            <li>
              <a href="/back-office-app/index.php?route=volunteer-add" class="flex items-center py-2 px-3 hover:bg-blue-600 rounded-lg">
                <i class="fas fa-user-plus mr-3"></i> Ajouter un bénévole
              </a>
            </li>
          <?php endif; ?>
          <li>
            <a href="/back-office-app/index.php?route=my-account" class="flex items-center py-2 px-3 hover:bg-blue-600 rounded-lg">
              <i class="fas fa-cogs mr-3"></i> Mon compte
            </a>
          </li>
        </ul>
        <div class="mt-6 flex flex-col items-center">
          <a href="/back-office-app/index.php?route=logout" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg shadow-md text-center">
            Déconnexion
          </a>
        </div>
      <?php endif; ?>
    </nav>
<?php
  }
}
