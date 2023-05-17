<?php

namespace Database\Seeders;

use App\Models\Source;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Add the current 3 sources to the database
        Source::insert([
            ['id' => 1, 'name' => 'news api'],
            ['id' => 2, 'name' => 'the guardian'],
            ['id' => 3, 'name' => 'new york times'],
        ]);
    }
}
