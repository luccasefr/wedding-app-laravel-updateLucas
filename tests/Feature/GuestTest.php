<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Guest;
use App\GuestsLike;
use App\MatchesConversation;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use \PHPQRCode\QRcode;

class GuestTest extends TestCase
{
    protected $user;
    // use WithoutMiddleware;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    public function tearDown(){
        // User::truncate();
        // Guest::truncate();
        // GuestsLike::truncate();
        // MatchesConversation::truncate();
        // parent::tearDown();
    }

    public function testCreate()
    {
        Passport::actingAs($this->user);

        $response = $this->json('POST','/api/guest',[
            'email'=>'f@gmail.com',
            'phone'=>'12345678910'
        ]);

        $content = json_decode($response->getContent());

        $response->assertStatus(201)->assertJson(['email'=>'f@gmail.com','phone'=>'12345678910']);
        Storage::disk('local')->assertExists($this->user->id.'/'.$content->id.'/qrcode.png');
        Storage::disk('local')->deleteDirectory($this->user->id);
    }

    public function testGetQrCode()
    {
        Passport::actingAs($this->user);

        $guest = factory(Guest::class)->create(['user_id'=>$this->user->id]);
        Storage::disk('local')->makeDirectory($this->user->id.'/'.$guest->id);
        QRcode::png($guest->id, 'storage/app/'.$this->user->id.'/'.$guest->id."/qrcode.png",'H', 10, 1);

        $response = $this->withHeaders(['Accept' => 'application/json'])->get('/api/guest/'.$guest->id.'/qrcode');
        $response->assertStatus(200);
        Storage::disk('local')->deleteDirectory($this->user->id);
    }

    public function testGetGuest()
    {
        $guest = factory(Guest::class)->create(['user_id'=>$this->user->id]);

        $response = $this->withHeaders(['Accept'=>'application/json','GuestAuthorization'=>$guest->id])->get('/api/guest');

        $response->assertStatus(200)->assertJson(['id'=>$guest->id]);
    }


    public function testGetQrCodeNotMine()
    {
        Passport::actingAs($this->user);

        $user = factory(User::class)->create();
        $guest = factory(Guest::class)->create(['user_id'=>$user->id]);

        $response = $this->withHeaders(['Accept' => 'application/json'])->get('/api/guest/'.$guest->id.'/qrcode');
        $response->assertStatus(401)->assertJson(['message'=>'you are not authorized to see this qrcode']);
    }

    public function testCreateNotLogged()
    {

        $response = $this->json('POST','/api/invite',[
            'email'=>'f@gmail.com',
            'phone'=>'12345678910'
        ]);
        $response->assertStatus(401)->assertJson(["message"=>"Unauthenticated."]);
    }

    public function testGetGuests()
    {
        Passport::actingAs($this->user);
        factory(Guest::class,2)->create([
            'user_id'=>50
        ]);
        factory(Guest::class,10)->create([
            'user_id'=>$this->user->id
        ]);

        $response = $this->withHeaders(['Accept' => 'application/json',])->get('/api/guests');
        $response->assertStatus(200)->assertJsonCount(10);
    }

    public function testGetGuestsNotAuthenticated($value='')
    {
        $response = $this->withHeaders(['Accept' => 'application/json',])->get('/api/guests');
        $response->assertStatus(401)->assertJson(["message"=>"Unauthenticated."]);
    }

    public function testGuestConfirm()
    {
        $guest = factory(Guest::class)->create(['user_id'=>$this->user->id]);
        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guest->id])->json('POST','/api/guest/confirm-presence',[]);
        $response->assertStatus(200)->assertJson(['confirmed'=>true]);
    }

    public function testGuestUnConfirm()
    {
        $guest = factory(Guest::class)->create(['user_id'=>$this->user->id]);
        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guest->id])->json('POST','/api/guest/unconfirm-presence',[]);
        $response->assertStatus(200)->assertJson(['confirmed'=>false]);
    }

    public function testGuestUpdate()
    {
        $guest = factory(Guest::class)->create(['user_id'=>$this->user->id]);

        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guest->id])->json('POST','/api/guest/' . $guest->id . '/update',[
            'name' => 'Novo name',
            'email' => 'teste@teste.com',
            'phone' => '123456789'
        ]);

        $response->assertStatus(200)->assertJson([
            'name' => 'Novo name',
            'email' => 'teste@teste.com',
            'phone' => '123456789'
        ]);

        $this->assertDatabaseHas('guests', [
            'name' => 'Novo name',
            'email' => 'teste@teste.com',
            'phone' => '123456789'
        ]);
    }

    public function testGuestLike()
    {

        $guest = factory(Guest::class)->create(['user_id'=>$this->user->id]);
        $guestLiked = factory(Guest::class)->create(['user_id'=>$this->user->id]);

        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guest->id])->json('POST','/api/guest/' . $guestLiked->id . '/like');

        $response->assertStatus(200)->assertJson(['match' => false, 'message'=>'Guest Liked successful']);

        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guestLiked->id])->json('POST','/api/guest/' . $guest->id . '/like');

        $response->assertStatus(200)->assertJson(['match' => true, 'message'=>'Guest Liked successful']);

        $this->assertDatabaseHas('guests_likes', [
            'guest_id' => $guest->id,
            'liked_id' => $guestLiked->id
        ]);

        $this->assertDatabaseHas('guests_likes', [
            'guest_id' => $guestLiked->id,
            'liked_id' => $guest->id
        ]);
    }

    public function testGuestUpdateProfile()
    {
        $guest = factory(Guest::class)->create(['user_id'=>$this->user->id]);

        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guest->id])->json('POST','/api/guest/' . $guest->id . '/update',[
            'name' => 'Novo name',
            'email' => 'teste@teste.com',
            'phone' => '123456789',
            'age' => '18',
            'gender_id' => '1',
            'want_gender_id' => '1',
            'about' => 'Texto breve que me descreve.',
            'image'=>UploadedFile::fake()->image('avatar.png', 640, 800)->size(100),
        ]);
