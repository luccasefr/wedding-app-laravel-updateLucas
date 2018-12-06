<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\PuzzleImage;
use Laravel\Passport\Passport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PuzzleImageTest extends TestCase
{
    protected $user;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    public function tearDown(){
        User::truncate();
        PuzzleImage::truncate();
        parent::tearDown();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreatePuzzleImage()
    {
        Passport::actingAs($this->user);

        $response = $this->json('POST','/api/puzzle/image',['image'=>UploadedFile::fake()->image('avatar.png', 640, 800)->size(100)]);
        $imageUrl = '1/puzzle-images/0.png';

        $response->assertStatus(201)->assertJsonStructure(['img_url']);

        $this->assertDatabaseHas('puzzle_images',['img_url'=>$imageUrl]);
        Storage::disk('local')->assertExists($imageUrl);
        Storage::deleteDirectory($this->user->id);
    }

    public function testDeleteImage()
    {
        Passport::actingAs($this->user);

        $image = UploadedFile::fake()->image('avatar.png', 640, 800);
        Storage::disk('local')->putFileAs('test', $image,'test.png');

        $image = factory(PuzzleImage::class)->create(['user_id'=>$this->user->id,'img_url'=>'test/test.png']);

        $response = $this->json('POST','/api/puzzle/image/'.$image->id,['_method'=>'delete']);

        $response->assertStatus(200)->assertJson(['message'=>'puzzle image delete successful']);

        Storage::disk('local')->assertMissing('test/test.png');
        Storage::disk('local')->deleteDirectory('test');
    }

    public function testDeleteImageNotMine()
    {
        Passport::actingAs($this->user);

        $user = factory(User::class)->create();

        $image = factory(PuzzleImage::class)->create(['user_id'=>$user->id]);

        $response = $this->json('POST','/api/puzzle/image/'.$image->id,['_method'=>'delete']);

        $response->assertStatus(401)->assertJson(['message'=>'you are not authorized to delete this image']);
    }

    public function testGetAllImages()
    {
        Passport::actingAs($this->user);

        factory(PuzzleImage::class,10)->create(['user_id'=>$this->user->id]);

        $response = $this->withHeaders(['Accept'=>'application/json'])->get('/api/puzzle/images');

        $response->assertStatus(200)->assertJsonCount(10);
    }
}
