<?php

/* ------------------- CREATE ------------------- */
function addVolunteer(PDO $pdo, $submittedName, $submittedEmail, $hashedPassword, $submittedRole)
{
    try {
        $sqlQueryToInsertVolunteer = "INSERT INTO benevoles(nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)";
        $statementToAddVolunteer = $pdo->prepare($sqlQueryToInsertVolunteer);
        if (!$statementToAddVolunteer->execute([$submittedName, $submittedEmail, $hashedPassword, $submittedRole])) {
            die("Erreur lors de l'insertion du bénévole.");
        }
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
        exit;
    }
}

function addVolunteerParticipation(PDO $pdo, $submittedParticipations, $volunteerId)
{
    try {
        if (!empty($submittedParticipations)) {
            $sqlQueryToInsertVolunteerParticipation = "INSERT INTO benevoles_collectes (id_benevole, id_collecte) VALUES (?, ?)";
            $stmtParticipation = $pdo->prepare($sqlQueryToInsertVolunteerParticipation);
            foreach ($submittedParticipations as $collectionId) {
                if (!$stmtParticipation->execute([$volunteerId, $collectionId])) {
                    die("Erreur lors de l'insertion des participations.");
                }
            }
        }
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
        exit;
    }
}
/* ============================================== */

/* ------------------- READ ------------------- */
function getVolunteersList(PDO $pdo)
{
    try {
        $sqlQueryToSelectVolunteersList = "SELECT id, nom FROM benevoles ORDER BY nom";
        $statementToGetVolunteersList = $pdo->prepare($sqlQueryToSelectVolunteersList);
        if (!$statementToGetVolunteersList->execute()) {
            die("Erreur lors de la récupération des bénévoles.");
        }
        return $statementToGetVolunteersList->fetchAll();
    } catch (PDOException $pdoException) {
        echo "Erreur de la base de données : " . $pdoException->getMessage();
        exit;
    }
}

function getCollectionsListVolunteerAttended(PDO $pdo, $volunteerId)
{
    try {
        $sqlQueryToSelectCollectionsListVolunteerAttended = "SELECT id_collecte FROM benevoles_collectes WHERE id_benevole = ?";
        $statementToGetCollectionsListVolunteerAttended = $pdo->prepare($sqlQueryToSelectCollectionsListVolunteerAttended);
        $statementToGetCollectionsListVolunteerAttended->execute([$volunteerId]);
        return array_column($statementToGetCollectionsListVolunteerAttended->fetchAll(), 'id_collecte');
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
        exit;
    }
}
/* ============================================== */

/* ------------------- UPDATE ------------------- */
function getEditableFieldsOfVolunteer(PDO $pdo, $volunteerId)
{
    try {
        $sqlQueryToSelectEditableFieldsOfVolunteer = "SELECT id, role FROM benevoles WHERE id = ?";
        $statementToGetEditableFieldsOfVolunteer = $pdo->prepare($sqlQueryToSelectEditableFieldsOfVolunteer);
        if (!$statementToGetEditableFieldsOfVolunteer->execute([$volunteerId])) {
            die("Erreur lors de la récupération du bénévole.");
        }
        return $statementToGetEditableFieldsOfVolunteer->fetch();
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
        exit;
    }
}

function updateVolunteer(PDO $pdo, $submittedRole, $volunteerId)
{
    try {
        $sqlQueryToUpdateVolunteer = "UPDATE benevoles SET role = COALESCE(?, role) WHERE id = ?";
        $statementToUpdateVolunteer = $pdo->prepare($sqlQueryToUpdateVolunteer);
        if (!$statementToUpdateVolunteer->execute([$submittedRole, $volunteerId])) {
            die("Erreur lors de la mise à jour du rôle.");
        }
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
        exit;
    }
}

function updateVolunteerParticipations(PDO $pdo, $volunteerId, $submittedParticipations)
{
    try {
        $sqlQueryToDeleteVolunteerParticipations = "DELETE FROM benevoles_collectes WHERE id_benevole = ?";
        $statementToDeleteVolunteerParticipations = $pdo->prepare($sqlQueryToDeleteVolunteerParticipations);
        if ($statementToDeleteVolunteerParticipations->execute([$volunteerId])) {
            die("Erreur lors de la suppression des participations.");
        }
        if (!empty($submittedParticipations)) {
            $sqlQueryToInsertNewVolunteerParticipations = "INSERT INTO benevoles_collectes (id_benevole, id_collecte) VALUES (?, ?)";
            $statementToAddNewVolunteerParticipations = $pdo->prepare($sqlQueryToInsertNewVolunteerParticipations);
            foreach ($submittedParticipations as $collectionId) {
                if (!$statementToAddNewVolunteerParticipations->execute([$volunteerId, $collectionId])) {
                    die("Erreur lors de l'assignation des collectes.");
                }
            }
        }
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
    }
}
/* ============================================== */

/* ------------------- DELETE ------------------- */
/* ============================================== */