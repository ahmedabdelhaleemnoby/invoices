<?php

namespace App\Http\Controllers;

use App\Models\invoices;
use App\Models\InvoicesAttachments;
use App\Models\InvoicesDetails;
use App\Models\Products;
use App\Models\sections;
use App\Models\User;
use App\Notifications\AddInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InvoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    function __construct()
    {
        $this->middleware('permission:الفواتير', ['only' => ['index']]);
        $this->middleware('permission:اضافة فاتورة', ['only' => ['create', 'store']]);
        $this->middleware('permission:تعديل الفاتورة', ['only' => ['edit', 'update']]);
        $this->middleware('permission:حذف المرفق', ['only' => ['destroy']]);
        $this->middleware('permission:طباعةالفاتورة', ['only' => ['Print_invoice']]);
        $this->middleware('permission:تغير حالة الدفع', ['only' => ['Status_Update']]);
        $this->middleware('permission:الفواتير المدفوعة', ['only' => ['Invoice_Paid']]);
        $this->middleware('permission:الفواتير الغير مدفوعة', ['only' => ['Invoice_unPaid']]);
        $this->middleware('permission:الفواتير المدفوعة جزئيا', ['only' => ['Invoice_Partial']]);
        $this->middleware('permission:ارشيف الفواتير', ['only' => ['Archive_index']]);
    }
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

        // $user = User::first();
        // Notification::send($user, new AddInvoice($invoice_id));

        //    $user = User::get();
        //    $invoices = invoices::latest()->first();
        //    Notification::send($user, new \App\Notifications\Add_invoice_new($invoices));

        return redirect('/invoices')->with('Add', 'تم إضافة الفاتورة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show($invoice)
    {
        $invoices = invoices::findOrFail($invoice);
        // return $invoices;
        return view('invoices.status_update', compact('invoices'));
    }

    public function Print_invoice($id)
    {
        $invoices = invoices::findOrFail($id);
        return view('invoices.Print_invoice', compact('invoices'));
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
        // return $request;
        $invoices = invoices::where('id', $request->invoice_id)->first();
        $Details = InvoicesDetails::where('id_Invoice', $request->invoice_id)->first();
        $id_page = $request->id_page;


        if (!$id_page == 2) {

            // return $Details->invoice_number;
            if (!empty($Details->invoice_number)) {
                Storage::disk('public_uploads')->deleteDirectory($Details->invoice_number);
            }

            $invoices->forceDelete();
            return redirect('/invoices')->with('delete_invoice', 'تم حذف الفاتورة بنجاح');
        } else {

            $invoices->delete();
            return redirect('/Archive')->with('archive_invoice', 'تم حذف الفاتورة بنجاح');
        }
        return redirect('/invoices')->with('delete_invoice', 'تم حذف الفاتورة بنجاح');
    }
    public function destroy2(Request $request)
    {
        // return 'destroy2';
        $invoices = invoices::withTrashed()->where('id', $request->invoice_id)->first();
        $Details = InvoicesDetails::where('id_Invoice', $request->invoice_id)->first();
        if (!empty($Details->invoice_number)) {
            Storage::disk('public_uploads')->deleteDirectory($Details->invoice_number);
        }
        $invoices->forceDelete();
        return redirect('/Archive')->with('delete_invoice', 'تم حذف الفاتورة بنجاح');
    }
    public function Status_Update($id, Request $request)
    {
        // return $request->invoice_id;
        $invoices = invoices::findOrFail($id);

        if ($request->Status === 'مدفوعة') {

            $invoices->update([
                'Value_Status' => 1,
                'Status' => $request->Status,
                'Payment_Date' => $request->Payment_Date,
            ]);

            InvoicesDetails::create([
                'id_Invoice' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'Section' => $request->Section,
                'Status' => $request->Status,
                'Value_Status' => 1,
                'note' => $request->note,
                'Payment_Date' => $request->Payment_Date,
                'user' => (Auth::user()->name),
            ]);
        } else {
            $invoices->update([
                'Value_Status' => 3,
                'Status' => $request->Status,
                'Payment_Date' => $request->Payment_Date,
            ]);
            InvoicesDetails::create([
                'id_Invoice' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'Section' => $request->Section,
                'Status' => $request->Status,
                'Value_Status' => 3,
                'note' => $request->note,
                'Payment_Date' => $request->Payment_Date,
                'user' => (Auth::user()->name),
            ]);
        }
        return redirect('/invoices')->with('Status_Update', 'تم نعديل حالة الفاتورة بنجاح');
    }

    public function Invoice_Paid()
    {
        // return 'Invoice_Paid';
        $invoices = Invoices::where('Value_Status', 1)->get();
        return view('invoices.invoices_paid', compact('invoices'));
    }

    public function Invoice_unPaid()
    {
        $invoices = Invoices::where('Value_Status', 2)->get();
        return view('invoices.invoices_unpaid', compact('invoices'));
    }

    public function Invoice_Partial()
    {
        $invoices = Invoices::where('Value_Status', 3)->get();
        return view('invoices.invoices_Partial', compact('invoices'));
    }
    public function Archive_index()
    {
        $invoices = invoices::onlyTrashed()->get();
        return view('Invoices.Archive_Invoices', compact('invoices'));
    }
    public function Archive_update(Request $request)
    {
        // return $request;
        $id = $request->invoice_id;
        $flight = Invoices::withTrashed()->where('id', $id)->restore();
        return redirect('/invoices')->with('restore_invoice', 'تم نعديل حالة الفاتورة بنجاح');
    }
}
