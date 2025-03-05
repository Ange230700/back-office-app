<?php

// source\Controllers\Volunteer\VolunteerController.php

namespace Kouak\BackOfficeApp\Controllers\Volunteer;

use PDO;
use PDOException;
use \Kouak\BackOfficeApp\Exceptions\DatabaseException;
use Kouak\BackOfficeApp\Models\Volunteer\VolunteerManager;
use Kouak\BackOfficeApp\Models\CollectionVolunteer\CollectionVolunteerManager;


class VolunteerController
{
    private $pdo;
    private $volunteerManager;
    private $collectionVolunteerManager;

    const ERROR_MSG = "Erreur de la base de données : ";

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->volunteerManager = new VolunteerManager($pdo);
        $this->collectionVolunteerManager = new CollectionVolunteerManager($pdo);
    }

    public function addVolunteer($submittedName, $submittedEmail, $hashedPassword, $submittedRole, $submittedParticipations): ?int
    {
        try {
            $volunteerId = $this->volunteerManager->createVolunteer($submittedName, $submittedEmail, $hashedPassword, $submittedRole);
            $this->collectionVolunteerManager->createVolunteerParticipation($submittedParticipations, $volunteerId);
            return $volunteerId;
        } catch (PDOException $e) {
            throw new DatabaseException("Une erreur est survenue lors de l'ajout du bénévole.", 0, $e);
        }
    }

    public function getVolunteersFullDetailsPaginated(): array
    {
        try {
            return $this->volunteerManager->readVolunteersFullDetailsPaginated();
        } catch (PDOException $e) {
            throw new PDOException(self::ERROR_MSG . $e->getMessage());
        }
    }

    public function getVolunteersList(): ?array
    {
        try {
            return $this->volunteerManager->readVolunteersList();
        } catch (PDOException $e) {
            throw new PDOException(self::ERROR_MSG . $e->getMessage());
        }
    }

    public function getCollectionsListVolunteerAttended($volunteerId): ?array
    {
        try {
            return $this->collectionVolunteerManager->readCollectionsListVolunteerAttended($volunteerId);
        } catch (PDOException $e) {
            throw new PDOException(self::ERROR_MSG . $e->getMessage());
        }
    }

    public function getEditableFieldsOfVolunteer($volunteerId): ?array
    {
        try {
            return $this->volunteerManager->readEditableFieldsOfVolunteer($volunteerId);
        } catch (PDOException $e) {
            throw new PDOException(self::ERROR_MSG . $e->getMessage());
        }
    }

    public function editVolunteer($submittedRole, $volunteerId, $submittedParticipations): void
    {
        try {
            $this->volunteerManager->updateVolunteer($submittedRole, $volunteerId);
            $this->collectionVolunteerManager->updateCollectionsVolunteerAttended($volunteerId, $submittedParticipations);
        } catch (PDOException $e) {
            throw new PDOException(self::ERROR_MSG . $e->getMessage());
        }
    }

    public function eraseVolunteer($volunteerId): void
    {
        try {
            $this->volunteerManager->deleteVolunteer($volunteerId);
        } catch (PDOException $e) {
            throw new PDOException(self::ERROR_MSG . $e->getMessage());
        }
    }
}
