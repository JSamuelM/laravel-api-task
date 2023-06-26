<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Just call de seeders
        $this->call([
            UserSeeder::class,
            TaskSeeder::class
        ]);

        // If app is local environment use
//        if (app()->environment('local')) {
//            put seeder
//            AnnouncementSeeder::class
//        }
    }
}
