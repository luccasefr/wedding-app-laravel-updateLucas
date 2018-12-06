<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Guest;
use App\Post;
use App\SensibleWord;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Faker\Factory as Faker;

class PostTest extends TestCase
{
    protected $user,$guest;
    public $faker;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->guest = factory(Guest::class)->create(['user_id'=>$this->user->id]);
        $this->faker = Faker::create();
    }

    public function tearDown(){
        User::truncate();
        Guest::truncate();
        SensibleWord::truncate();
        Post::truncate();
        parent::tearDown();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSavePost()
    {
        $text = $this->faker->text();
        $response = $this->json('POST','/api/post',[
            'image'=>UploadedFile::fake()->image('avatar.png', 640, 800)->size(100),
            'guest_id'=>$this->guest->id,
            'text'=>$text
        ]);
        $imageUrl=$this->user->id.'/posts-images/'.$this->guest->id.'-0.png';
        // echo $response->getContent();
        Storage::disk('local')->assertExists($imageUrl);
        $response->assertStatus(201)->assertJson(['image_url'=>$imageUrl,'guest_id'=>$this->guest->id,'text'=>$text]);
        $this->assertDatabaseHas('posts',[
            'text'=>$text,
            'guest_id'=>$this->guest->id,
            'image_url'=>$imageUrl
        ]);
        Storage::deleteDirectory($this->user->id);
    }

    public function testErrorSavePostWithSensibleWord()
    {
        factory(SensibleWord::class)->create([
            'user_id'=>$this->user->id,
            'word'=>'invalid'
        ]);
        $response = $this->json('POST','/api/post',[
            'guest_id'=>$this->guest->id,
            'text'=>'this post is invalid'
        ]);

        $response->assertStatus(403)->assertJson(['error'=>'the text containt a word that is not allowed']);
    }

    public function testSavePostWithSensibleWord()
    {
        factory(SensibleWord::class)->create([
            'user_id'=>$this->user->id,
            'word'=>'invalid'
        ]);
        $response = $this->json('POST','/api/post',[
            'guest_id'=>$this->guest->id,
            'text'=>'this post is valid'
        ]);

        $response->assertStatus(201)->assertJson(['guest_id'=>$this->guest->id,'text'=>'this post is valid']);
        $this->assertDatabaseHas('posts',[
            'text'=>'this post is valid',
            'guest_id'=>$this->guest->id
        ]);
    }

    public function testSavePostWithoutImg()
    {
        $text = $this->faker->text();
        $response = $this->json('POST','/api/post',[
            'guest_id'=>$this->guest->id,
            'text'=>$text
        ]);
        $response->assertStatus(201)->assertJson(['guest_id'=>$this->guest->id,'text'=>$text]);
        $this->assertDatabaseHas('posts',[
            'text'=>$text,
            'guest_id'=>$this->guest->id
        ]);
    }

    public function testSaveWithoutGuestId()
    {
        $text = $this->faker->text();
        $response = $this->json('POST','/api/post',[
            'text'=>$text
        ]);

        $response->assertStatus(422)->assertJson(["message"=>"The given data was invalid.","errors"=>["guest_id"=>["The guest id field is required."]]]);
    }

    public function testDeletePostUserAuthenticated()
    {
        Passport::actingAs($this->user);
        $post = factory(Post::class)->create([
            'guest_id'=>$this->guest->id,
        ]);

        $response = $this->json('POST','/api/post/'.$post->id,[
            '_method'=>'delete'
        ]);

        $response->assertStatus(200)->assertJson(['message'=>'post deleted successful']);
        $this->assertDatabaseMissing('posts',[
            'text'=>$post->text,
            'id'=>$post->id
        ]);
    }

    public function testDeletePostGuestCode()
    {
        $post = factory(Post::class)->create([
            'guest_id'=>$this->guest->id,
        ]);

        $response = $this->json('POST','/api/post/'.$post->id,[
            '_method'=>'delete',
            'guest_id'=>$this->guest->id
        ]);

        $response->assertStatus(200)->assertJson(['message'=>'post deleted successful']);
        $this->assertDatabaseMissing('posts',[
            'text'=>$post->text,
            'id'=>$post->id
        ]);
    }

    public function testDeletePostThatNotBelongsToGuest()
    {
        $user = factory(User::class)->create();
        $guest = factory(Guest::class)->create([
            'user_id'=>$user->id,
        ]);
        $post = factory(Post::class)->create([
            'guest_id'=>$guest->id,
        ]);

        $response = $this->json('POST','/api/post/'.$post->id,[
            '_method'=>'delete',
            'guest_id'=>$this->guest->id
        ]);

        $response->assertStatus(401)->assertJson(['error'=>'You are not allowed to delete this post']);
    }

    public function testDeletePostThatNotBelongsToGuestOfThisUser()
    {
        Passport::actingAs($this->user);
        $user = factory(User::class)->create();
        $guest = factory(Guest::class)->create([
            'user_id'=>$user->id
        ]);
        $post = factory(Post::class)->create([
            'guest_id'=>$guest->id,
        ]);
        // print_r($user);
        $response = $this->json('POST','/api/post/'.$post->id,[
            '_method'=>'delete'
        ]);

        $response->assertStatus(401)->assertJson(['error'=>'You are not allowed to delete this post']);
    }

    public function testReturnPostsForAprove()
    {
        $this->user = factory(User::class)->create([
            'publications_should_be_aproved'=>true
        ]);
        $this->guest = factory(Guest::class)->create(['user_id'=>$this->user->id]);
        Passport::actingAs($this->user);

        factory(Post::class,5)->create([
            'guest_id'=>$this->guest->id
        ]);

        $posts = factory(Post::class,10)->create([
            'guest_id'=>$this->guest->id
        ]);

        foreach ($posts as $post) {
            $post->aproved=true;
            $post->save();
        }

        $response = $this->withHeaders(['Accept' => 'application/json'])->get('/api/user/posts-for-aprove');

        $content = json_decode($response->getContent());

        $response->assertStatus(200);


        $this->assertTrue(count($content)==5);
    }

    public function testReturnPostsForAproveNotAuthenticated()
    {
        $this->user = factory(User::class)->create([
            'publications_should_be_aproved'=>true
        ]);

        $this->guest = factory(Guest::class)->create(['user_id'=>$this->user->id]);

        $posts = factory(Post::class,10)->create([
            'guest_id'=>$this->guest->id
        ]);

        factory(Post::class,5)->create([
            'guest_id'=>$this->guest->id
        ]);


        foreach ($posts as $post) {
            $post->aproved=true;
            $post->save();
        }

        $response = $this->withHeaders(['Accept' => 'application/json'])->get('/api/user/posts-for-aprove');

        $response->assertStatus(401)->assertJson(["message"=>"Unauthenticated."]);;
    }

    public function testReturnPostsAprovedWithUserAthentication()
    {
        $this->user = factory(User::class)->create([
            'publications_should_be_aproved'=>true
        ]);
        $this->guest = factory(Guest::class)->create(['user_id'=>$this->user->id]);
        Passport::actingAs($this->user);

        factory(Post::class,5)->create([
            'guest_id'=>$this->guest->id
        ]);

        $posts = factory(Post::class,10)->create([
            'guest_id'=>$this->guest->id
        ]);

        foreach ($posts as $post) {
            $post->aproved=true;
            $post->save();
        }

        $response = $this->withHeaders(['Accept' => 'application/json'])->get('/api/posts');

        $content = json_decode($response->getContent());

        $response->assertStatus(200);
        // print_r($response->getContent());

        $this->assertTrue(count($content)==10);
    }

    public function testReturnPostsWithGuestAuthentication()
    {
        $this->user = factory(User::class)->create([
            'publications_should_be_aproved'=>true
        ]);
        $this->guest = factory(Guest::class)->create(['user_id'=>$this->user->id]);

        factory(Post::class,5)->create([
            'guest_id'=>$this->guest->id
        ]);

        $posts = factory(Post::class,10)->create([
            'guest_id'=>$this->guest->id
        ]);

        foreach ($posts as $post) {
            $post->aproved=true;
            $post->save();
        }

        $guest = factory(Guest::class)->create(['user_id'=>$this->user->id]);

        $response = $this->withHeaders(['Accept' => 'application/json','GuestAuthorization'=>$guest->id])->get('/api/posts');

        $content = json_decode($response->getContent());
        // print_r($response->getContent());

        $response->assertStatus(200);

        $this->assertTrue(count($content)==10);
    }

    public function testReturnPostNotAuthenticated()
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])->get('/api/posts');
        // print_r($response->getContent());
        $response->assertStatus(401)->assertJson(["message"=>"Unauthenticated."]);
    }

    public function testApprovePost()
    {
        $this->user = factory(User::class)->create([
            'publications_should_be_aproved'=>true
        ]);

        Passport::actingAs($this->user);

        $this->guest = factory(Guest::class)->create(['user_id'=>$this->user->id]);

        $post = factory(Post::class)->create([
            'guest_id'=>$this->guest->id
        ]);

        $response = $this->json('POST','/api/post/'.$post->id.'/aprove');

        $response->assertStatus(200)->assertJson(['aproved'=>true]);
    }

    public function testAproveNotMine()
    {
        Passport::actingAs($this->user);
        $user = factory(User::class)->create([
            'publications_should_be_aproved'=>true
        ]);


        $this->guest = factory(Guest::class)->create(['user_id'=>$user->id]);

        $post = factory(Post::class)->create([
            'guest_id'=>$this->guest->id
        ]);

        $response = $this->json('POST','/api/post/'.$post->id.'/aprove');
        // print_r($response->getContent());
        $response->assertStatus(401)->assertJson(['error'=>'You are not allowed to change this post']);
    }

    public function testPostLike()
    {
        $this->user = factory(User::class)->create([
            'publications_should_be_aproved'=>true
        ]);

        $this->guest = factory(Guest::class)->create(['user_id'=>$this->user->id]);

        $post = factory(Post::class)->create([
            'guest_id'=>$this->guest->id
        ]);

        $response = $this->withHeaders(['GuestAuthorization' => $this->guest->id])->json('POST','/api/post/'.$post->id.'/like');

        $response->assertStatus(200)->assertJson(['message'=>'Post Liked successful']);
    }
}
