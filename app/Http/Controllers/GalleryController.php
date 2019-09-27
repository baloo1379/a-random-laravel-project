<?php

namespace App\Http\Controllers;

use App\Gallery;
use App\NewsPost;
use App\Subpage;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function store(Request $request, Subpage $subpage, NewsPost $post)
    {
        $gallery = $post->galleries()->create();
        foreach ($request->gallery as $item) {
            $url = $item->store('gallery', 'public');
            $gallery->images()->create(['url'=>$url]);
        }

        return response()->json([
            'success' => true,
            'gallery' => $gallery->images
        ]);
    }

    public function show(Gallery $gallery)
    {
        //
    }

    public function update(Request $request, Gallery $gallery)
    {
        //
    }

    public function destroy(Gallery $gallery)
    {
        //
    }
}
