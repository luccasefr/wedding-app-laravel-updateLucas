<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Guest;
use App\Action;
use App\SensibleWord;
use App\GiftList;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    protected $user;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    public function tearDown()
    {
        SensibleWord::truncate();
        User::truncate();
        Guest::truncate();
        Action::truncate();
        GiftList::truncate();
        parent::tearDown();
    }

    public function testGetUser()
    {
        Passport::actingAs($this->user);

        $response = $this->get('/api/user');

        $response->assertStatus(201)->assertJson([
            'name_1'=>$this->user->name_1,
            'name_2'=>$this->user->name_2,
            'email'=>$this->user->email,
            'id'=>$this->user->id,
        ]);
    }

    public function testReturningExpenseCorrectly()
    {
        Passport::actingAs($this->user);

        factory(Action::class,12)->create([
            'expense_value'=>500,
            'user_id'=>$this->user->id
        ]);

        $response = $this->get('/api/user')->assertStatus(201)->assertJson(['expenses_total'=>6000]);
    }

    public function testReturnGiftLists()
    {
        Passport::actingAs($this->user);
        factory(GiftList::class,2)->create([
            'user_id'=>($this->user->id+1)
        ]);

        factory(GiftList::class,10)->create([
            'user_id'=>$this->user->id
        ]);

        $response = $this->get('/api/user');

        $response->assertStatus(201);
        $content = json_decode($response->getContent());
        $this->assertTrue(count($content->gift_lists)==10);
    }

    public function testSensibleWord()
    {
        Passport::actingAs($this->user);
        $response = $this->json('POST','/api/user/sensible-word',[
            'word'=>'test'
        ]);

        $response->assertStatus(201)->assertJson(['user_id'=>$this->user->id,'word'=>'test']);
    }

    public function testSensibleNotAuthenticated()
    {
        $response = $this->json('POST','/api/user/sensible-word',[
            'word'=>'test'
        ]);

        $response->assertStatus(401)->assertJson(["message"=>"Unauthenticated."]);
    }

    public function testGetUserNotAunthenticated()
    {
        $response = $this->withHeaders(['Accept' => 'application/json',])->get('/api/user');
        $response->assertStatus(401)->assertJson(["message"=>"Unauthenticated."]);
    }

    public function testUpdateUser()
    {
        Passport::actingAs($this->user);

        $response = $this->json('POST','/api/user' ,[
            'name_1'=>'alberto',
            'name_2'=>'alberto 2',
            'email'=>'newmail@teste.com.br',
            'wedding_date'=>'01/01/2020',
            'waiting_guests'=>200
        ]);

        $response->assertStatus(201)->assertJson([
            'name_1'=>'alberto',
            'name_2'=>'alberto 2',
            'email'=>'newmail@teste.com.br',
            'id'=>$this->user->id,
        ]);

        $this->assertDatabaseHas('users', [
            'name_1'=>'alberto',
            'name_2'=>'alberto 2',
            'email'=>'newmail@teste.com.br',
            'id'=>$this->user->id,
        ]);
    }
}
