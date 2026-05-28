<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $host = parse_url(config('app.url'), PHP_URL_HOST) ?? 'zledger.local';
        // Clean host if it has port
        if (str_contains($host, ':')) {
            $host = explode(':', $host)[0];
        }
        
        $email = "admin@{$host}";
        $password = 'admin123'; 
        
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Admin',
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info("✅ Admin User Created (or already exists):");
        $this->command->info("📧 Email: {$email}");
        $this->command->info("🔑 Password: {$password}");
    }
}
