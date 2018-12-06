<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Guest;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GuestTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();
        Guest::truncate();
    }

    public function tearDown(){
        Guest::truncate();
        parent::tearDown();
    }

    public function testGeneratorId()
    {
        factory(Guest::class)->create([
            'id' => 'MM97476',
        ]);
        factory(Guest::class)->create([
            'id' => 'MM46879',
        ]);
        factory(Guest::class)->create([
            'id' => 'MM36549',
        ]);
        factory(Guest::class)->create([
            'id' => 'MM16489',
        ]);
        for ($i=0; $i < 10; $i++) {
            $code = Guest::generateId('MM');
            $this->assertEquals(Guest::find($code), NULL);
        }
    }
}
