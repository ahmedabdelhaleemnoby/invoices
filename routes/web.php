<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\InvoicesAttachmentsController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\InvoicesDetailsController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SectionsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('index');
})->middleware(['auth', 'verified']);

Route::get('/dashboard', function () {
    return view('index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
Route::group(['middleware' => ['auth']], function () {
    Route::resource('invoices', InvoicesController::class);
    Route::resource('sections', SectionsController::class);
    Route::resource('products', ProductsController::class);
    Route::resource('attachments', InvoicesAttachmentsController::class);
    Route::get('/InvoicesDetails/{id}', [InvoicesDetailsController::class, 'edit']);
    Route::post('/Status_Update/{id}', [InvoicesController::class, 'Status_Update'])->name('Status_Update');
    Route::post('attachments.store', [InvoicesAttachmentsController::class, 'store'])->name('attachments.store');
    Route::post('delete_file', [InvoicesAttachmentsController::class, 'destroy'])->name('delete_file');
    Route::get('/section/{id}', [InvoicesController::class, 'getProducts']);
    Route::get('/InvoicesDetails/{id}', [InvoicesDetailsController::class, 'edit']);
    Route::get('View_file/{invoice_number}/{file_name}', [InvoicesAttachmentsController::class, 'open_file']);
    Route::get('download/{invoice_number}/{file_name}', [InvoicesAttachmentsController::class, 'download_file']);
    Route::get('Invoice_Paid', [InvoicesController::class, 'Invoice_Paid']);
    Route::get('Invoice_UnPaid', [InvoicesController::class, 'Invoice_UnPaid']);
    Route::get('Invoice_Partial', [InvoicesController::class, 'Invoice_Partial']);
    Route::post('Archive_update', [InvoicesController::class, 'Archive_update'])->name('Archive_update');
    Route::get('Print_invoice/{id}', [InvoicesController::class, 'Print_invoice'])->name('Print_invoice');
    Route::post('invoices.destroy2', [InvoicesController::class, 'destroy2'])->name('invoices.destroy2');
    Route::get('Archive', [InvoicesController::class, 'Archive_index'])->name('Archive_index');
    Route::get('/{page}', [AdminController::class, 'index']);
});
Route::group(['middleware' => ['auth']], function () {
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
});
