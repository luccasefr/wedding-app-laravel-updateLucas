<?php

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
      $this->call(ModulesTableSeeder::class);
      //$this->call(UserTableSeeder::class);
      $this->call(ColorSeed::class);
      $this->call(FontsSeeder::class);
    }
}
