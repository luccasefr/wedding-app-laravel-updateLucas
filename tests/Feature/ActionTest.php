<?php

namespace Tests\Feature;

use App\User;
use App\Action;
use Tests\TestCase;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActionTest extends TestCase
{
    protected $user;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    public function tearDown()
    {
        // User::truncate();
        // Action::truncate();
        // parent::tearDown();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRegisterAction()
    {
        Passport::actingAs($this->user);

        $response = $this->json('POST','/api/action',[
            'title'=>'Action name',
            'expense_value'=>1800,
            'expense_date'=>'01/01/2018',
            'notify_date_from'=>'01/12/2018',
            'notify_date_to'=>'30/12/2018',
            'message'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed mollis enim eget lectus aliquam, at feugiat nisl iaculis. Fusce et rutrum nisi. Aenean dignissim molestie consequat.'
        ]);

        // echo $response->getContent();


        $response->assertStatus(201)->assertJson([
            'title'=>'Action name',
            'expense_value'=>1800,
            'expense_date'=>'01/01/2018',
            'notify_date_from'=>'01/12/2018',
            'notify_date_to'=>'30/12/2018',
            'message'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed mollis enim eget lectus aliquam, at feugiat nisl iaculis. Fusce et rutrum nisi. Aenean dignissim molestie consequat.'
        ]);
    }

    public function testCreateMissingExpenseDate()
    {
        Passport::actingAs($this->user);

        $response = $this->json('POST','/api/action',[
            'title'=>'Action name',
            'expense_value'=>1800,
            'notify_date_from'=>'01/12/2018',
            'notify_date_to'=>'30/12/2018',
            'message'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed mollis enim eget lectus aliquam, at feugiat nisl iaculis. Fusce et rutrum nisi. Aenean dignissim molestie consequat.'
        ]);

        $response->assertStatus(400)->assertJson(['message'=>'expense_value or expense_date is not set']);
    }

    public function testCreateMissingExpenseValue()
    {
        Passport::actingAs($this->user);

        $response = $this->json('POST','/api/action',[
            'title'=>'Action name',
            'expense_date'=>'01/01/2018',
            'notify_date_from'=>'01/12/2018',
            'notify_date_to'=>'30/12/2018',
            'message'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed mollis enim eget lectus aliquam, at feugiat nisl iaculis. Fusce et rutrum nisi. Aenean dignissim molestie consequat.'
        ]);

        $response->assertStatus(400)->assertJson(['message'=>'expense_value or expense_date is not set']);
    }

    public function testCreateMissingNotifyDate()
    {
        Passport::actingAs($this->user);

        $response = $this->json('POST','/api/action',[
            'title'=>'Action name',
            'message'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed mollis enim eget lectus aliquam, at feugiat nisl iaculis. Fusce et rutrum nisi. Aenean dignissim molestie consequat.'
        ]);

        $response->assertStatus(400)->assertJson(['message'=>'notify_date_from, notify_date_to or message is not set']);
    }

    public function testCreateMissingMessage()
    {
        Passport::actingAs($this->user);

        $response = $this->json('POST','/api/action',[
            'title'=>'Action name',
            'notify_date_from'=>'01/12/2018',
            'notify_date_to'=>'30/12/2018',
        ]);

        $response->assertStatus(400)->assertJson(['message'=>'notify_date_from, notify_date_to or message is not set']);
    }

    public function testRegisterActionNotAutheticated()
    {

        $response = $this->json('POST','/api/action',[
            'title'=>'Action name',
            'expense_value'=>1800,
            'expense_date'=>'01/01/2018',
            'notify_date_from'=>'01/12/2018',
            'notify_date_to'=>'30/12/2018',
            'message'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed mollis enim eget lectus aliquam, at feugiat nisl iaculis. Fusce et rutrum nisi. Aenean dignissim molestie consequat.'
        ]);

        $response->assertStatus(401)->assertJson(["message"=>"Unauthenticated."]);
    }

    public function testGetActions()
    {
        Passport::actingAs($this->user);
        factory(Action::class,2)->create([
            'user_id'=>50
        ]);
        factory(Action::class,10)->create([
            'user_id'=>$this->user->id
        ]);

        $response = $this->withHeaders(['Accept' => 'application/json'])->get('/api/actions');

        // echo $response->getContent();

        $response->assertStatus(200)->assertJsonCount(10);
    }

    public function testGetActionsNotAuthenticated($value='')
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])->get('/api/actions');
        $response->assertStatus(401)->assertJson(["message"=>"Unauthenticated."]);
    }
}
