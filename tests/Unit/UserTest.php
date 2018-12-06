<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\User;
use App\SensibleWord;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    public function tearDown()
    {
        SensibleWord::truncate();
        User::truncate();
        parent::tearDown();
    }

    public function testGetInitials()
    {
        $user = factory(User::class)->make([
            'name_1'=>'João Augusto',
            'name_2'=>'maria antonieta'
        ]);
        $this->assertTrue($user->getInitials()=='JM');
    }

    public function testInvalidText()
    {
        $user = factory(User::class)->create([
            'name_1'=>'João Augusto',
            'name_2'=>'maria antonieta'
        ]);

        $words = factory(SensibleWord::class,10)->create([
            'user_id'=>$user->id
        ]);

        $this->assertTrue(!$user->ValidText($words[5]->word.$words[1]->word));
    }

    public function testValidText()
    {
        $user = factory(User::class)->create([
            'name_1'=>'João Augusto',
            'name_2'=>'maria antonieta'
        ]);

        $words = factory(SensibleWord::class)->create([
            'word'=>'divorcio',
            'user_id'=>$user->id
        ]);

        $this->assertTrue($user->ValidText("este é um post valido"));
    }

    public function testInvalidTextDiferentsCases()
    {
        $user = factory(User::class)->create([
            'name_1'=>'João Augusto',
            'name_2'=>'maria antonieta'
        ]);

        $words = factory(SensibleWord::class)->create([
            'word'=>'invalido',
            'user_id'=>$user->id
        ]);

        $this->assertTrue(!$user->ValidText("este é um post INVALIDO"));
        $this->assertTrue(!$user->ValidText("este é um post iNvAlIdO"));
    }
}
