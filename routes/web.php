<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ShipmentsController;
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
    return view('welcome');
});



// Route for creating bulk shipments
Route::post('/shipments/bulk', [ShipmentsController::class, 'createBulkShipment']);

// Route for handling shipment events
Route::post('/shipments/{shipment_id}/events', [ShipmentsController::class, 'handleShipmentEvents']);

// Route for getting a shipment's status
Route::get('/shipments/{shipment_id}/track', [ShipmentsController::class, 'getShipmentStatus']);

// Route for retrieving a specific shipment
Route::get('/shipments/{shipment_id}', [ShipmentsController::class, 'getShipment']);
