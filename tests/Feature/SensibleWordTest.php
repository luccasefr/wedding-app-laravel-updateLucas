<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\SensibleWord;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SensibleWordTest extends TestCase
{

    protected $user;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    public function tearDown(){
        User::truncate();
        SensibleWord::truncate();
        parent::tearDown();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testDelete()
    {
        Passport::actingAs($this->user);

        $word = factory(SensibleWord::class)->create([
            'user_id'=>$this->user->id
        ]);

        $response = $this->json('POST','/api/sensible-word/'.$word->id,[
            '_method'=>'delete'
        ]);

        $response->assertStatus(200)->assertJson(['message'=>'Sensible word deleted successful']);
        $this->assertDatabaseMissing('sensible_words',['word'=>$word->word]);
    }

    public function testDeleteNotAuthenticated()
    {

        $word = factory(SensibleWord::class)->create([
            'user_id'=>$this->user->id
        ]);

        $response = $this->json('POST','/api/sensible-word/'.$word->id,[
            '_method'=>'delete'
        ]);

        $response->assertStatus(401)->assertJson(["message"=>"Unauthenticated."]);
    }

    public function testDeleteNotMine()
    {
        Passport::actingAs($this->user);

        $word = factory(SensibleWord::class)->create([
            'user_id'=>($this->user->id+1)
        ]);

        $response = $this->json('POST','/api/sensible-word/'.$word->id,[
            '_method'=>'delete'
        ]);

        $response->assertStatus(401)->assertJson(["message"=>"you are not authorized to delete this word"]);
    }

    public function testGetSensibleWord()
    {
        Passport::actingAs($this->user);

        $word = factory(SensibleWord::class,10)->create([
            'user_id'=>$this->user->id
        ]);

        $response = $this->withHeaders(['Accept' => 'application/json',])->get('/api/user/sensible-words');

        $response->assertStatus(200);

        $content = json_decode($response->getContent());
        $this->assertTrue(count($content)==10);

    }
}