//echo "\n\r\n\r".$response->getContent()."\n\r\n\r";
        $imageUrl=$guest->user_id.'/'.$guest->id.'/profile/'.$guest->id.'.png';

        $response->assertStatus(200)->assertJson([
            'name' => 'Novo name',
            'email' => 'teste@teste.com',
            'phone' => '123456789',
            'age' => '18',
            'gender_id' => '1',
            'want_gender_id' => '1',
            'about' => 'Texto breve que me descreve.',
            'profile_img'=>$imageUrl
        ]);

        $this->assertDatabaseHas('guests', [
            'name' => 'Novo name',
            'email' => 'teste@teste.com',
            'phone' => '123456789',
            'age' => '18',
            'gender_id' => '1',
            'want_gender_id' => '1',
            'about' => 'Texto breve que me descreve.',
            'profile_img'=>$imageUrl
        ]);
    }

    public function testGuestUploadPhoto()
    {
        $guest = factory(Guest::class)->create(['user_id'=>$this->user->id]);
        $imageUrl=$guest->user_id.'/'.$guest->id.'/photo/'.$guest->id;

        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guest->id])->json('POST','/api/guest/' . $guest->id . '/upload-photo',[
            'image1'=>UploadedFile::fake()->image('avatar.png', 640, 800)->size(100),
        ]);

        $response->assertStatus(200)->assertJson([
            'photo1_url'=>$imageUrl.'1.png'
        ]);

        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guest->id])->json('POST','/api/guest/' . $guest->id . '/upload-photo',[
            'image2'=>UploadedFile::fake()->image('avatar.png', 640, 800)->size(100),
        ]);

        $response->assertStatus(200)->assertJson([
            'photo2_url'=>$imageUrl.'2.png'
        ]);

        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guest->id])->json('POST','/api/guest/' . $guest->id . '/upload-photo',[
            'image3'=>UploadedFile::fake()->image('avatar.png', 640, 800)->size(100),
        ]);

        $response->assertStatus(200)->assertJson([
            'photo3_url'=>$imageUrl.'3.png'
        ]);

        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guest->id])->json('POST','/api/guest/' . $guest->id . '/upload-photo',[
            'image1'=>UploadedFile::fake()->image('avatar.png', 640, 800)->size(100),
            'image2'=>UploadedFile::fake()->image('avatar.png', 640, 800)->size(100),
            'image3'=>UploadedFile::fake()->image('avatar.png', 640, 800)->size(100)
        ]);

        $response->assertStatus(200)->assertJson([
            'photo1_url'=>$imageUrl.'1.png',
            'photo2_url'=>$imageUrl.'2.png',
            'photo3_url'=>$imageUrl.'3.png'
        ]);

        $this->assertDatabaseHas('guests', [
            'photo1_url'=>$imageUrl.'1.png',
            'photo2_url'=>$imageUrl.'2.png',
            'photo3_url'=>$imageUrl.'3.png'
        ]);

        Storage::deleteDirectory($this->user->id);
    }

    public function testMatchesConversation()
    {

        $guest = factory(Guest::class)->create(['user_id'=>$this->user->id]);
        $guestMatch = factory(Guest::class)->create(['user_id'=>$this->user->id]);

        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guest->id])->json('POST','/api/guest/' . $guestMatch->id . '/match', [
            'message' => 'a'
        ]);

        echo "\n\r\n\r".$response->getContent()." - ".$guestMatch->id."\n\r\n\r";

        $response->assertStatus(200)->assertJson(['message'=>'Guest Match successful']);

        $this->assertDatabaseHas('matches_conversations', [
            'guest_id' => $guest->id,
            'match_id' => $guestMatch->id,
            'message' => 'a'
        ]);
    }

    public function testGetMatchesConversation()
    {

        $guest = factory(Guest::class)->create(['user_id'=>$this->user->id]);
        $guestMatch = factory(Guest::class)->create(['user_id'=>$this->user->id]);
        $guestMatch2 = factory(Guest::class)->create(['user_id'=>$this->user->id]);
        $guestMatch3 = factory(Guest::class)->create(['user_id'=>$this->user->id]);
        $guestMatch4 = factory(Guest::class)->create(['user_id'=>$this->user->id]);
        $guestMatch5 = factory(Guest::class)->create(['user_id'=>$this->user->id]);

        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guest->id])->json('POST','/api/guest/' . $guestMatch->id . '/match', [
            'message' => 'Olá 1!'
        ]);

        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guestMatch->id])->json('POST','/api/guest/' . $guest->id . '/match', [
            'message' => 'Olá como vai?'
        ]);

        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guest->id])->json('POST','/api/guest/' . $guestMatch->id . '/match', [
            'message' => 'Tudo bem.'
        ]);
        //*****************************************************

        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guest->id])->json('POST','/api/guest/' . $guestMatch2->id . '/match', [
            'message' => 'Olá 2, como vai?'
        ]);

        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guestMatch2->id])->json('POST','/api/guest/' . $guest->id . '/match', [
            'message' => 'Olá, sou mesmo o 2, como vai?'
        ]);

        //*****************************************************

        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guest->id])->json('POST','/api/guest/' . $guestMatch3->id . '/match', [
            'message' => 'Olá 3, como vai?'
        ]);

        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guestMatch3->id])->json('POST','/api/guest/' . $guest->id . '/match', [
            'message' => 'Olá, sou mesmo o 3, como vai?'
        ]);

        //*****************************************************

        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guest->id])->json('POST','/api/guest/' . $guestMatch4->id . '/match', [
            'message' => 'Olá 4, como vai?'
        ]);

        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guestMatch4->id])->json('POST','/api/guest/' . $guest->id . '/match', [
            'message' => 'Olá, sou mesmo o 4, como vai?'
        ]);

        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guestMatch4->id])->json('POST','/api/guest/' . $guest->id . '/match', [
            'message' => 'Ainda está aí?'
        ]);

        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guest->id])->json('POST','/api/guest/' . $guestMatch4->id . '/match', [
            'message' => 'Estou sim 4 tudo bem?'
        ]);

        //*****************************************************

        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guest->id])->json('POST','/api/guest/' . $guestMatch5->id . '/match', [
            'message' => 'Olá 5, como vai?'
        ]);

        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guestMatch5->id])->json('POST','/api/guest/' . $guest->id . '/match', [
            'message' => 'Olá, sou mesmo o 5, como vai?'
        ]);

        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guest->id])->json('POST','/api/guest/' . $guestMatch5->id . '/match', [
            'message' => 'Vou bem.'
        ]);

        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guest->id])->json('GET','/api/guest/match-conversation');

        // echo "\n\r\n\r".$response->getContent()." - ".$guestMatch->id."\n\r\n\r";

        $response->assertStatus(200);
    }

    public function testGetMatchesConversationSpecificGuest()
    {

        $guest1 = factory(Guest::class)->create(['user_id'=>$this->user->id]);
        $guests = factory(Guest::class,5)->create(['user_id'=>$this->user->id]);

        $i = 1;
        foreach ($guests as $guest) {
            $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guest1->id])->json('POST','/api/guest/' . $guest->id . '/match', [
                'message' => 'Olá ' . $i . ', como vai?'
            ]);

            $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guest->id])->json('POST','/api/guest/' . $guest1->id . '/match', [
                'message' => 'Olá, sou mesmo o ' . $i . ', como vai?'
            ]);

            $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guest->id])->json('POST','/api/guest/' . $guest1->id . '/match', [
                'message' => 'Ei, eu sou o ' . $i . ', ainda está aí?'
            ]);

            $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guest1->id])->json('POST','/api/guest/' . $guest->id . '/match', [
                'message' => 'Estou sim ' . $i . ' tudo bem.'
            ]);
            $i++;
        }

        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guest->id])->json('GET','/api/guest/' . $guests[0]->id . '/match-conversation');

        $response->assertStatus(200);

    }

    public function testFcmDeviceToken()
    {

        $guest = factory(Guest::class)->create(['user_id'=>$this->user->id]);

        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guest->id])->json('POST','/api/guest/register-fcm-token', [
            'token' => '123'
        ]);

        echo "\n\r\n\r".$response->getContent()."\n\r\n\r";

        $response->assertStatus(200)->assertJson(['message'=>'Token saved successful']);

        $this->assertDatabaseHas('guests', [
            'id' => $guest->id,
            'fcm_device_token' => '123'
        ]);
    }

}
