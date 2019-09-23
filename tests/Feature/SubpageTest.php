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
        $name = 'Tytuł testowy';
        $response = $this->json('POST', '/', [
            'name' => $name
        ]);
        $response->assertOk()->assertJson([
            'success' => true,
            'subpage' => ['name' => $name]
        ]);
        $this->assertCount(1, Subpage::all());
    }

    /** @test */
    public function a_subpage_can_be_updated()
    {
        $oldName = "Stara nazwa";
        $newName = "Nowa nazwa";
        $response = $this->json('POST','/', [
            'name' => $oldName
        ]);
        $response->assertOk()->assertJson([
            'success' => true,
            'subpage' => ['name' => $oldName]
        ]);
        $this->assertCount(1, Subpage::all());

        $slug = $response->json('subpage.slug');
        $response = $this->json('PATCH','/'.$slug, [
            'name' => $newName
        ]);
        $response->assertOk()->assertJson([
            'success' => true,
            'subpage' => [
                'name' => $newName,
                'slug' => Str::slug($newName, '-')
            ]
        ]);
        $this->assertCount(1, Subpage::all());
    }

    /** @test */
    public function a_subpage_can_be_deleted()
    {
        $name = 'Tytuł testowy';
        $response = $this->json('POST','/', [
            'name' => $name
        ]);
        $response->assertOk()->assertJson([
            'success' => true,
            'subpage' => ['name' => $name]
        ]);
        $this->assertCount(1, Subpage::all());

        $slug = $response->json('subpage.slug');
        $response = $this->json('DELETE','/'.$slug);
        $response->assertOk()->assertJson([
            'success' => true
        ]);
        $this->assertCount(0, Subpage::all());
    }

    /** @test */
    public function a_subpage_cannot_have_duplicates()
    {
        $name = 'Tytuł testowy 2';

        $response = $this->json('POST', '/', ['name' => $name]);
        $response->assertOk()->assertJson([
            'success' => true,
            'subpage' => ['name' => $name]
        ]);
        $this->assertCount(1, Subpage::all());

        $response = $this->json('POST', '/', ['name' => $name]);
        $response->assertStatus(422);
        $this->assertCount(1, Subpage::all());
    }
}
