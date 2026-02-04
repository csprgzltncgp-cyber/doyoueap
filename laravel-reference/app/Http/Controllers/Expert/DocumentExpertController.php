<?php

namespace App\Http\Controllers\Expert;

use App\Models\Document;

class DocumentExpertController extends BaseExpertController
{
    //
    //
    public function view($id)
    {
        $document = Document::query()->findOrFail($id);

        return view('expert.documents.view', ['document' => $document]);
    }
}
