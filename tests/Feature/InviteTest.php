<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use App\User;
use App\Invite;
use App\InviteText;

class InviteTest extends TestCase
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
        Invite::truncate();
        InviteText::truncate();
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

        $response = $this->json('POST','/api/invite');

        $response->assertStatus(201)->assertJson([
            'user_id' => $this->user->id
        ]);


        $this->assertDatabaseHas('invites', [
            'user_id' => $this->user->id
        ]);

    }

    public function testCreateInviteText()
    {
        Passport::actingAs($this->user);

        $invite = factory(Invite::class)->create(['user_id'=>$this->user->id]);

        $response = $this->json('POST','/api/invite/text',[
            'text' => 'texto do convite',
            'width' => 0,
            'height' => 0,
            'x' => 0,
            'y' => 0,
            'layer' => 0,
            'font_id' => 1,
            'font_size' => 18,
            'hexColor'=>'#ffffff'
        ]);

        $response->assertStatus(201)->assertJson([
            'text' => 'texto do convite',
            'width' => 0,
            'height' => 0,
            'x' => 0,
            'y' => 0,
            'layer' => 0,
            'font_id' => 1,
            'font_size' => 18,
            'hexColor'=>'#ffffff'
        ]);

        $this->assertDatabaseHas('invite_texts', [
            'text' => 'texto do convite',
            'width' => 0,
            'height' => 0,
            'x' => 0,
            'y' => 0,
            'layer' => 0,
            'font_id' => 1,
            'font_size' => 18,
            'hexColor'=>'#ffffff'
        ]);

    }

    public function testUpdateInviteText()
    {
        Passport::actingAs($this->user);

        $invite = factory(Invite::class)->create(['user_id'=>$this->user->id]);
        $inviteText = factory(InviteText::class)->create(['invite_id'=>$invite->id]);

        $response = $this->json('POST','/api/invite/text/' . $inviteText->id,[
            'text' => 'novo texto do convite',
            'width' => 0,
            'height' => 0,
            'x' => 0,
            'y' => 0,
            'layer' => 0,
            'font_id' => 1,
            'font_size' => 18,
            'hexColor'=>'#ffffff'
        ]);

        $response->assertStatus(200)->assertJson([
            'text' => 'novo texto do convite',
            'width' => 0,
            'height' => 0,
            'x' => 0,
            'y' => 0,
            'layer' => 0,
            'font_id' => 1,
            'font_size' => 18,
            'hexColor'=>'#ffffff'
        ]);

        $this->assertDatabaseHas('invite_texts', [
            'text' => 'novo texto do convite',
            'width' => 0,
            'height' => 0,
            'x' => 0,
            'y' => 0,
            'layer' => 0,
            'font_id' => 1,
            'font_size' => 18,
            'hexColor'=>'#ffffff'
        ]);
    }

    public function testDeleteInviteText()
    {
        Passport::actingAs($this->user);
        $invite = factory(Invite::class)->create(['user_id'=>$this->user->id]);
        $inviteText = factory(InviteText::class)->create(['invite_id'=>$invite->id]);

        $response = $this->json('POST','/api/invite/text/' . $inviteText->id, ['_method' => 'delete']);

        $response->assertStatus(200)->assertJson([
            'message' => 'Invite Text deleted successful'
        ]);

        $this->assertDatabaseMissing('invite_texts', ['id' => $inviteText->id]);
    }

    public function testDeleteIsNotMine()
    {
        Passport::actingAs($this->user);
        $user = factory(User::class)->create();
        $invite = factory(Invite::class)->create(['user_id'=>$user->id]);
        $inviteText = factory(InviteText::class)->create(['invite_id'=>$invite->id]);

        $response = $this->json('POST','/api/invite/text/' . $inviteText->id, ['_method' => 'delete']);
        $response->assertStatus(401)->assertJson(['message'=>'you are not authorized to delete this text']);
    }

    public function testDeleteIsNotMineButIHaveInvite()
    {
        Passport::actingAs($this->user);
        factory(Invite::class)->create(['user_id'=>$this->user->id]);
        $user = factory(User::class)->create();
        $invite = factory(Invite::class)->create(['user_id'=>$user->id]);
        $inviteText = factory(InviteText::class)->create(['invite_id'=>$invite->id]);

        $response = $this->json('POST','/api/invite/text/' . $inviteText->id, ['_method' => 'delete']);
        $response->assertStatus(401)->assertJson(['message'=>'you are not authorized to delete this text']);
    }

    public function testUpdateInvite()
    {
        Passport::actingAs($this->user);
        factory(Invite::class)->create(['user_id'=>$this->user->id]);

        $response=$this->json('POST','/api/invite',[
            '_method'=>'put',
            'bg_url'=>'https://image.com.br/img.jpg'
        ]);

        $response->assertStatus(200)->assertJson(['bg_url'=>'https://image.com.br/img.jpg']);
        $this->assertDatabaseHas('invites',[
            'bg_url'=>'https://image.com.br/img.jpg'
        ]);
    }
}
