<?php

namespace App\Mocks;

use App\Interfaces\CalendarServiceInterface;
use App\Models\User;

class CalendarServiceMock implements CalendarServiceInterface
{
    public function getTodayEventsByEmail(string $userEmail): array
    {
        return [
            [
                'title' => 'Project Kickoff',
                'start_time' => '2024-08-24 09:00:00',
                'end_time' => '2024-08-24 10:00:00',
                'participants' => ['alice@example.com', 'bob@example.com'],
            ],
            [
                'title' => 'Client Meeting',
                'start_time' => '2024-08-24 11:00:00',
                'end_time' => '2024-08-24 12:00:00',
                'participants' => ['charlie@example.com'],
            ],
        ];
    }

    public function getTodayEvents(): array
    {
        $emails = User::all()->pluck('email');
        $events = [];

        foreach ($emails as $email) {
            $events[$email] = $this->getTodayEventsByEmail($email);
        }

        return $events;
    }
}
