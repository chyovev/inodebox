<?php

namespace App\Http\Controllers;

use App\Actions\CreateDirectoryAction;
use App\Actions\CreateFileAction;
use App\Actions\RenameInodeAction;
use App\Http\Requests\CreateDirectoryRequest;
use App\Http\Requests\CreateFileRequest;
use App\Http\Requests\RenameInodeRequest;
use App\Models\Inode;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

class InodeController
{

    ///////////////////////////////////////////////////////////////////////////
    /**
     * @throws ModelNotFoundException inode not found
     */
    public function create_file(Inode $inode, CreateFileRequest $request, CreateFileAction $action): Response {
        $action->execute($inode, $request->getFileName());

        // no need to return any data of the newly created
        // file as front-end is expected to refresh the
        // contents of the current inode to have it
        // listed in alphabetical order
        return response()->make()->setStatusCode(Response::HTTP_CREATED);
    }

    ///////////////////////////////////////////////////////////////////////////
    /**
     * @throws ModelNotFoundException inode not found
     */
    public function create_directory(Inode $inode, CreateDirectoryRequest $request, CreateDirectoryAction $action): Response {
        $action->execute($inode, $request->getDirName());

        // no need to return any data of the newly created
        // directory as front-end is expected to refresh the
        // contents of the current inode to have it
        // listed in alphabetical order
        return response()->make()->setStatusCode(Response::HTTP_CREATED);
    }

    ///////////////////////////////////////////////////////////////////////////
    /**
     * @throws ModelNotFoundException inode not found
     */
    public function rename_inode(Inode $inode, RenameInodeRequest $request, RenameInodeAction $action): Response {
        $action->execute($inode, $request->getName());

        // no need to return any data of the renamed inode as
        // front-end is expected to refresh the contents of the
        // parent inode to have it listed in alphabetical order
        return response()->make();
    }
    
}
