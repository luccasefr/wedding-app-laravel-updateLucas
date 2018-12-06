<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\GiftList;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GiftTest extends TestCase
{
    protected $user;
    public function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    public function tearDown()
    {
        GiftList::truncate();
        User::truncate();
        parent::tearDown();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreate()
    {
        Passport::actingAs($this->user);

        $response = $this->json('POST','/api/user/gift',[
            'name'=>'list one',
            'link'=>'https://lista.test.com'
        ]);
        $response->assertStatus(201)->assertJson([
            'name'=>'list one',
            'link'=>'https://lista.test.com',
            'user_id'=>$this->user->id
        ]);

        $this->assertDatabaseHas('gift_lists',[
            'name'=>'list one',
            'link'=>'https://lista.test.com',
            'user_id'=>$this->user->id
        ]);
    }

    public function testCreateWithoutAuthenticated()
    {
        $response = $this->json('POST','/api/user/gift',[
            'name'=>'list one',
            'link'=>'https://lista.test.com'
        ]);
        $response->assertStatus(401)->assertJson(["message"=>"Unauthenticated."]);
    }

    public function testCreateNoLink()
    {
        Passport::actingAs($this->user);
        $response = $this->json('POST','/api/user/gift',[]);
        $response->assertStatus(422)->assertJson(["message"=>"The given data was invalid.","errors"=>[
            "link"=>["The link field is required."],
            "name"=>["The name field is required."]
        ]]);
    }

    public function testGetAll()
    {
        Passport::actingAs($this->user);
        $gift = factory(GiftList::class,5)->create([
            'user_id'=>$this->user->id+5
        ]);
        $gift = factory(GiftList::class,10)->create([
            'user_id'=>$this->user->id
        ]);
        $response = $this->withHeaders(['Accept' => 'application/json'])->get('/api/user/gifts');
        
        $response->assertStatus(200)->assertJsonCount(10);
    }

    public function testDelete()
    {
        Passport::actingAs($this->user);
        $gift = factory(GiftList::class)->create([
            'user_id'=>$this->user->id
        ]);

        $response = $this->json('POST','/api/gift/'.$gift->id,[
            '_method'=>'delete'
        ]);

        $response->assertStatus(200)->assertJson(['message'=>'Gift list deleted successful']);
        $this->assertDatabaseMissing('gift_lists',[
            'name'=>$gift->name,
            'link'=>$gift->link,
            'id'=>$gift->id
        ]);
    }

    public function testDeleteNotMine()
    {
        Passport::actingAs($this->user);
        $gift = factory(GiftList::class)->create([
            'user_id'=>($this->user->id+1)
        ]);

        $response = $this->json('POST','/api/gift/'.$gift->id,[
            '_method'=>'delete'
        ]);

        $response->assertStatus(401)->assertJson(['message'=>'you are not authorized to delete this gift list']);
    }

    public function testDeleteNotAuthenticated()
    {
        $gift = factory(GiftList::class)->create([
            'user_id'=>($this->user->id)
        ]);
        $response = $this->json('POST','/api/gift/'.$gift->id,[
            '_method'=>'delete'
        ]);
        $response->assertStatus(401)->assertJson(["message"=>"Unauthenticated."]);
    }
}
