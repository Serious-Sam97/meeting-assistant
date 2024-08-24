<?php

namespace Tests\Unit;

use App\Mocks\CalendarServiceMock;
use App\Mocks\PersonServiceMock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Email;
use App\Models\Person;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\DataProvider;

class SendDailyEmailsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->bind('App\Services\CalendarServiceInterface', CalendarServiceMock::class);
        $this->app->bind('App\Services\PersonServiceInterface', PersonServiceMock::class);

        User::factory()->count(5)->create();
    }

    public static function sendDailyEmailsProvider(): array
    {
        return [
            'recent_person_data' => [
                [
                    [
                        'email' => 'alice@example.com',
                        'first_name' => 'Alice',
                        'last_name' => 'Johnson',
                        'title' => 'Software Engineer',
                        'company_name' => 'Tech Corp',
                        'company_employees' => 100,
                        'updated_at' => now()->subDays(10),
                    ],
                    [
                        'email' => 'bob@example.com',
                        'first_name' => 'Bob',
                        'last_name' => 'Smith',
                        'title' => 'Project Manager',
                        'company_name' => 'Business Solutions Inc.',
                        'company_employees' => 50,
                        'updated_at' => now()->subDays(10),
                    ],
                    [
                        'email' => 'charlie@example.com',
                        'first_name' => 'Charlie',
                        'last_name' => 'Brown',
                        'title' => 'Product Designer',
                        'company_name' => 'Design Studio',
                        'company_employees' => 25,
                        'updated_at' => now()->subDays(10),
                    ],
                ],
            ],
            'outdated_person_data' => [
                [
                    [
                        'email' => 'alice@example.com',
                        'first_name' => 'Alice',
                        'last_name' => 'Johnson',
                        'title' => 'Software Engineer',
                        'company_name' => 'Tech Corp',
                        'company_employees' => 100,
                        'updated_at' => now()->subDays(40),
                    ],
                    [
                        'email' => 'bob@example.com',
                        'first_name' => 'Bob',
                        'last_name' => 'Smith',
                        'title' => 'Project Manager',
                        'company_name' => 'Business Solutions Inc.',
                        'company_employees' => 50,
                        'updated_at' => now()->subDays(40),
                    ],
                    [
                        'email' => 'charlie@example.com',
                        'first_name' => 'Charlie',
                        'last_name' => 'Brown',
                        'title' => 'Product Designer',
                        'company_name' => 'Design Studio',
                        'company_employees' => 25,
                        'updated_at' => now()->subDays(40),
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider sendDailyEmailsProvider
     */
    #[DataProvider('sendDailyEmailsProvider')]
    public function test_send_daily_emails(array $personData)
    {
        Person::insert($personData);
        Artisan::call('emails:send-daily');
        $emails = Email::all();

        $this->assertCount(5, $emails);
    }
}