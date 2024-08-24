<?php
namespace App\Services;

use App\Interfaces\PersonServiceInterface;
use App\Models\Person;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class PersonService implements PersonServiceInterface
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.person.api_key');
        $this->baseUrl = config('services.person.base_url');
    }

    public function getPersonData($email): Person
    {
        $person = Person::where('email', $email)->first();

        if ($person && Carbon::parse($person->updated_at)->gt(now()->subDays(30))) {
            return $person;
        }

        $personData = $this->fetchPersonDataFromApi($email);
        $personData['updated_at'] = now();

        Person::updateOrInsert(
            ['email' => $email],
            $personData,
        );

        return (object) $personData;
    }

    private function fetchPersonDataFromApi($email): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->get($this->baseUrl . "/person/{$email}");
        
        if ($response->successful()) {
            $data = $response->json();

            $person = Person::updateOrCreate(
                ['email' => $email],
                [
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'avatar' => $data['avatar'],
                    'title' => $data['title'],
                    'linkedin_url' => $data['linkedin_url'],
                    'company_name' => $data['company']['name'],
                    'company_linkedin_url' => $data['company']['linkedin_url'],
                    'company_employees' => $data['company']['employees'],
                ]
            );

            return $person;
        }

        throw new \Exception('Error in Person API');
    }

    public function updatePeopleBatch(array $emails): void
    {
        $response = $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->post('/people/batch', ['emails' => $emails]);

        foreach ($response['data'] as $personData) {
            Person::updateOrInsert(
                ['email' => $personData['email']],
                [
                    'first_name' => $personData['first_name'],
                    'last_name' => $personData['last_name'],
                    'title' => $personData['title'],
                    'company_name' => $personData['company_name'],
                    'company_employees' => $personData['company_employees'],
                    'total_meetings' => $personData['total_meetings']
                ]
            );
        }
    }
}
