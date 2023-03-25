<?php

namespace App\Http\Controllers;

use App\Models\InvoicesAttachments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InvoicesAttachmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $this->validate($request, [

            'file_name' => 'mimes:pdf,jpeg,png,jpg',

        ], [
            'file_name.mimes' => 'صيغة المرفق يجب ان تكون   pdf, jpeg , png , jpg',
        ]);

        $image = $request->file('file_name');
        $file_name = $image->getClientOriginalName();

        $attachments =  new InvoicesAttachments();
        $attachments->file_name = $file_name;
        $attachments->invoice_number = $request->invoice_number;
        $attachments->invoice_id = $request->invoice_id;
        $attachments->Created_by = Auth::user()->name;
        $attachments->save();

        // move pic
        $imageName = $request->file_name->getClientOriginalName();
        $request->file_name->move(public_path('Attachments/' . $request->invoice_number), $imageName);

        return redirect()->back()->with('Add', 'تم إضافة المرفق بنجاح');;
    }
    public function open_file($invoice_number, $file_name)

    {
        $path = public_path() . '/Attachments';
        $files = $path . '/' . $invoice_number . '/' . $file_name;
        return response()->file($files);
    }
    public function download_file($invoice_number, $file_name)

    {
        $path = public_path() . '/Attachments';
        $contents = $path . '/' . $invoice_number . '/' . $file_name;
        return response()->download($contents);
    }
    /**
     * Display the specified resource.
     */
    public function show(InvoicesAttachments $invoicesAttachments)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InvoicesAttachments $invoicesAttachments)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InvoicesAttachments $invoicesAttachments)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, InvoicesAttachments $invoicesAttachments)
    {
        // return $request;
        $delete = InvoicesAttachments::findOrFail($request->id_file);
        $delete->delete();
        Storage::disk('public_uploads')->delete($request->invoice_number . '/' . $request->file_name);
        return redirect()->back()->with('delete', 'تم حذف المرفق بنجاح');
    }
}
