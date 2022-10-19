<?php

namespace App\Repositories;

use App\Interfaces\EditorRepositoryInterface;
use App\Models\v1\Editor;

class EditorRepository implements EditorRepositoryInterface 
{

    public function getAllEditors()
    {
        return Editor::orderBy('editor_id', 'asc')->get();
    }
}