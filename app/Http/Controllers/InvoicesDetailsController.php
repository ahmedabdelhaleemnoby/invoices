<?php

namespace App\Http\Controllers;

use App\Models\invoices;
use App\Models\InvoicesAttachments;
use App\Models\InvoicesDetails;
use Illuminate\Http\Request;

class InvoicesDetailsController extends Controller
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(InvoicesDetails $invoicesDetails)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($invoicesDetails)
    {
        $invoices = invoices::where('id', $invoicesDetails)->first();
        $details  = InvoicesDetails::where('id_Invoice', $invoicesDetails)->get();
        $attachments  = InvoicesAttachments::where('invoice_id', $invoicesDetails)->get();

        return view('invoices.details_invoice', compact('invoices', 'details', 'attachments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InvoicesDetails $invoicesDetails)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        //
    }
}
