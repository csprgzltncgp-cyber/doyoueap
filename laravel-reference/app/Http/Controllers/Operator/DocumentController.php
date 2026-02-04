<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Document;

class DocumentController extends Controller
{
    //
    public function view($id)
    {
        $document = Document::query()->findOrFail($id);

        return view('operator.documents.view', ['document' => $document]);
    }
}
