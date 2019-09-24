<?php

namespace Tests\Feature;

use App\BookPost;
use App\Image;
use App\Subpage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BookPostTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private function generatePost($withCover = false, $coverType = 'file', $withPdf= false)
    {
        $post = [
            'title' => $this->faker->sentence,
            'author' => $this->faker->firstName .' '. $this->faker->lastName,
            'city' => $this->faker->city,
            'year' => $this->faker->year,
        ];
        if($withCover) {
            if($coverType === 'url') {
                $post['coverUrl'] = 'https://picsum.photos/id/230/200/300';
            }
            else if ($coverType === 'file') {
                $post['coverFile'] = UploadedFile::fake()->image('cover.jpg');
            }
            else if ($coverType === 'all') {
                $post['coverUrl'] = 'https://picsum.photos/id/230/200/300';
                $post['coverFile'] = UploadedFile::fake()->image('cover.jpg');
            }
        }
        if($withPdf) {
            $post['pdf'] = UploadedFile::fake()->create('doc.pdf', 200);
        }
        return $post;
    }

    /** @test */
    public function a_bP_can_be_created()
    {
        $subpage = factory('App\Subpage')->create();
        $post = $this->generatePost();
        $response = $this->json('POST','/'.$subpage->slug.'/b', $post);
        $response->assertOk()->assertJson([
            'success' => true,
            'post' => [
                'title' => $post['title'],
                'author' => $post['author'],
                'city' => $post['city'],
                'year' => $post['year']
            ]
        ]);
        $this->assertCount(1, BookPost::all());
        $this->assertEquals($post['title'], BookPost::first()->title);
        $this->assertEquals(BookPost::first(), Subpage::first()->posts()->first());
        $this->json('GET', $response->json('post.url'))->assertOk()->assertJson([[
            'title' => $post['title'],
            'author' => $post['author'],
            'city' => $post['city'],
            'year' => $post['year']
        ]]);
        return $response->json('post.url');
    }

    /** @test */
    public function a_bP_can_be_updated()
    {
        $postRoute = $this->a_bP_can_be_created();
        $newPost = $this->generatePost();

        $response = $this->json('PATCH', $postRoute, $newPost);
        $response->assertOk()->assertJson([
            'success' => true,
            'post' => $newPost
        ]);
        $this->assertCount(1, BookPost::all());

        $this->json('GET', $response->json('post.url'))->assertOk()->assertJson([[
            'title' => $newPost['title'],
            'author' => $newPost['author'],
            'city' => $newPost['city'],
            'year' => $newPost['year']
        ]]);
    }

    /** @test */
    public function a_bP_can_be_deleted()
    {
        $route = $this->a_bP_can_be_created();
        $response = $this->json('DELETE', $route);
        $response->assertOk()->assertJson([
            'success' => true
        ]);
        $this->assertCount(0, BookPost::all());
        $this->json('GET', $route)->assertStatus(404);
    }

    /** @test */
    public function a_bP_can_be_created_with_cover_from_file()
    {
        $subpage = factory('App\Subpage')->create();
        $post = $this->generatePost(true, 'file');
        $file = $post['coverFile'];

        $response = $this->json('POST','/'.$subpage->slug.'/b', $post);
        $response->assertOk();
        Storage::disk('public')->assertExists('covers/'.$file->hashName());
        $response->assertJson([
            'success' => true,
            'post' => [
                'title' => $post['title'],
                'author' => $post['author'],
                'city' => $post['city'],
                'year' => $post['year']
            ]
        ]);
        $this->assertCount(1, BookPost::all());
        $this->assertEquals($post['title'], BookPost::first()->title);
        $this->assertEquals(BookPost::first(), Subpage::first()->posts()->first());
        return BookPost::first();
    }

    /** @test */
    public function a_bP_can_be_updated_with_cover_from_file()
    {
        $oldPost = $this->a_bP_can_be_created_with_cover_from_file();
        $newPost = $this->generatePost(true, 'file');
        $file = $newPost['coverFile'];

        $response = $this->json('PATCH', $oldPost->url, $newPost);
        $response->assertOk();
        Storage::disk('public')->assertExists('covers/'.$file->hashName());
        $response->assertJson([
            'success' => true,
            'post' => [
                'title' => $newPost['title'],
                'author' => $newPost['author'],
                'city' => $newPost['city'],
                'year' => $newPost['year']
            ]
        ]);
        $this->assertCount(1, BookPost::all());
        $this->assertEquals($newPost['title'], BookPost::first()->title);
        $this->assertEquals(BookPost::first(), Subpage::first()->posts()->first());
    }

    /** @test */
    public function a_bP_can_be_created_with_cover_from_url()
    {
        $subpage = factory('App\Subpage')->create();
        $post = $this->generatePost(true, 'url');
        $response = $this->json('POST','/'.$subpage->slug.'/b', $post);
        $response->assertOk();
        Storage::disk('public')->assertExists(BookPost::first()->cover->url);
        $response->assertJson([
            'success' => true,
            'post' => [
                'title' => $post['title'],
                'author' => $post['author'],
                'city' => $post['city'],
                'year' => $post['year']
            ]
        ]);
        $this->assertCount(1, BookPost::all());
        $this->assertEquals($post['title'], BookPost::first()->title);
        $this->assertEquals(BookPost::first(), Subpage::first()->posts()->first());
        return $response->json('post.url');
    }

    /** @test */
    public function a_bP_can_be_updated_with_cover_from_url()
    {
        $oldPost = $this->a_bP_can_be_created_with_cover_from_url();
        $newPost = $this->generatePost(true, 'url');

        $response = $this->json('PATCH', $oldPost, $newPost);
        $response->assertOk();
        Storage::disk('public')->assertExists(BookPost::first()->cover->url);
        $response->assertJson([
            'success' => true,
            'post' => [
                'title' => $newPost['title'],
                'author' => $newPost['author'],
                'city' => $newPost['city'],
                'year' => $newPost['year']
            ]
        ]);
        $this->assertCount(1, BookPost::all());
        $this->assertEquals($newPost['title'], BookPost::first()->title);
        $this->assertEquals(BookPost::first(), Subpage::first()->posts()->first());
    }

    /** @test */
    public function a_bP_can_be_updated_with_cover_other_type()
    {
        $oldPost = $this->a_bP_can_be_created_with_cover_from_url();
        $newPost = $this->generatePost(true, 'file');
        $file = $newPost['coverFile'];

        $response = $this->json('PATCH', $oldPost, $newPost);
        $response->assertOk();
        Storage::disk('public')->assertExists('covers/'.$file->hashName());
        $response->assertJson([
            'success' => true,
            'post' => [
                'title' => $newPost['title'],
                'author' => $newPost['author'],
                'city' => $newPost['city'],
                'year' => $newPost['year']
            ]
        ]);
        $this->assertCount(1, BookPost::all());
        $this->assertEquals($newPost['title'], BookPost::first()->title);
        $this->assertEquals(BookPost::first(), Subpage::first()->posts()->first());

        $oldPost = $response->json('post.url');
        $newPost = $this->generatePost(true, 'url');

        $response = $this->json('PATCH', $oldPost, $newPost);
        $response->assertOk();
        Storage::disk('public')->assertExists(BookPost::first()->cover->url);
        $response->assertJson([
            'success' => true,
            'post' => [
                'title' => $newPost['title'],
                'author' => $newPost['author'],
                'city' => $newPost['city'],
                'year' => $newPost['year']
            ]
        ]);
        $this->assertCount(1, BookPost::all());
        $this->assertEquals($newPost['title'], BookPost::first()->title);
        $this->assertEquals(BookPost::first(), Subpage::first()->posts()->first());
    }

    /** @test */
    public function a_bP_can_be_created_with_pdf()
    {
        $subpage = factory('App\Subpage')->create();
        $post = $this->generatePost(false, '', true);
        $file = $post['pdf'];

        $response = $this->json('POST','/'.$subpage->slug.'/b', $post);
        $response->assertOk();
        Storage::disk('public')->assertExists('pdfs/'.$file->hashName());
        $response->assertJson([
            'success' => true,
            'post' => [
                'title' => $post['title'],
                'author' => $post['author'],
                'city' => $post['city'],
                'year' => $post['year'],
                'pdf' => 'pdfs/'.$file->hashName()
            ]
        ]);
        $this->assertCount(1, BookPost::all());
        $this->assertEquals($post['title'], BookPost::first()->title);
        $this->assertEquals(BookPost::first(), Subpage::first()->posts()->first());
        return BookPost::first();
    }

    /** @test */
    public function a_bP_can_be_created_with_pdf_and_cover()
    {
        $subpage = factory('App\Subpage')->create();
        $post = $this->generatePost(true, 'file', true);
        $file = $post['coverFile'];
        $pdf = $post['pdf'];

        $response = $this->json('POST','/'.$subpage->slug.'/b', $post);
        $response->assertOk();
        Storage::disk('public')->assertExists('covers/'.$file->hashName());
        Storage::disk('public')->assertExists('pdfs/'.$pdf->hashName());
        $response->assertJson([
            'success' => true,
            'post' => [
                'title' => $post['title'],
                'author' => $post['author'],
                'city' => $post['city'],
                'year' => $post['year']
            ]
        ]);
        $this->assertCount(1, BookPost::all());
        $this->assertEquals($post['title'], BookPost::first()->title);
        $this->assertEquals(BookPost::first(), Subpage::first()->posts()->first());
        return BookPost::first();
    }

    /** @test */
    public function a_bP_can_be_updated_with_pdf()
    {
        $oldPost = $this->a_bP_can_be_created_with_pdf();
        $newPost = $this->generatePost(false, 'file', true);
        $file = $newPost['pdf'];

        $response = $this->json('PATCH', $oldPost->url, $newPost);
        $response->assertOk();
        Storage::disk('public')->assertExists('pdfs/'.$file->hashName());
        $response->assertJson([
            'success' => true,
            'post' => [
                'title' => $newPost['title'],
                'author' => $newPost['author'],
                'city' => $newPost['city'],
                'year' => $newPost['year'],
                'pdf' => 'pdfs/'.$file->hashName()
            ]
        ]);
        $this->assertCount(1, BookPost::all());
        $this->assertEquals($newPost['title'], BookPost::first()->title);
        $this->assertEquals(BookPost::first(), Subpage::first()->posts()->first());
    }

    /** @test */
    public function a_bP_can_be_updated_with_pdf_and_cover()
    {
        $oldPost = $this->a_bP_can_be_created_with_cover_from_file();
        $newPost = $this->generatePost(true, 'file', true);
        $file = $newPost['coverFile'];
        $pdf = $newPost['pdf'];


        $response = $this->json('PATCH', $oldPost->url, $newPost);
        $response->assertOk();
        Storage::disk('public')->assertExists('covers/'.$file->hashName());
        Storage::disk('public')->assertExists('pdfs/'.$pdf->hashName());
        $response->assertJson([
            'success' => true,
            'post' => [
                'title' => $newPost['title'],
                'author' => $newPost['author'],
                'city' => $newPost['city'],
                'year' => $newPost['year'],
                'pdf' => 'pdfs/'.$pdf->hashName()
            ]
        ]);
        $this->assertCount(1, BookPost::all());
        $this->assertEquals($newPost['title'], BookPost::first()->title);
        $this->assertEquals(BookPost::first(), Subpage::first()->posts()->first());
    }

    /** @test */
    public function a_cover_can_be_removed_from_bP()
    {
        $post = $this->a_bP_can_be_created_with_cover_from_file();
        $this->assertCount(1, Image::all());
        $response = $this->json('PATCH', $post->url, [
            'title' => $post->title,
            'author' => $post->author,
            'removeCover' => true,
        ])->assertOk();
        $this->assertNotInstanceOf(Image::class, BookPost::first()->cover);
        $response->assertJson([
            'success' => true,
            'post' => [
                'title' => $post->title,
                'author' => $post->author,
                'city' => $post->city,
                'year' => (string) $post->year,
                'cover' => null
            ]
        ]);
    }

    /** @test */
    public function a_pdf_can_be_removed_from_bP()
    {
        $post = $this->a_bP_can_be_created_with_pdf();
        $response = $this->json('PATCH', $post->url, [
            'title' => $post->title,
            'author' => $post->author,
            'removePdf' => true,
        ]);
        $response->assertOk()->assertJson([
            'success' => true,
            'post' => [
                'title' => $post->title,
                'author' => $post->author,
                'city' => $post->city,
                'year' => (string) $post->year,
                'pdf' => null
            ]
        ]);
    }

    /** @test */
    public function a_cover_and_pdf_can_be_removed_from_bP()
    {
        $post = $this->a_bP_can_be_created_with_pdf_and_cover();
        $response = $this->json('PATCH', $post->url, [
            'title' => $post->title,
            'author' => $post->author,
            'removePdf' => true,
            'removeCover' => true
        ]);
        $response->assertOk()->assertJson([
            'success' => true,
            'post' => [
                'title' => $post->title,
                'author' => $post->author,
                'city' => $post->city,
                'year' => (string) $post->year,
                'pdf' => null,
                'cover' => null
            ]
        ]);
    }

    /** @test */
    public function a_bP_cannot_be_created_with_both_covers()
    {
        $subpage = factory('App\Subpage')->create();
        $post = $this->generatePost(true, 'all');
        $response = $this->json('POST', '/'.$subpage->slug.'/b', $post);
        $response->assertStatus(422)->assertJsonValidationErrors(['coverFile', 'coverUrl']);
    }

    /** @test */
    public function a_bP_cannot_have_duplicates()
    {
        $subpage = factory('App\Subpage')->create();
        $post = $this->generatePost();

        $response = $this->json('POST', '/'.$subpage->slug.'/b', $post);
        $response->assertOk()->assertJson([
            'success' => true,
            'post' => $post
        ]);
        $this->assertCount(1, BookPost::all());

        $response = $this->json('POST', '/'.$subpage->slug.'/b', $post);
        $response->assertStatus(422)->assertJsonValidationErrors(['title']);
        $this->assertCount(1, BookPost::all());

        $post['title'] = $this->faker->sentence;
        $response = $this->json('POST', '/'.$subpage->slug.'/b', $post);
        $response->assertOk()->assertJson([
            'success' => true,
            'post' => $post
        ]);
        $this->assertCount(2, BookPost::all());

    }
}
