<?php

use App\Http\Controllers\InodeController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'inodes'], function() {
    Route::get('/{inode}/files/filter', [InodeController::class, 'filter_nested_files'])->name('filter_nested_files');
    Route::get('/{inode}',              [InodeController::class, 'list_contents'])->name('list_contents');
    Route::post('/{inode}/files',       [InodeController::class, 'create_file'])->name('create_file');
    Route::post('/{inode}/directories', [InodeController::class, 'create_directory'])->name('create_directory');
    Route::delete('/{inode}',           [InodeController::class, 'delete_inode'])->name('delete_inode');
});
