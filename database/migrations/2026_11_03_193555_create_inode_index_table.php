<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    ///////////////////////////////////////////////////////////////////////////
    /**
     * The inodes table keeps track of direct parent-child relationship,
     * but the directory search is supposed to work in a nested way
     * (i.e. matching files of subdirectories).
     * To achieve this, the full parent-child tree should be stored in
     * a separate intermediate table in a way that each directory inode
     * stored as parent_id has corresponding records for all its inodes,
     * its inodes inodes etc. using the child_id column.
     */
    public function up(): void {
        Schema::create('inode_index', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id');
            $table->foreign('parent_id')->references('id')->on('inodes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('child_id');
            $table->foreign('child_id')->references('id')->on('inodes')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    ///////////////////////////////////////////////////////////////////////////
    public function down(): void {
        Schema::dropIfExists('inode_index');
    }
};
