<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Interfaces\CalendarServiceInterface;
use App\Interfaces\PersonServiceInterface;
use App\Models\Email;
use Illuminate\Support\Facades\DB;

class SendDailyEmails extends Command
{
    protected $signature = 'emails:send-daily';
    protected $description = 'Send daily emails with meeting summaries';

    protected $calendarService;
    protected $personService;

    public function __construct(CalendarServiceInterface $calendarService, PersonServiceInterface $personService)
    {
        parent::__construct();
        $this->calendarService = $calendarService;
        $this->personService = $personService;
    }

    public function handle()
    {
        $events = $this->calendarService->getTodayEvents();
        $this->generateEmailContent($events);

        $this->info('Daily emails sent successfully!');
    }

    private function generateEmailContent(array $people)
    {
        foreach ($people as $index => $events) {
            $this->formEmailAndStoreEmail($index, $events);
        }
    }

    private function formEmailAndStoreEmail(string $email, array $events): void
    {
        $emailContent = "Today's Meetings Summary:\n";

        foreach ($events as $index => $event) {
            $emailContent .= "Meeting: {$event['title']} ({$event['start_time']} - {$event['end_time']})\n";
            $emailContent .= "Participants:\n";

            foreach ($event['participants'] as $participantEmail) {
                $person = $this->personService->getPersonData($participantEmail);
                $person->increment('total_meetings');
                $emailContent .= "- {$person->first_name} {$person->last_name} (Met {$person->total_meetings} times)\n";
            }

            $emailContent .= "\n";
        }

        Email::insert([
            'recipient' => $index,
            'content' => $emailContent,
            'sent_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}