<?php

use Illuminate\Database\Seeder;

class ColorSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('colors')->insert([
          ['id' => 'black','r'=>0,'g'=>0,'b'=>0],
          ['id' => 'white','r'=>255,'g'=>255,'b'=>255],
        ]);
    }
}
