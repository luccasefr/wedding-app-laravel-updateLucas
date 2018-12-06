<?php

namespace Tests\Feature;

use App\Guest;
use App\User;
use App\Song;
use Tests\TestCase;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\Http\Middleware\CustomAuth;

class SongTest extends TestCase
{
    protected $user,$guest;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->guest = factory(Guest::class)->create(['user_id'=>$this->user->id]);
    }

    public function tearDown(){
        User::truncate();
        Song::truncate();
        Guest::truncate();
        DB::table('songs_likes')->truncate();
        parent::tearDown();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreateSong()
    {
        Passport::actingAs($this->user);

        $response = $this->withHeaders(['GuestAuthorization'=>$this->guest->id])->json('POST','/api/songs',[
            'name'=>'b.y.o.b',
            'artist'=>'System of a down'
        ]);

        $response->assertStatus(201)->assertJson(['name'=>'b.y.o.b','artist'=>'System of a down']);
        $this->assertDatabaseHas('songs',[
            'name'=>'b.y.o.b',
            'artist'=>'System of a down',
            'user_id'=>$this->user->id,
            'guest_id'=>$this->guest->id
        ]);
    }

    public function testReturnSongsUser()
    {
        $this->withoutMiddleware();
        Passport::actingAs($this->user);

        factory(Song::class,10)->create(['user_id'=>$this->user->id]);
        factory(Song::class,10)->create(['user_id'=>($this->user->id+5)]);

        $response = $this->withHeaders(['Accept' => 'application/json'])->get('/api/songs');

        $response->assertStatus(200);


        $content=json_decode($response->getContent());

        $this->assertTrue(count($content)==10);
    }

    public function testReturnSongsGuest()
    {
        factory(Song::class,10)->create(['user_id'=>$this->user->id]);
        factory(Song::class,10)->create(['user_id'=>($this->user->id+5)]);

        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$this->guest->id])->get('/api/songs');

        $response->assertStatus(200);

        $content=json_decode($response->getContent());

        $this->assertTrue(count($content)==10);
    }

    public function testSongUserDelete()
    {
        $this->withoutMiddleware([CustomAuth::class]);
        Passport::actingAs($this->user);

        $song = factory(Song::class)->create(['user_id'=>$this->user->id]);

        $response = $this->actingAs($this->user)->json('POST','/api/song/'.$song->id,['_method'=>'delete']);

        // echo $response->getContent();

        $response->assertStatus(200)->assertJson(['message'=>'song deleted successfuly']);
        $this->assertDatabaseMissing('songs',[
            'name'=>$song->name,
            'artist'=>$song->artist,
            'user_id'=>$this->user->id
        ]);
    }

    public function testSongGuestDelete()
    {

        $song = factory(Song::class)->create(['user_id'=>$this->user->id,'guest_id'=>$this->guest->id]);

        $response = $this->withHeaders(['GuestAuthorization'=>$this->guest->id])->json('POST','/api/song/'.$song->id,['_method'=>'delete']);

        $response->assertStatus(200)->assertJson(['message'=>'song deleted successfuly']);
        $this->assertDatabaseMissing('songs',[
            'name'=>$song->name,
            'artist'=>$song->artist,
            'user_id'=>$this->user->id
        ]);
    }

    public function testSongUserDeleteNotMine()
    {
        $this->withoutMiddleware([CustomAuth::class]);
        Passport::actingAs($this->user);
        $user = factory(User::class)->create();
        $song = factory(Song::class)->create(['user_id'=>$user->id]);


        $response = $this->json('POST','/api/song/'.$song->id,['_method'=>'delete']);

        $response->assertStatus(401)->assertJson(['message'=>'you are not authorized to delete this song']);
    }

    public function testSongGuestDeleteNotBelongsToMyUser()
    {
        $this->withoutMiddleware([CustomAuth::class]);
        Passport::actingAs($this->user);
        $user = factory(User::class)->create();
        $song = factory(Song::class)->create(['user_id'=>$user->id]);


        $response = $this->withHeaders(['GuestAuthorization'=>$this->guest->id])->json('POST','/api/song/'.$song->id,['_method'=>'delete']);

        $response->assertStatus(401)->assertJson(['message'=>'you are not authorized to delete this song']);
    }

    public function testSongGuestDeleteNotMine()
    {

        $guest = factory(Guest::class)->create(['user_id'=>$this->user->id]);
        $song = factory(Song::class)->create(['user_id'=>$this->user->id,'guest_id'=>$guest->id]);


        $response = $this->withHeaders(['GuestAuthorization'=>$this->guest->id])->json('POST','/api/song/'.$song->id,['_method'=>'delete']);

        $response->assertStatus(401)->assertJson(['message'=>'you are not authorized to delete this song']);
    }

    public function testSongLikeAndUnlike()
    {
        $guest = factory(Guest::class)->create(['user_id'=>$this->user->id]);
        $song = factory(Song::class)->create(['user_id'=>$this->user->id,'guest_id'=>$guest->id]);

        $response = $this->withHeaders(['GuestAuthorization'=>$this->guest->id])->json('POST','/api/song/'.$song->id.'/like',[]);

        $response->assertStatus(200)->assertJson(['message'=>'Song Liked successful']);

        $this->assertDatabaseHas('songs_likes',['song_id'=>$song->id,'guest_id'=>$this->guest->id]);

        $response = $this->withHeaders(['GuestAuthorization'=>$this->guest->id])->json('POST','/api/song/'.$song->id.'/like',[]);

        $response->assertStatus(200)->assertJson(['message'=>'Song Unliked successful']);

        $this->assertDatabaseMissing('songs_likes',['song_id'=>$song->id,'guest_id'=>$this->guest->id]);
    }

    public function testLikeSongThatIsNotMine()
    {
        $user = factory(User::class)->create();
        $guest = factory(Guest::class)->create(['user_id'=>$user->id]);
        $song = factory(Song::class)->create(['user_id'=>$user->id,'guest_id'=>$guest->id]);

        $response = $this->withHeaders(['GuestAuthorization'=>$this->guest->id])->json('POST','/api/song/'.$song->id.'/like',[]);

        $response->assertStatus(401)->assertJson(['message'=>'you are not authorized to like this song']);
    }
}
