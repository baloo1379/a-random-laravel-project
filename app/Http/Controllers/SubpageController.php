<?php

namespace App\Http\Controllers;

use App\Subpage;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class SubpageController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $attr = $this->validation($request);
        $attr['slug'] = Str::slug($attr['name'], '-');
        $subpage = Subpage::create($attr);
        return response()->json([
            'success' => true,
            'subpage' => $subpage
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Subpage $subpage
     * @return Response
     */
    public function show(Subpage $subpage)
    {
        return response()->json([
            'subpage' => $subpage,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Subpage $subpage
     * @return Response
     */
    public function update(Request $request, Subpage $subpage)
    {
        $attr = $this->validation($request);
        $attr['slug'] = Str::slug($attr['name'], '-');
        $subpage->update($attr);
        return response()->json([
            'success' => true,
            'subpage' => $subpage
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Subpage $subpage
     * @return Response
     * @throws Exception
     */
    public function destroy(Subpage $subpage)
    {
        $subpage->delete();
        return response()->json(['success' => true]);
    }


    private function validation(Request $request)
    {
        return $request->validate([
            'name' => 'string|min:3|unique:subpages'
        ]);
    }
}
