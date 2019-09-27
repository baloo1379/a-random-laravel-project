<?php

namespace Tests\Feature;

use App\Gallery;
use App\Image;
use App\NewsPost;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GalleryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private function generateGallery()
    {
        $gallery = array();
        for ($i=0; $i<rand(2, 5); $i++)
        {
            array_push($gallery, UploadedFile::fake()->image("gallery-$i.jpg"));
        }
        return $gallery;
    }

    /** @test */
    public function a_gallery_can_be_created()
    {
        $this->withoutExceptionHandling();
        $gallery = $this->generateGallery();
        $post = factory('App\NewsPost')->create();
        $response = $this->json('POST', $post->url.'/gallery', ['gallery' => $gallery]);
        $this->assertInstanceOf(Gallery::class, NewsPost::first()->galleries()->first());
        $this->assertInstanceOf(Image::class, NewsPost::first()->galleries()->first()->images()->first());
        $gallery_model = NewsPost::first()->galleries()->first()->images->toArray();
        $this->assertCount(sizeof($gallery), $gallery_model);
        $response->assertOk()->assertJson([
            'success' => true,
            'gallery' => $gallery_model
        ]);
    }

    /** @test */
    public function a_multiple_galleries_can_be_created()
    {
        $this->withoutExceptionHandling();
        $gallery1 = $this->generateGallery();
        $gallery2 = $this->generateGallery();
        $post = factory('App\NewsPost')->create();
        $response1 = $this->json('POST', $post->url.'/gallery', ['gallery' => $gallery1]);
        $response2 = $this->json('POST', $post->url.'/gallery', ['gallery' => $gallery2]);
        $post = NewsPost::first();
        $this->assertInstanceOf(Gallery::class, $post->galleries()->first());
        $this->assertInstanceOf(Gallery::class, $post->galleries()->skip(1)->first());
        $this->assertInstanceOf(Image::class, $post->galleries()->first()->images()->first());
        $gallery_model_one = $post->galleries()->first()->images->toArray();
        $gallery_model_two = $post->galleries()->skip(1)->first()->images->toArray();
        $this->assertCount(sizeof($gallery1), $gallery_model_one);
        $response1->assertOk()->assertJson([
            'success' => true,
            'gallery' => $gallery_model_one
        ]);
        $response2->assertOk()->assertJson([
            'success' => true,
            'gallery' => $gallery_model_two
        ]);
    }
}
