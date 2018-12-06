<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Address;
use App\Event;
use App\Guest;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\DB;


class EventTest extends TestCase
{

    protected $user;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    public function tearDown()
    {
        User::truncate();
        Address::truncate();
        Event::truncate();
        Guest::truncate();
        DB::table('event_guest')->truncate();
        parent::tearDown();
    }
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreateEvent()
    {
        Passport::actingAs($this->user);

        $response = $this->json('POST','/api/user/event',[
            'street'=>'Rua tal',
            'number'=>'256',
            'neighborhood'=>'bairro',
            'city'=>'cidade',
            'state'=>'state',
            'cep'=>'12345-123',
            'name'=>'evento tal',
            'date'=>'01/01/2018 10:30',
        ]);
        
        $response->assertStatus(201)->assertJson([
            'name'=>'evento tal',
            'date'=>'01/01/2018 10:30'
        ]);

        $this->assertDatabaseHas('addresses',[
            'street'=>'Rua tal',
            'number'=>'256',
            'neighborhood'=>'bairro',
            'city'=>'cidade',
            'state'=>'state',
            'cep'=>'12345-123'
        ]);

        $this->assertDatabaseHas('events',[
            'name'=>'evento tal',
            'date'=>'2018-01-01 10:30:00'
        ]);
    }

    public function testCreateEventMisingParams()
    {
        Passport::actingAs($this->user);

        $response = $this->json('POST','/api/user/event',[
            'street'=>'Rua tal',
            'number'=>'256',
            'neighborhood'=>'bairro',
            'state'=>'state',
            'cep'=>'12345-123',
            'date'=>'01/01/2018 10:30',
        ]);

        $response->assertStatus(422)->assertJson(["message"=>"The given data was invalid.","errors"=>["name"=>["The name field is required."],"city"=>["The city field is required."]]]);
    }

    public function testDeleteEvent()
    {
        Passport::actingAs($this->user);
        $post = factory(Event::class)->create(['user_id'=>$this->user->id]);
        $response = $this->json('POST','/api/event/'.$post->id,[
            '_method'=>'delete'
        ]);

        $response->assertStatus(200)->assertJson(['message'=>'Event deleted successful']);

        $this->assertDatabaseMissing('events',[
            'name'=>$post->name,
            'id'=>$post->id
        ]);
    }

    public function testDeleteNotMine()
    {
        Passport::actingAs($this->user);
        $post = factory(Event::class)->create(['user_id'=>($this->user->id+2)]);
        $response = $this->json('POST','/api/event/'.$post->id,[
            '_method'=>'delete'
        ]);

        $response->assertStatus(401)->assertJson(['message'=>'you are not authorized to delete this event list']);
    }

    public function testReturnEventsCorrectly()
    {
        Passport::actingAs($this->user);

        factory(Event::class,10)->create(['user_id'=>($this->user->id+2)]);
        factory(Event::class,10)->create(['user_id'=>$this->user->id]);

        $response = $this->withHeaders(['Accept' => 'application/json'])->get('/api/events');

        $content = json_decode($response->getContent());

        $response->assertStatus(200);
        $this->assertTrue(count($content)==10);
    }

    public function testReturnEventsWithGuests()
    {
        Passport::actingAs($this->user);

        $guests = factory(Guest::class,10)->create(['user_id'=>($this->user->id)]);
        $event = factory(Event::class)->create(['user_id'=>$this->user->id]);

        foreach ($guests as $guest) {
            $guest->events()->attach($event);
        }

        $response = $this->withHeaders(['Accept' => 'application/json'])->get('/api/events');

        $content = json_decode($response->getContent());
        // print_r($content);
        $response->assertStatus(200);
        $this->assertTrue(count($content[0]->guests)==10);
    }

    public function testReturnEventsWithGuestAuthentication()
    {
        $guest = factory(Guest::class)->create(['user_id'=>$this->user->id]);
        $events = factory(Event::class,10)->create(['user_id'=>$this->user->id]);
        foreach ($events as $event) {
            $event->guests()->attach($guest);
        }
        factory(Event::class,10)->create(['user_id'=>$this->user->id]);

        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guest->id])->get('/api/events');
        $content = json_decode($response->getContent());

        $response->assertStatus(200);
        $this->assertTrue(count($content)==10);
    }

    public function testConfirmPresence()
    {
        $guest = factory(Guest::class)->create(['user_id'=>$this->user->id]);
        $event = factory(Event::class)->create(['user_id'=>$this->user->id]);
        $event->guests()->attach($guest);
        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guest->id])->json('POST','/api/event/'.$event->id.'/confirm',[]);

        $response->assertStatus(200);
        $guest = Guest::find($guest->id)->with('events')->get();

        $this->assertTrue($guest[0]->events[0]->pivot->confirmed==1);
    }

    public function testTryConfirmEvent()
    {
        $guest = factory(Guest::class)->create(['user_id'=>$this->user->id]);
        $event = factory(Event::class)->create(['user_id'=>$this->user->id]);
        // $event->guests()->attach($guest);
        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guest->id])->json('POST','/api/event/'.$event->id.'/confirm',[]);
        $response->assertStatus(401)->assertJson(['error'=>'you are not invited to this event']);
    }

    public function testInviteGuest()
    {
        Passport::actingAs($this->user);
        $guests = factory(Guest::class,13)->create(['user_id'=>$this->user->id]);
        $guestsIds='';
        foreach ($guests as $guest) {
            $guestsIds=$guestsIds.$guest->id.';';
        }
        $guestsIds = substr($guestsIds, 0, -1);
        $event = factory(Event::class)->create(['user_id'=>$this->user->id]);
        $response = $this->json('POST','/api/event/'.$event->id.'/invite',['guest_ids'=>$guestsIds]);

        $response->assertStatus(200);
        $content = json_decode($response->getContent());
        $this->assertTrue(count($content->guests)==13);
    }

    public function testInviteGuestEventNotMine()
    {
        Passport::actingAs($this->user);
        $guests = factory(Guest::class,13)->create(['user_id'=>$this->user->id]);
        $guestsIds='';
        foreach ($guests as $guest) {
            $guestsIds=$guestsIds.$guest->id.';';
        }
        $guestsIds = substr($guestsIds, 0, -1);
        $event = factory(Event::class)->create(['user_id'=>($this->user->id+2)]);
        $response = $this->json('POST','/api/event/'.$event->id.'/invite',['guest_ids'=>$guestsIds]);

        $response->assertStatus(401)->assertJson(['error'=>'this event is not yours']);
    }
}
