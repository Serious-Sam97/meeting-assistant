<?php

namespace App\Interfaces;

interface CalendarServiceInterface
{
    public function getTodayEventsByEmail(string $userEmail): array;
    public function getTodayEvents(): array;

}