<?php

namespace App\Http\Controllers;

use App\NewsPost;
use App\BookPost;
use App\Subpage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{
    public function storeNews(Request $request, Subpage $subpage)
    {
        $post = $subpage->newses()->create($this->newsValidate($request, $subpage));
        $post->setCover($request);
        return response()->json([
            'success' => true,
            'post' => $post->refresh()
        ]);
    }

    public function updateNews(Request $request, Subpage $subpage, NewsPost $post)
    {
        $post->update($this->newsValidate($request, $subpage, $post->id));
        $post->setCover($request);
        return response()->json([
            'success' => true,
            'post' => $post->refresh()
        ]);
    }

    public function storeBook(Request $request, Subpage $subpage)
    {
        $post = $subpage->books()->create($this->bookValidate($request, $subpage));
        $post->setCover($request);
        $post->setPdf($request);
        return response()->json([
            'success' => true,
            'post' => $post->refresh()
        ]);
    }

    public function updateBook(Request $request, Subpage $subpage, BookPost $post)
    {
        $post->update($this->bookValidate($request, $subpage, $post->id));
        $post->setCover($request);
        $post->setPdf($request);
        return response()->json([
            'success' => true,
            'post' => $post->refresh()
        ]);
    }

    public function show(Subpage $subpage, $slug)
    {
        $post = $subpage->findPostOrFail($slug)->get();
        return response()->json($post);
    }

    public function destroy(Subpage $subpage, $slug)
    {
        $subpage->findPostOrFail($slug)->delete();
        return response()->json([
            'success' => true,
        ]);
    }

    private function newsValidate(Request $request, $subpage, $post = -1)
    {
        return $request->validate([
            'title' => [
                'required',
                'string',
                Rule::unique('news_posts')->where(function($query) use($subpage) {
                    return $query->where('subpage_id', $subpage->id);
                })->ignore($post),
                Rule::unique('book_posts')->where(function($query) use($subpage) {
                    return $query->where('subpage_id', $subpage->id);
                })->ignore($post)
            ],
            'body' => ['string']
        ]);
    }

    private function bookValidate(Request $request, $subpage, $post = -1)
    {
        return $request->validate([
            'title' => [
                'required',
                'string',
                Rule::unique('news_posts')->where(function($query) use($subpage) {
                    return $query->where('subpage_id', $subpage->id);
                })->ignore($post),
                Rule::unique('book_posts')->where(function($query) use($subpage) {
                    return $query->where('subpage_id', $subpage->id);
                })->ignore($post)
            ],
            'author' => 'required|string',
            'city' => 'nullable|string',
            'year' => 'nullable|string'
        ]);
    }
}
