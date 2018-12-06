<?php

namespace Tests\Feature;

use App\User;
use App\Guest;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;

class CustomAuthTest extends TestCase
{

    protected function setUp(){
        parent::setUp();
        \Route::middleware('custom.auth')->any('/_test/CustomAuth', function () {
            return ['message'=>'ok'];
        });
    }

    public function tearDown(){
        User::truncate();
        Guest::truncate();
        parent::tearDown();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testMidlareNotAuthenticated()
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])->get('/_test/CustomAuth');
        $response->assertStatus(401)->assertJson(["message"=>"Unauthenticated."]);
    }

    public function testMidlareUserAuthenticated()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $response = $this->withHeaders(['Accept' => 'application/json'])->get('/_test/CustomAuth');
        $response->assertStatus(200)->assertJson(["message"=>"ok"]);
    }

    public function testMidlareGuestAuthenticated()
    {
        $user = factory(User::class)->create();

        $guest = factory(Guest::class)->create([
            'user_id'=>$user->id
        ]);


        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guest->id])->get('/_test/CustomAuth');
        $response->assertStatus(200)->assertJson(["message"=>"ok"]);
    }

    public function testMidlareGuestCodeWrong()
    {
        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>'CC123456'])->get('/_test/CustomAuth');
        $response->assertStatus(401)->assertJson(["message"=>"Unauthenticated."]);
    }
}
