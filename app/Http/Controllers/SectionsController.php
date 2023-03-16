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
        $input = $request->all();
        $b_exists = sections::where('section_name', $input['section_name'])->exists();
        if ($b_exists) {
            session()->flash('error', 'خطأ القسم موجود سابقاً');
            return redirect('/sections');
        } else {
            $addSection = new sections();
            $addSection->section_name = $request->input('section_name');
            $addSection->description = $request->input('description');
            $addSection->created_by = (Auth::user()->name);
            $addSection->save();
            session()->flash('add', 'تم إضافة القسم بنجاح');
            return redirect('/sections');
        }
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
    public function update(Request $request, sections $sections)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(sections $sections)
    {
        //
    }
}
