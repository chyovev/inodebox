<?php

use App\Http\Controllers\InodeController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'inodes'], function() {
    Route::post('/{inode}/files', [InodeController::class, 'create_file'])->name('create_file');
});
