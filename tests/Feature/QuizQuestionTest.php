<?php

namespace Tests\Feature;

use App\User;
use App\QuizQuestion;
use Tests\TestCase;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Faker\Factory as Faker;

class QuizQuestionTest extends TestCase
{
    protected $user;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    public function tearDown(){
        User::truncate();
        QuizQuestion::truncate();
        parent::tearDown();
    }
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreateQuizQuestion()
    {
        $faker = Faker::create();

        Passport::actingAs($this->user);

        $response = $this->json('POST','/api/quiz/question',[
            'question'=>$faker->text(190),
            'correct_answer'=>$faker->text(190),
            'wrong_answer_1'=>$faker->text(190),
            'wrong_answer_2'=>$faker->text(190),
            'wrong_answer_3'=>$faker->text(190)
        ]);

        $response->assertStatus(201)->assertJsonStructure(['question','correct_answer','wrong_answer_1','wrong_answer_2','wrong_answer_3']);
        $this->assertDatabaseHas('quiz_questions',[
            'user_id'=>$this->user->id,
        ]);
    }

    public function testDeleteQuizQuestion()
    {
        Passport::actingAs($this->user);

        $question = factory(QuizQuestion::class)->create(['user_id'=>$this->user->id]);

        $response = $this->json('POST','/api/quiz/question/'.$question->id,['_method'=>'delete']);


        $response->assertStatus(200)->assertJson(['message'=>'quiz question delete successfuly']);

        $this->assertDatabaseMissing('quiz_questions',['user_id'=>$this->user->id,'id'=>$question->id]);
    }

    public function testDeleteQuizQuestionThatIsNotMine()
    {
        Passport::actingAs($this->user);

        $user = factory(User::class)->create();
        $question = factory(QuizQuestion::class)->create(['user_id'=>$user->id]);

        $response = $this->json('POST','/api/quiz/question/'.$question->id,['_method'=>'delete']);


        $response->assertStatus(401)->assertJson(['message'=>'you are not authorized to delete this quiz question']);
    }
}
