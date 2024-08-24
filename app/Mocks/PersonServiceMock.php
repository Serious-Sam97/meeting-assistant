<?php

namespace App\Mocks;

use App\Interfaces\PersonServiceInterface;
use App\Models\Person;
use Carbon\Carbon;

class PersonServiceMock implements PersonServiceInterface
{
    public function getPersonData($email): Person
    {
        $person = Person::where('email', $email)->first();

        if ($person && Carbon::parse($person->updated_at)->gt(now()->subDays(30))) {
            return $person;
        }

        $personData['updated_at'] = now();

        Person::updateOrInsert(
            ['email' => $email],
            $personData,
        );

        return $person = Person::whereEmail($email)->first();
    }

    public function updatePeopleBatch(array $emails): void
    {
        foreach ($emails as $email) {
            $personData = $this->generateMockPersonData($email);
            Person::updateOrInsert(
                ['email' => $personData['email']],
                $personData
            );
        }
    }

    private function generateMockPersonData($email)
    {
        $mockData = [
            'alice@example.com' => [
                'first_name' => 'Alice',
                'last_name' => 'Johnson',
                'title' => 'Software Engineer',
                'company_name' => 'Tech Corp',
                'company_employees' => 100,
                'total_meetings' => 5
            ],
            'bob@example.com' => [
                'first_name' => 'Bob',
                'last_name' => 'Smith',
                'title' => 'Project Manager',
                'company_name' => 'Business Solutions Inc.',
                'company_employees' => 50,
                'total_meetings' => 3
            ],
            'charlie@example.com' => [
                'first_name' => 'Charlie',
                'last_name' => 'Brown',
                'title' => 'Product Designer',
                'company_name' => 'Design Studio',
                'company_employees' => 25,
                'total_meetings' => 2
            ],
        ];

        return $mockData[$email] ?? [
            'first_name' => 'Unknown',
            'last_name' => 'Person',
            'title' => 'Unknown Title',
            'company_name' => 'Unknown Company',
            'company_employees' => 0,
            'total_meetings' => 0,
            'email' => $email,
        ];
    }
}
