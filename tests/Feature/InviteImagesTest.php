<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Invite;
use App\InviteImage;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Faker\Factory as Faker;

class InviteImagesTest extends TestCase
{
    protected $user,$faker;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->faker = Faker::create();
    }

    public function tearDown()
    {
        User::truncate();
        Invite::truncate();
        InviteImage::truncate();
        parent::tearDown();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreateInviteImage()
    {
        Passport::actingAs($this->user);

        $response = $this->json('POST','/api/invite/image',[
            'image'=>UploadedFile::fake()->image('avatar.png', 640, 800)->size(100),
            'width'=>$this->faker->numberBetween(0,200),
            'height'=>$this->faker->numberBetween(0,200),
            'x'=>$this->faker->numberBetween(0,200),
            'y'=>$this->faker->numberBetween(0,200),
            'layer'=>$this->faker->numberBetween(0,200),
        ]);

        $imageUrl=$this->user->id.'/invite-images/'.'invite_bg_img-0.png';

        $response->assertStatus(201)->assertJsonStructure(['width','height','x','y','layer','image_url']);

        Storage::disk('local')->assertExists($imageUrl);
        Storage::deleteDirectory($this->user->id);
    }

    public function testDeleteInviteImage()
    {
      Passport::actingAs($this->user);

      Storage::disk('local')->deleteDirectory('test');

      $image = UploadedFile::fake()->image('avatar.png', 640, 800);
      Storage::disk('local')->putFileAs('test', $image,'test.png');

      $invite = factory(Invite::class)->create(['user_id'=>$this->user->id]);
      $inviteImage = factory(InviteImage::class)->create(['invite_id'=>$invite->id,'image_url'=>'test/test.png']);

      $response = $this->json('POST','/api/invite/image/'.$inviteImage->id,['_method'=>'delete']);

      $response->assertStatus(200)->assertJson(['message'=>'image delete successfuly']);
      Storage::disk('local')->assertMissing('test/test.png');
      Storage::disk('local')->deleteDirectory('test');
    }

    public function testDeleteInviteImageThatIsNotMine(){
        Passport::actingAs($this->user);
        $user = factory(User::class)->create();
        $invite = factory(Invite::class)->create(['user_id'=>$user->id]);
        $inviteImage = factory(InviteImage::class)->create(['invite_id'=>$invite->id,'image_url'=>'test/test.png']);

        $response = $this->json('POST','/api/invite/image/'.$inviteImage->id,['_method'=>'delete']);

        $response->assertStatus(401)->assertJson(['message'=>'you are not authorized to delete this image']);
    }
}
