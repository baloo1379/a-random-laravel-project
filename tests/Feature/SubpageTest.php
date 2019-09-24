<?php

namespace Tests\Feature;

use App\Subpage;
use Illuminate\Support\Str;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubpageTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function a_subpage_can_be_created()
    {
        $name = $this->faker->sentence;
        $response = $this->json('POST', '/', [
            'name' => $name
        ]);
        $response->assertOk()->assertJson([
            'success' => true,
            'subpage' => [
                'name' => $name,
                'slug' => Str::slug($name, '-')
            ]
        ]);
        $this->assertCount(1, Subpage::all());
    }

    /** @test */
    public function a_subpage_can_be_updated()
    {
        $subpage = factory('App\Subpage')->create();
        $name = $this->faker->sentence;

        $response = $this->json('PATCH','/'.$subpage->slug, [
            'name' => $name
        ]);
        $response->assertOk()->assertJson([
            'success' => true,
            'subpage' => [
                'name' => $name,
                'slug' => Str::slug($name, '-')
            ]
        ]);
        $this->assertCount(1, Subpage::all());
    }

    /** @test */
    public function a_subpage_can_be_deleted()
    {
        $subpage = factory('App\Subpage')->create();
        $response = $this->json('DELETE','/'.$subpage->slug);
        $response->assertOk()->assertJson([
            'success' => true
        ]);
        $this->assertCount(0, Subpage::all());
        $this->json('GET', '/'.$subpage->slug)->assertStatus(404);
    }

    /** @test */
    public function a_subpage_cannot_have_duplicates()
    {
        $subpage = factory('App\Subpage')->create();
        $response = $this->json('POST', '/', ['name' => $subpage->name]);
        $response->assertStatus(422);
        $this->assertCount(1, Subpage::all());
    }
}
