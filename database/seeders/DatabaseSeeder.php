<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

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

        // Individual seeders truncate() their table before reseeding, but several of
        // those tables (users, rekanans, staff, kode_rekening) are referenced by foreign
        // keys from kuitansis. MySQL refuses TRUNCATE on a table another table has an FK
        // to, even when that other table is empty, so FK checks are disabled for the
        // duration of seeding.
        Schema::disableForeignKeyConstraints();

        $this->call([
            InstansiSeeder::class,
            UserSeeder::class,
            RekananSeeder::class,
            StaffSeeder::class,
            KodeObjekPajakSeeder::class,
            RekeningSeeder::class,
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
