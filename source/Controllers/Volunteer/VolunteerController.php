<?php

namespace Kouak\BackOfficeApp\Controllers\Volunteer;

use PDO;
use PDOException;
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

    public function addVolunteer($submittedName, $submittedEmail, $hashedPassword, $submittedRole, $submittedParticipations)
    {
        try {
            $volunteerId = $this->volunteerManager->createVolunteer($submittedName, $submittedEmail, $hashedPassword, $submittedRole);
            $this->collectionVolunteerManager->createVolunteerParticipation($submittedParticipations, $volunteerId);
            return $volunteerId;
        } catch (PDOException $e) {
            throw new PDOException(self::ERROR_MSG . $e->getMessage());
        }
    }

    public function getVolunteersFullDetailsPaginated()
    {
        try {
            return $this->volunteerManager->readVolunteersFullDetailsPaginated();
        } catch (PDOException $e) {
            throw new PDOException(self::ERROR_MSG . $e->getMessage());
        }
    }

    public function getVolunteersList()
    {
        try {
            return $this->volunteerManager->readVolunteersList();
        } catch (PDOException $e) {
            throw new PDOException(self::ERROR_MSG . $e->getMessage());
        }
    }

    public function getCollectionsListVolunteerAttended($volunteerId)
    {
        try {
            return $this->collectionVolunteerManager->readCollectionsListVolunteerAttended($volunteerId);
        } catch (PDOException $e) {
            throw new PDOException(self::ERROR_MSG . $e->getMessage());
        }
    }

    public function getEditableFieldsOfVolunteer($volunteerId)
    {
        try {
            return $this->volunteerManager->readEditableFieldsOfVolunteer($volunteerId);
        } catch (PDOException $e) {
            throw new PDOException(self::ERROR_MSG . $e->getMessage());
        }
    }

    public function editVolunteer($submittedRole, $volunteerId, $submittedParticipations)
    {
        try {
            $this->volunteerManager->updateVolunteer($submittedRole, $volunteerId);
            $this->collectionVolunteerManager->updateCollectionsVolunteerAttended($volunteerId, $submittedParticipations);
        } catch (PDOException $e) {
            throw new PDOException(self::ERROR_MSG . $e->getMessage());
        }
    }

    public function eraseVolunteer($volunteerId)
    {
        try {
            $this->volunteerManager->deleteVolunteer($volunteerId);
        } catch (PDOException $e) {
            throw new PDOException(self::ERROR_MSG . $e->getMessage());
        }
    }
}
