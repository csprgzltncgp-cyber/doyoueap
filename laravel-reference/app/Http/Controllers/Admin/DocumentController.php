<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Document;
use App\Models\Language;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /* INDEX */
    public function index()
    {
        $documents = Document::query()->orderBy('name', 'asc')->get();
        $countries = Country::query()->get();

        return view('admin.documents.list', ['documents' => $documents, 'countries' => $countries]);
    }
    /* INDEX */

    /* EDIT */
    public function edit($id)
    {
        $document = Document::query()->findOrFail($id);
        $countries = Country::query()->get();
        $languages = Language::query()->orderBy('name', 'asc')->get();

        return view('admin.documents.edit', ['document' => $document, 'languages' => $languages, 'countries' => $countries]);
    }

    public function edit_process(Request $request, $id)
    {
        Document::edit($id, $request);

        return redirect()->route('admin.documents.edit', ['id' => $id]);
    }
    /* EDIT */

    /* NEW */
    public function create()
    {
        $countries = Country::query()->get();
        $languages = Language::query()->orderBy('name', 'asc')->get();

        return view('admin.documents.new', ['languages' => $languages, 'countries' => $countries]);
    }

    public function store(Request $request)
    {
        Document::add($request);

        return redirect()->route('admin.documents.list');
    }
    /* NEW */

    /* DELETE */
    public function delete($id)
    {
        $document = Document::query()->findOrFail($id);
        $document->delete();

        return response(['status' => 0]);
    }
    /* DELETE */
}
