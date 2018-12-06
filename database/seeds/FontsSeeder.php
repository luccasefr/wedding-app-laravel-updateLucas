<?php

use Illuminate\Database\Seeder;

class FontsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('fonts')->insert([
          ['name'=>'Bebas','font_url'=>'BebasNeue Regular.otf'],
          ['name'=>'Arial','font_url'=>'arial.ttf'],
          ['name'=>'Aspades','font_url'=>'Aspades-Regular.ttf'],
          ['name'=>'Watermelon','font_url'=>'Watermelon Script Demo_0.ttf'],
          ['name'=>'Bianka','font_url'=>'Bianka Script.otf'],
          ['name'=>'Misses','font_url'=>'Misses.otf'],
          ['name'=>'EasyN','font_url'=>'Easy November.ttf'],
        ]);
    }
}
