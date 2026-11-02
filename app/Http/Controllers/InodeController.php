<?php

namespace App\Http\Controllers;

use App\Actions\CreateFileAction;
use App\Http\Requests\CreateFileRequest;
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
    
}
