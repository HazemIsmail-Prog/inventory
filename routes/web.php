<?php

use App\Helpers\GetData;
use App\Livewire\InvoicesIndex;
use App\Livewire\ItemsIndex;
use App\Livewire\SuppliersIndex;
use App\Livewire\TransactionsIndex;
use App\Livewire\WarehousesIndex;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth'])->group(function () {
    
    Route::view('profile', 'profile')->name('profile');
    Route::get('/', function () {
        return redirect()->route('items.index');
    })->name('dashboard');
    
    Route::get('/suppliers',SuppliersIndex::class)->name('suppliers.index');
    Route::get('/invoices', InvoicesIndex::class)->name('invoices.index');
    Route::get('/items', ItemsIndex::class)->name('items.index');
    Route::get('/warehouses', WarehousesIndex::class)->name('warehouses.index');
    Route::get('/transactions', TransactionsIndex::class)->name('transactions.index');

    // APIs
    Route::get('/api/warehouses/{warehouse}/items', function (Warehouse $warehouse) {
        return GetData::getAvailableItems($warehouse);
    });

    Route::get('/api/items/getItemsList', function () {
        return GetData::getItemsList();
    });
});

require __DIR__ . '/auth.php';
