<?php

namespace App\Http\Controllers;

use App\Models\sections;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function GuzzleHttp\Promise\inspect;

class SectionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sections = sections::all();
        return view('settings.sections', compact('sections'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate(
            $request,
            [
                'section_name' => 'required|string|unique:sections|max:255',
            ],
            [

                'section_name.required' => 'يرجي ادخال اسم القسم',
                'section_name.unique' => 'اسم القسم مسجل مسبقا',

            ]
        );
        $create = new sections();
        $create->section_name = $request->input('section_name');
        $create->description = $request->input('description');
        $create->created_by = (Auth::user()->name);
        $create->save();
        return redirect('/sections')->with('add', 'تم إضافة القسم بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(sections $sections)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(sections $sections)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $this->validate(
            $request,
            [
                'section_name' => 'required|max:255|unique:sections,section_name,' . $request->id,

            ],
            [

                'section_name.required' => 'يرجي ادخال اسم القسم',
                'section_name.unique' => 'اسم القسم مسجل مسبقا',

            ]
        );
        $update = sections::findOrFail($request->id);
        $update->section_name = $request->input('section_name');
        $update->description = $request->input('description');
        $update->created_by = (Auth::user()->name);
        $update->save();
        return redirect('/sections')->with('edit', 'تم تعديل القسم بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        // return $request;
        $delete = sections::findOrFail($request->id);
        $delete->delete();
        return redirect()->back()->with('delete', 'تم حذف القسم بنجاح');
    }
}
