<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\TenantController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\Admin\UsersController as AdminUserController;
use App\Http\Controllers\Api\CnpjController;
use App\Http\Controllers\Api\DailyRegistersController;
use App\Http\Controllers\Api\PartnersController;
use App\Http\Controllers\Api\ManagerPanelController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\SquadsController;
use App\Http\Controllers\Api\TrafficsController;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/
// Rotas da API com middleware de tenant e autenticação
Route::middleware([
    'jwt.auth',
    'tenant'
])->prefix('api')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/users', [UsersController::class, 'index']);
    Route::post('/users', [UsersController::class, 'store']);
    Route::get('/users/{id}', [UsersController::class, 'show']);
    Route::put('/users/{id}', [UsersController::class, 'update']);
    Route::delete('/users/{id}', [UsersController::class, 'destroy']);
    
    Route::get('/partners', [PartnersController::class, 'index']);
    Route::post('/partners', [PartnersController::class, 'store']);
    Route::get('/partners/{id}', [PartnersController::class, 'show']);
    Route::put('/partners/{id}', [PartnersController::class, 'update']);
    Route::delete('/partners/{id}', [PartnersController::class, 'destroy']);

    Route::get('/squads', [SquadsController::class, 'index']);
    Route::post('/squads', [SquadsController::class, 'store']);
    Route::get('/squads/{id}', [SquadsController::class, 'show']);
    Route::put('/squads/{id}', [SquadsController::class, 'update']);
    Route::delete('/squads/{id}', [SquadsController::class, 'destroy']);

    Route::get('/traffics', [TrafficsController::class, 'index']);
    Route::post('/traffics', [TrafficsController::class, 'store']);
    Route::put('/traffics/{id}', [TrafficsController::class, 'update']);

        Route::get('/daily-registers', [DailyRegistersController::class, 'index']);
    Route::post('/daily-registers', [DailyRegistersController::class, 'store']);
    Route::put('/daily-registers/{id}', [DailyRegistersController::class, 'update']);

    Route::get('/roles', [RolesController::class, 'index']);

    Route::get('/manager-panel', [ManagerPanelController::class, 'index']);

    Route::get('/consultar/cnpj/{cnpj}', [CnpjController::class, 'show']);
});

Route::middleware([
    'jwt.auth',
    'check.admin.tenant'
])->prefix('api/admin')->group(function () {
    Route::get('/tenants', [TenantController::class, 'index']);
    Route::post('/tenants', [TenantController::class, 'store']);
    Route::get('/tenants/{id}', [TenantController::class, 'show']);
    Route::put('/tenants/{id}', [TenantController::class, 'update']);

    Route::get('/{tenant}/users', [AdminUserController::class, 'index']);
    Route::post('/{tenant}/users', [AdminUserController::class, 'store']);
    Route::get('/{tenant}/users/{id}', [AdminUserController::class, 'show']);
    Route::put('/{tenant}/users/{id}', [AdminUserController::class, 'update']);
    Route::delete('/{tenant}/users/{id}', [AdminUserController::class, 'destroy']);
});
