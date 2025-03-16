<?php
// source\Database\seeding.php

define('BASE_PATH', __DIR__ . '/../../');
require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;
use Faker\Factory as FakerFactory;
use Kouak\BackOfficeApp\Database\Configuration;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

// Get database connection settings from .env
$superAdminPassword = $_ENV['SUPER_ADMIN_PASSWORD'];

// Create a Faker instance (you can choose your locale)
$faker = FakerFactory::create('fr_FR');

// Get PDO instance from your Configuration class
$pdo = Configuration::getPdo();

try {
    // Disable foreign key checks to safely TRUNCATE tables
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

    // Truncate the tables to start from a clean slate
    $tables = ['Collected_waste', 'Volunteer_Collection', 'CollectionEvent', 'Volunteer'];
    foreach ($tables as $table) {
        $pdo->exec("TRUNCATE TABLE `$table`");
        echo "Truncated table $table\n";
    }

    // Insert sample volunteers
    $insertVolunteerStmt = $pdo->prepare("INSERT INTO Volunteer (username, email, password, role) VALUES (?, ?, ?, ?)");
    // Insert super admin volunteer
    $superAdminPasswordHash = password_hash($superAdminPassword, PASSWORD_DEFAULT);
    $insertVolunteerStmt->execute(['SuperAdmin', 'superAdmin@superAdmin.superAdmin', $superAdminPasswordHash, 'superAdmin']);
    echo "Inserted super admin volunteer\n";

    // Insert default admin (example credentials: admin/admin123)
    $pdo->exec("INSERT INTO Volunteer (username, email, password, role) VALUES ('admin', 'admin@admin.admin', '" . password_hash('admin123', PASSWORD_DEFAULT) . "', 'admin')");
    echo "Inserted default admin\n";

    // Insert default user (example credentials: user/user123)
    $pdo->exec("INSERT INTO Volunteer (username, email, password, role) VALUES ('user', 'user@user.user', '" . password_hash('user123', PASSWORD_DEFAULT) . "', 'participant')");
    echo "Inserted default user\n";



    // Insert several participant volunteers
    $volunteerIds = [];
    for ($i = 0; $i < 5; $i++) {
        $name = $faker->name;
        $email = $faker->unique()->email;
        $passwordHash = password_hash('password', PASSWORD_DEFAULT);
        $role = 'participant';
        $insertVolunteerStmt->execute([$name, $email, $passwordHash, $role]);
        $volunteerIds[] = $pdo->lastInsertId();
    }
    echo "Inserted participant volunteers\n";

    // Insert sample collection events
    $insertCollectionStmt = $pdo->prepare("INSERT INTO CollectionEvent (collection_date, collection_place) VALUES (?, ?)");
    $collectionIds = [];
    for ($i = 0; $i < 3; $i++) {
        // Generate a random date and place
        $date = $faker->date('Y-m-d');
        $place = $faker->city();
        $insertCollectionStmt->execute([$date, $place]);
        $collectionIds[] = $pdo->lastInsertId();
    }
    echo "Inserted collection events\n";

    // Assign volunteers to collections
    $insertVolunteerCollectionStmt = $pdo->prepare("INSERT INTO Volunteer_Collection (id_volunteer, id_collection) VALUES (?, ?)");
    foreach ($volunteerIds as $volunteerId) {
        // Randomly assign each volunteer to one or two collections
        $assignedCollections = (array) $faker->randomElements($collectionIds, rand(1, 2));
        foreach ($assignedCollections as $collectionId) {
            $insertVolunteerCollectionStmt->execute([$volunteerId, $collectionId]);
        }
    }
    echo "Assigned volunteers to collections\n";

    // Insert sample collected waste details
    $insertWasteStmt = $pdo->prepare("INSERT INTO Collected_waste (id_collection, waste_type, quantity_kg) VALUES (?, ?, ?)");
    $wasteTypes = ['Plastique', 'Papier', 'Métal', 'Organique'];
    foreach ($collectionIds as $collectionId) {
        // For each collection, insert 1 to 3 waste entries
        $numEntries = rand(1, 3);
        for ($j = 0; $j < $numEntries; $j++) {
            $wasteType = $faker->randomElement($wasteTypes);
            $quantity = $faker->randomFloat(1, 0.1, 100);
            $insertWasteStmt->execute([$collectionId, $wasteType, $quantity]);
        }
    }
    echo "Inserted collected waste details\n";

    // Re-enable foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    echo "Seeding completed successfully.\n";
} catch (PDOException $e) {
    echo "Seeding error: " . $e->getMessage() . "\n";
    exit(1);
}
