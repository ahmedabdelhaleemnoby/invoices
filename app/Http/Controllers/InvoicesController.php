<?php

namespace App\Http\Controllers;

use App\Models\invoices;
use App\Models\InvoicesAttachments;
use App\Models\InvoicesDetails;
use App\Models\Products;
use App\Models\sections;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = invoices::all();
        return view('invoices.view', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sections = sections::all();
        return view('invoices.add_invoice', compact('sections'));
    }

    public function getProducts($id)
    {
        $products = Products::where('section_id', $id)->pluck('product_name', 'id');
        return json_decode($products);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // return  $request;
        $createInvoices = new invoices();
        $createInvoices->invoice_number = $request->input('invoice_number');
        $createInvoices->invoice_Date = $request->input('invoice_Date');
        $createInvoices->Due_date = $request->input('Due_date');
        $createInvoices->product = $request->input('product');
        $createInvoices->section_id = $request->input('Section');
        $createInvoices->Amount_collection = $request->input('Amount_collection');
        $createInvoices->Amount_Commission = $request->input('Amount_Commission');
        $createInvoices->Discount = $request->input('Discount');
        $createInvoices->Value_VAT = $request->input('Value_VAT');
        $createInvoices->Rate_VAT = $request->input('Rate_VAT');
        $createInvoices->Total = $request->input('Total');
        $createInvoices->Status = 'غير مدفوعة';
        $createInvoices->Value_Status = 2;
        $createInvoices->note = $request->input('note');
        $createInvoices->save();
        $invoice_id = invoices::latest()->first()->id;
        $createDetails = new InvoicesDetails();
        $createDetails->id_Invoice = $invoice_id;
        $createDetails->invoice_number = $request->input('invoice_number');
        $createDetails->product = $request->input('product');
        $createDetails->Section = $request->input('Section');
        $createDetails->Status = 'غير مدفوعة';
        $createDetails->Value_Status = 2;
        $createDetails->note = $request->input('note');
        $createDetails->user = Auth::user()->name;
        $createDetails->save();
        if ($request->hasFile('pic')) {
            $this->validate($request, [

                'file_name' => 'mimes:pdf,jpeg,png,jpg',

            ], [
                'file_name.mimes' => 'صيغة المرفق يجب ان تكون   pdf, jpeg , png , jpg',
            ]);

            $invoice_id = Invoices::latest()->first()->id;
            $image = $request->file('pic');
            $file_name = $image->getClientOriginalName();
            $invoice_number = $request->invoice_number;

            $attachments = new InvoicesAttachments();
            $attachments->file_name = $file_name;
            $attachments->invoice_number = $invoice_number;
            $attachments->invoice_id = $invoice_id;
            $attachments->Created_by = Auth::user()->name;
            $attachments->save();

            // move pic
            $imageName = $request->pic->getClientOriginalName();
            $request->pic->move(public_path('Attachments/' . $invoice_number), $imageName);
        }

        return redirect('/invoices')->with('Add', 'تم إضافة الفاتورة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(invoices $invoices)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($invoices)
    {
        // return $invoices;
        $invoices = invoices::where('id', $invoices)->first();
        $sections = sections::all();
        return view('invoices.edit_invoice', compact('sections', 'invoices'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $invoices)
    {
        // return $request;
        $updateInvoices = invoices::findOrFail($invoices);
        $updateInvoices->invoice_number = $request->input('invoice_number');
        $updateInvoices->invoice_Date = $request->input('invoice_Date');
        $updateInvoices->Due_date = $request->input('Due_date');
        $updateInvoices->product = $request->input('product');
        $updateInvoices->section_id = $request->input('Section');
        $updateInvoices->Amount_collection = $request->input('Amount_collection');
        $updateInvoices->Amount_Commission = $request->input('Amount_Commission');
        $updateInvoices->Discount = $request->input('Discount');
        $updateInvoices->Value_VAT = $request->input('Value_VAT');
        $updateInvoices->Rate_VAT = $request->input('Rate_VAT');
        $updateInvoices->Total = $request->input('Total');
        $updateInvoices->note = $request->input('note');
        $updateInvoices->save();
        return redirect('/invoices')->with('update', 'تم تعديل الفاتورة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $invoices)
    {
        return $request;
        $invoices = invoices::where('id', $request->invoice_id)->first();
        $Details = InvoicesDetails::where('invoice_id', $invoices)->first();
    }
}
