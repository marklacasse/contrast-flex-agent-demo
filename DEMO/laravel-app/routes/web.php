<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VulnerableController;

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

Route::get('/', [VulnerableController::class, 'home']);
Route::get('/sql-injection', [VulnerableController::class, 'sqlInjection']);
Route::get('/xss', [VulnerableController::class, 'xssVulnerable']);
Route::get('/file-upload', [VulnerableController::class, 'fileUpload']);
Route::post('/file-upload', [VulnerableController::class, 'fileUpload']);
Route::get('/command-injection', [VulnerableController::class, 'commandInjection']);
Route::get('/path-traversal', [VulnerableController::class, 'pathTraversal']);
Route::get('/info', [VulnerableController::class, 'systemInfo']);
