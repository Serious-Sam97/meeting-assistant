<?php

namespace App\Interfaces;

use App\Models\Person;

interface PersonServiceInterface
{
    public function getPersonData($email): Person;

    public function updatePeopleBatch(array $emails): void;
}