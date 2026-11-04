<?php

namespace App\Http\Controllers;

use App\Actions\CreateDirectoryAction;
use App\Actions\CreateFileAction;
use App\Actions\DeleteInodeAction;
use App\Actions\FilterNestedFilesAction;
use App\Actions\ListContentsAction;
use App\Exceptions\ValidationException;
use App\Http\Requests\CreateDirectoryRequest;
use App\Http\Requests\CreateFileRequest;
use App\Http\Requests\FilterNestedFilesRequest;
use App\Http\Resources\FilteredInodeResource;
use App\Http\Resources\InodeContentsResource;
use App\Models\Inode;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

class InodeController
{

    ///////////////////////////////////////////////////////////////////////////
    public function filter_nested_files(Inode $inode, FilterNestedFilesRequest $request, FilterNestedFilesAction $action): Response {
        $files = $action->execute($inode, $request->getSearchParam());

        return response()->make(FilteredInodeResource::collection($files));
    }

    ///////////////////////////////////////////////////////////////////////////
    /**
     * @throws ModelNotFoundException inode not found
     * @throws ValidationException
     */
    public function list_contents(Inode $inode, ListContentsAction $action): Response {
        $action->execute($inode);

        return response()->make(InodeContentsResource::make($inode));
    }

    ///////////////////////////////////////////////////////////////////////////
    /**
     * @throws ModelNotFoundException inode not found
     * @throws ValidationException
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
     * @throws ValidationException
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
     * @throws ValidationException
     */
    public function delete_inode(Inode $inode, DeleteInodeAction $action): Response {
        $action->execute($inode);
        
        return response()->make()->setStatusCode(Response::HTTP_NO_CONTENT);
    }
    
}
