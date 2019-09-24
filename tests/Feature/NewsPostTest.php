<?php

namespace Tests\Feature;

use App\Image;
use App\NewsPost;
use App\Subpage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class NewsPostTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private function generatePost($withCover = false, $coverType = 'file')
    {
        $post = [
            'title' => $this->faker->sentence,
            'body' => $this->faker->paragraph
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
        return $post;
    }

    /** @test */
    public function a_nP_can_be_created()
    {
        $subpage = factory('App\Subpage')->create();
        $post = $this->generatePost();
        $response = $this->json('POST','/'.$subpage->slug.'/n', $post);
        $response->assertOk()->assertJson([
            'success' => true,
            'post' => $post
        ]);
        $this->assertCount(1, NewsPost::all());
        $this->assertEquals($post['title'], NewsPost::first()->title);
        $this->assertEquals(NewsPost::first(), Subpage::first()->posts()->first());
        $this->json('GET', $response->json('post.url'))->assertOk()->assertJson([[
            'title' => $post['title'],
            'body' => $post['body']
        ]]);
        return $response->json('post.url');
    }

    /** @test */
    public function a_nP_can_be_updated()
    {
        $postRoute = $this->a_nP_can_be_created();
        $newPost = $this->generatePost();

        $response = $this->json('PATCH', $postRoute, $newPost);
        $response->assertOk()->assertJson([
            'success' => true,
            'post' => $newPost
        ]);
        $this->assertCount(1, NewsPost::all());

        $this->json('GET', $response->json('post.url'))->assertOk()->assertJson([[
            'title' => $newPost['title'],
            'body' => $newPost['body']
        ]]);
    }

    /** @test */
    public function a_nP_can_be_deleted()
    {
        $route = $this->a_nP_can_be_created();
        $response = $this->json('DELETE', $route);
        $response->assertOk()->assertJson([
            'success' => true
        ]);
        $this->assertCount(0, NewsPost::all());
        $this->json('GET', $route)->assertStatus(404);
    }

    /** @test */
    public function a_nP_can_be_created_with_cover_from_file()
    {
        $subpage = factory('App\Subpage')->create();
        $post = $this->generatePost(true, 'file');
        $file = $post['coverFile'];

        $response = $this->json('POST','/'.$subpage->slug.'/n', $post);
        $response->assertOk();
        Storage::disk('public')->assertExists('covers/'.$file->hashName());
        $response->assertJson([
            'success' => true,
            'post' => [
                'title' => $post['title'],
                'body' => $post['body']
            ]
        ]);
        $this->assertCount(1, NewsPost::all());
        $this->assertEquals($post['title'], NewsPost::first()->title);
        $this->assertEquals(NewsPost::first(), Subpage::first()->posts()->first());
        return NewsPost::first();
    }

    /** @test */
    public function a_nP_can_be_updated_with_cover_from_file()
    {
        $oldPost = $this->a_nP_can_be_created_with_cover_from_file();
        $newPost = $this->generatePost(true, 'file');
        $file = $newPost['coverFile'];

        $response = $this->json('PATCH', $oldPost->url, $newPost);
        $response->assertOk();
        Storage::disk('public')->assertExists('covers/'.$file->hashName());
        $response->assertJson([
            'success' => true,
            'post' => [
                'title' => $newPost['title'],
                'body' => $newPost['body']
            ]
        ]);
        $this->assertCount(1, NewsPost::all());
        $this->assertEquals($newPost['title'], NewsPost::first()->title);
        $this->assertEquals(NewsPost::first(), Subpage::first()->posts()->first());
    }

    /** @test */
    public function a_nP_can_be_created_with_cover_from_url()
    {
        $subpage = factory('App\Subpage')->create();
        $post = $this->generatePost(true, 'url');
        $response = $this->json('POST','/'.$subpage->slug.'/n', $post);
        $response->assertOk();
        Storage::disk('public')->assertExists(NewsPost::first()->cover->url);
        $response->assertJson([
            'success' => true,
            'post' => [
                'title' => $post['title'],
                'body' => $post['body']
            ]
        ]);
        $this->assertCount(1, NewsPost::all());
        $this->assertEquals($post['title'], NewsPost::first()->title);
        $this->assertEquals(NewsPost::first(), Subpage::first()->posts()->first());
        return $response->json('post.url');
    }

    /** @test */
    public function a_nP_can_be_updated_with_cover_from_url()
    {
        $oldPost = $this->a_nP_can_be_created_with_cover_from_url();
        $newPost = $this->generatePost(true, 'url');

        $response = $this->json('PATCH',$oldPost, $newPost);
        $response->assertOk();
        Storage::disk('public')->assertExists(NewsPost::first()->cover->url);
        $response->assertJson([
            'success' => true,
            'post' => [
                'title' => $newPost['title'],
                'body' => $newPost['body']
            ]
        ]);
        $this->assertCount(1, NewsPost::all());
        $this->assertEquals($newPost['title'], NewsPost::first()->title);
        $this->assertEquals(NewsPost::first(), Subpage::first()->posts()->first());
    }

    /** @test */
    public function a_nP_can_be_updated_with_cover_other_type()
    {
        $oldPost = $this->a_nP_can_be_created_with_cover_from_url();
        $newPost = $this->generatePost(true, 'file');
        $file = $newPost['coverFile'];

        $response = $this->json('PATCH', $oldPost, $newPost);
        $response->assertOk();
        Storage::disk('public')->assertExists('covers/'.$file->hashName());
        $response->assertJson([
            'success' => true,
            'post' => [
                'title' => $newPost['title'],
                'body' => $newPost['body']
            ]
        ]);
        $this->assertCount(1, NewsPost::all());
        $this->assertEquals($newPost['title'], NewsPost::first()->title);
        $this->assertEquals(NewsPost::first(), Subpage::first()->posts()->first());

        $oldPost = $response->json('post.url');
        $newPost = $this->generatePost(true, 'url');

        $response = $this->json('PATCH', $oldPost, $newPost);
        $response->assertOk();
        Storage::disk('public')->assertExists(NewsPost::first()->cover->url);
        $response->assertJson([
            'success' => true,
            'post' => [
                'title' => $newPost['title'],
                'body' => $newPost['body']
            ]
        ]);
        $this->assertCount(1, NewsPost::all());
        $this->assertEquals($newPost['title'], NewsPost::first()->title);
        $this->assertEquals(NewsPost::first(), Subpage::first()->posts()->first());
    }

    /** @test */
    public function a_cover_can_be_removed_from_nP()
    {
        $post = $this->a_nP_can_be_created_with_cover_from_file();
        $this->assertCount(1, Image::all());
        $response = $this->json('PATCH', $post->url, [
            'title' => $post['title'],
            'removeCover' => true,
        ]);
        $this->assertNotInstanceOf(Image::class, NewsPost::first()->cover);
        $response->assertOk()->assertJson([
            'success' => true,
            'post' => [
                'title' => $post['title'],
                'cover' => null
            ]
        ]);
    }

    /** @test */
    public function a_nP_cannot_be_created_with_both_covers()
    {
        $subpage = factory('App\Subpage')->create();
        $post = $this->generatePost(true, 'all');
        $response = $this->json('POST', '/'.$subpage->slug.'/n', $post);
        $response->assertStatus(422)->assertJsonValidationErrors(['coverFile', 'coverUrl']);
    }

    /** @test */
    public function a_nP_cannot_have_duplicates()
    {
        $subpage = factory('App\Subpage')->create();
        $post = $this->generatePost();

        $response = $this->json('POST', '/'.$subpage->slug.'/n', $post);
        $response->assertOk()->assertJson([
            'success' => true,
            'post' => $post
        ]);
        $this->assertCount(1, NewsPost::all());

        $response = $this->json('POST', '/'.$subpage->slug.'/n', $post);
        $response->assertStatus(422)->assertJsonValidationErrors(['title']);
        $this->assertCount(1, NewsPost::all());

        $post['title'] = $this->faker->sentence;
        $response = $this->json('POST', '/'.$subpage->slug.'/n', $post);
        $response->assertOk()->assertJson([
            'success' => true,
            'post' => $post
        ]);
        $this->assertCount(2, NewsPost::all());

    }
}
