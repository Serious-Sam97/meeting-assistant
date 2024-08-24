<?php

namespace App\Console\Commands;

use App\Interfaces\CalendarServiceInterface;
use Illuminate\Console\Command;
use App\Interfaces\PersonServiceInterface;
use App\Models\User;

class UpdatePeopleTableForToday extends Command
{
    protected $signature = 'people:update-for-today';
    protected $description = 'Update the people table for today\'s events using the Calendar and Person API';

    protected $calendarService;
    protected $personService;

    public function __construct(
        CalendarServiceInterface $calendarService,
        PersonServiceInterface $personService
    ) {
        parent::__construct();
        $this->calendarService = $calendarService;
        $this->personService = $personService;
    }

    public function handle()
    {
        $users = User::all();

        $allParticipantEmails = [];

        foreach ($users as $user) {
            $events = $this->calendarService->getTodayEvents();
            $participantEmails = collect($events)->flatMap(function ($event) {
                return $event['participants'];
            })->unique()->toArray();

            $allParticipantEmails = array_merge($allParticipantEmails, $participantEmails);
        }

        $allParticipantEmails = array_unique($allParticipantEmails);

        $this->personService->updatePeopleBatch($allParticipantEmails);

        $this->info('People table updated for today\'s events successfully!');
    }
}
