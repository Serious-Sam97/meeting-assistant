# Calendar Email Ticket Project

This project implements a proof of concept for sending daily email summaries of meetings to users.

## Key Features

- Fetches today's events from a mock calendar service.
- Updates or retrieves user information from a mock person service.
- Sends daily emails summarizing user meetings.
- Handles recent and outdated user data appropriately.
- Includes basic tests for functionality.

## Setup Instructions

1. **Clone the repository, install dependencies, configure environment, generate application key, run migrations, and serve the application:**
   ```bash
   git clone <repository-url>
   cd <repository-folder>
   composer install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate
   php artisan serve
   php artisan test