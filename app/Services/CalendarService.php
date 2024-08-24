<?php
namespace App\Services;

use App\Interfaces\CalendarServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class CalendarService implements CalendarServiceInterface
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.calendar.api_key');
        $this->baseUrl = config('services.calendar.base_url');
    }

    public function getTodayEventsByEmail(string $userEmail): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->get($this->baseUrl . "/events", [
            'email' => $userEmail,
            'date' => now()->format('Y-m-d'),
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    public function getTodayEvents(): array
    {
        $emails = User::all()->pluck('email');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->post($this->baseUrl . "/events", [
            'emails' => $emails,
            'date' => now()->format('Y-m-d'),
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }
}
