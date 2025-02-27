<?php

namespace Kouak\BackOfficeApp\Controllers\Volunteer;

use PDO;
use PDOException;
use Kouak\BackOfficeApp\Models\Volunteer\VolunteerManager;

class VolunteerController
{
    private $pdo;
    private $volunteerManager;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->volunteerManager = new VolunteerManager($pdo);
    }

    /**
     * Add a new volunteer.
     *
     * @param string $submittedName
     * @param string $submittedEmail
     * @param string $hashedPassword
     * @param string $submittedRole
     * @param array  $submittedParticipations
     * @return int The ID of the newly created volunteer.
     * @throws PDOException
     */
    public function addVolunteer($submittedName, $submittedEmail, $hashedPassword, $submittedRole, $submittedParticipations)
    {
        try {
            $volunteerId = $this->volunteerManager->createVolunteer($submittedName, $submittedEmail, $hashedPassword, $submittedRole);
            $this->volunteerManager->createVolunteerParticipation($submittedParticipations, $volunteerId);
            return $volunteerId;
        } catch (PDOException $e) {
            throw new PDOException("Erreur de base de données : " . $e->getMessage());
        }
    }

    /**
     * Retrieve full details of volunteers with pagination.
     *
     * @return array [volunteersList, numberOfPages]
     * @throws PDOException
     */
    public function getVolunteersFullDetailsPaginated()
    {
        return $this->volunteerManager->readVolunteersFullDetailsPaginated();
    }

    /**
     * Retrieve the list of volunteers.
     *
     * @return array
     * @throws PDOException
     */
    public function getVolunteersList()
    {
        try {
            return $this->volunteerManager->readVolunteersList();
        } catch (PDOException $e) {
            throw new PDOException("Erreur de la base de données : " . $e->getMessage());
        }
    }

    /**
     * Retrieve the list of collection IDs that the volunteer attended.
     *
     * @param int $volunteerId
     * @return array
     * @throws PDOException
     */
    public function getCollectionsListVolunteerAttended($volunteerId)
    {
        try {
            return $this->volunteerManager->readCollectionsListVolunteerAttended($volunteerId);
        } catch (PDOException $e) {
            throw new PDOException("Erreur de la base de données : " . $e->getMessage());
        }
    }

    /**
     * Retrieve editable fields of a volunteer.
     *
     * @param int $volunteerId
     * @return array
     * @throws PDOException
     */
    public function getEditableFieldsOfVolunteer($volunteerId)
    {
        try {
            return $this->volunteerManager->readEditableFieldsOfVolunteer($volunteerId);
        } catch (PDOException $e) {
            throw new PDOException("Erreur de la base de données : " . $e->getMessage());
        }
    }

    /**
     * Update volunteer data.
     *
     * @param string $submittedRole
     * @param int    $volunteerId
     * @param array  $submittedParticipations
     * @throws PDOException
     */
    public function editVolunteer($submittedRole, $volunteerId, $submittedParticipations)
    {
        try {
            $this->volunteerManager->updateVolunteer($submittedRole, $volunteerId);
            $this->volunteerManager->updateVolunteerParticipations($volunteerId, $submittedParticipations);
        } catch (PDOException $e) {
            throw new PDOException("Erreur de base de données : " . $e->getMessage());
        }
    }

    /**
     * Delete a volunteer.
     *
     * @param int $volunteerId
     * @throws PDOException
     */
    public function eraseVolunteer($volunteerId)
    {
        try {
            $this->volunteerManager->deleteVolunteer($volunteerId);
        } catch (PDOException $e) {
            throw new PDOException("Erreur de la base de données : " . $e->getMessage());
        }
    }
}
