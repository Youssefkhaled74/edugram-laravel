<?php

use Illuminate\Support\Facades\Route;
use Modules\ParentModule\Http\Controllers\ImpersonationController;

/*
|--------------------------------------------------------------------------
| Impersonation Routes
|--------------------------------------------------------------------------
|
| Routes for parent impersonation functionality (Login as Child)
|
*/

Route::group(['prefix' => 'parent', 'as' => 'parent.', 'middleware' => ['auth', 'parent']], function () {
    
    // Login as child (start impersonation)
    Route::post('impersonation/login/{childId}', [ImpersonationController::class, 'loginAsChild'])
        ->name('impersonation.login');
    
    // Return to parent dashboard (stop impersonation)
    Route::post('impersonation/return', [ImpersonationController::class, 'returnToParent'])
        ->name('impersonation.return');
    
    // Check impersonation status (API endpoint)
    Route::get('impersonation/check', [ImpersonationController::class, 'checkImpersonation'])
        ->name('impersonation.check');
});

