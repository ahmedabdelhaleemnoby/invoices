<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\sections;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    function __construct()
    {
        $this->middleware('permission:المنتجات', ['only' => ['index']]);
        $this->middleware('permission:اضافة منتج', ['only' => ['create', 'store']]);
        $this->middleware('permission:تعديل منتج', ['only' => ['edit', 'update']]);
        $this->middleware('permission:حذف منتج', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sections = sections::all();
        $products = Products::all();
        return view('settings.products', compact('sections', 'products'));
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
        // return $request;
        $this->validate(
            $request,
            [
                'product_name' => 'required|string|max:255',
            ],
            [

                'product_name.required' => 'يرجي ادخال اسم المنتج',
                // 'product_name.unique' => 'اسم المنتج مسجل مسبقا',

            ]
        );
        $create = new Products();
        $create->product_name = $request->input('product_name');
        $create->description = $request->input('description');
        $create->section_id = $request->input('section_id');
        $create->save();
        return redirect('/products')->with('add', 'تم إضافة المنتج بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Products $products)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Products $products)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Products $products)
    {
        // dd($request);
        // return $request;
        $id = sections::where('section_name', $request->section_name)->first()->id;
        $this->validate(
            $request,
            [
                'product_name' => 'required|max:255',

            ],
            [

                'product_name.required' => 'يرجي ادخال اسم القسم',
                // 'product_name.unique' => 'اسم القسم مسجل مسبقا',

            ]
        );
        $update = Products::findOrFail($request->id);
        $update->product_name = $request->input('product_name');
        $update->description = $request->input('description');
        $update->section_id = $id;
        $update->save();
        return redirect('/products')->with('edit', 'تم تعديل القسم بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $delete = Products::findOrFail($request->id);
        $delete->delete();
        return redirect()->back()->with('delete', 'تم حذف القسم بنجاح');
    }
}
