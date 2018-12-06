<?php

use Illuminate\Database\Seeder;

class ModulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('modules')->insert([
        ['name' => 'guests'],
        ['name' => 'actions'],
        ['name' => 'games'],
        ['name' => 'invite'],
        ['name' => 'posts'],
        ['name' => 'playlist'],
        ['name' => 'report'],
        ['name' => 'event'],
        ['name' => 'tie'],
        ['name' => 'oculus360'],
        ['name' => 'couplePicture']
        ]);
    }
}
