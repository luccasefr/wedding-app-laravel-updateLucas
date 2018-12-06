<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Guest;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthGuestTest extends TestCase
{

    protected function setUp(){
        parent::setUp();
        \Route::middleware('auth.guest')->any('/_test/AuthGuest', function () {
            return ['message'=>'ok'];
        });
    }

    public function tearDown(){
        Guest::truncate();
        parent::tearDown();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGuestAuthentication()
    {
        $guest = factory(Guest::class)->create();
        $response =  $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guest->id])->get('/_test/AuthGuest');

        $response->assertStatus(200)->assertJson(['message'=>'ok']);
    }

    public function testNotAuthenticated()
    {
        $response =  $this->withHeaders(['Accept' => 'application/json'])->get('/_test/AuthGuest');

        $response->assertStatus(401)->assertJson(["message"=>"Unauthenticated."]);
    }

    public function testAuthenticationCodeInvalid()
    {
        $response =  $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>'mm135465'])->get('/_test/AuthGuest');

        $response->assertStatus(401)->assertJson(["message"=>"Unauthenticated."]);
    }
}
