<?php

use App\Http\Controllers\InodeController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'inodes'], function() {
    Route::post('/{inode}/files',       [InodeController::class, 'create_file'])->name('create_file');
    Route::post('/{inode}/directories', [InodeController::class, 'create_directory'])->name('create_directory');
    Route::patch('/{inode}',            [InodeController::class, 'rename_inode'])->name('rename_inode');
});
