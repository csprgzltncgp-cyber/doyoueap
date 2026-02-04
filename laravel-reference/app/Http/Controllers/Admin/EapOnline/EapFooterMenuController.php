<?php

namespace App\Http\Controllers\Admin\EapOnline;

use App\Http\Controllers\Controller;
use App\Models\EapOnline\EapFooterMenu;
use App\Models\EapOnline\EapFooterMenuDocument;
use App\Models\EapOnline\EapLanguage;
use App\Models\EapOnline\EapTranslation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EapFooterMenuController extends Controller
{
    public function menu_points_index()
    {
        $menu_points = EapFooterMenu::with(['eap_footer_menu_documents' => fn ($query) => $query->whereNull('translation_of')])->get();
        $languages = EapLanguage::all();

        return view('admin.eap-online.footer.menu_points.index', ['languages' => $languages, 'menu_points' => $menu_points]);
    }

    public function menu_points_store(Request $request)
    {
        $new_menu_points = $request->all('new_menu_points');
        $new_menu_points = array_shift($new_menu_points);

        $existing_menu_points = $request->all('existing_menu_points');
        $existing_menu_points = array_shift($existing_menu_points);

        if (! empty($new_menu_points)) {
            foreach ($new_menu_points as $new_menu_point) {
                $menu_point = EapFooterMenu::query()->create([
                    'slug' => Str::slug($new_menu_point['name']),
                ]);
                $menu_point->eap_footer_menu_translations()->save(new EapTranslation([
                    'language_id' => $new_menu_point['language_id'],
                    'value' => $new_menu_point['name'],
                ]));

                $language = EapLanguage::query()->find($new_menu_point['language_id']);

                if (array_key_exists('documents', $new_menu_point)) {
                    foreach ($new_menu_point['documents'] as $document) {
                        $this->storeDocument($menu_point, $document, $language, null, true);
                    }
                }
            }
        }
        if (! empty($existing_menu_points)) {
            foreach ($existing_menu_points as $id => $existing_menu_point) {
                $menu_point = EapFooterMenu::query()->find($id);
                $language = EapLanguage::query()->find($menu_point->firstTranslation->language->id);

                $menu_point->eap_footer_menu_translations()->where('language_id', $language->id)->update([
                    'value' => $existing_menu_point['name'],
                ]);
                if (array_key_exists('documents', $existing_menu_point)) {
                    foreach ($existing_menu_point['documents'] as $document_id => $document) {
                        if (array_key_exists('file', $document) && array_key_exists('name', $document)) {
                            if ($old_document = $menu_point->eap_footer_menu_documents()->where('id', $document_id)->first()) {
                                Storage::deleteDirectory(substr((string) $old_document->getAttribute('path'), 0, strrpos((string) $old_document->getAttribute('path'), '/')));
                            }

                            if (! array_key_exists('is_new', $document)) {
                                $document['id'] = $document_id;
                            }
                            $this->storeDocument($menu_point, $document, $language, null, array_key_exists('is_new', $document));
                        } else {
                            $existing_document = EapFooterMenuDocument::query()->find($document_id);
                            $existing_document->update([
                                'name' => $document['name'],
                                'description' => $document['description'],
                            ]);
                        }
                    }
                }
            }
        }

        return redirect()->back();
    }

    public function delete_menu_point($id): void
    {
        try {
            EapFooterMenu::destroy($id);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }

    public function menu_points_translate_view()
    {
        $menu_points = EapFooterMenu::all();
        $languages = EapLanguage::all();

        return view('admin.eap-online.footer.menu_points.translate', ['menu_points' => $menu_points, 'languages' => $languages]);
    }

    public function menu_points_translate_store(Request $request)
    {
        foreach ($request->input('menu_points') as $menu_point) {
            foreach ($menu_point['text'] as $language_id => $translation) {
                if ($menu_point_translation = EapTranslation::query()->where(['translatable_type' => 'App\Models\FooterMenu', 'translatable_id' => $menu_point['id'], 'language_id' => $language_id])->first()) {
                    $menu_point_translation->value = $translation;
                    $menu_point_translation->save();
                } elseif (! empty($translation)) {
                    $parent_menu_item = EapFooterMenu::query()->find($menu_point['id']);
                    $parent_menu_item->eap_footer_menu_translations()->save(new EapTranslation([
                        'value' => $translation,
                        'language_id' => $language_id,
                    ]));
                }
            }
        }

        return redirect()->back();
    }

    public function documents_translate_list()
    {
        $menu_points = EapFooterMenu::with('eap_footer_menu_documents')->get();

        return view('admin.eap-online.footer.documents.list', ['menu_points' => $menu_points]);
    }

    public function documents_translate_view($id)
    {
        $languages = EapLanguage::all();
        $menu_point = EapFooterMenu::query()->find($id);
        $documents = EapFooterMenuDocument::query()->where('footer_menu_id', $id)->where('language_id', $menu_point->firstTranslation->language->id)->get();

        return view('admin.eap-online.footer.documents.translate', ['documents' => $documents, 'languages' => $languages, 'menu_point' => $menu_point]);
    }

    public function documents_translate_store(Request $request, $id)
    {
        $documents = $request->all('documents');
        $documents = array_shift($documents);

        $menu_point = EapFooterMenu::query()->where('id', $id)->first();
        if (empty($documents)) {
            return redirect()->back();
        }
        if (empty($menu_point)) {
            return redirect()->back();
        }
        foreach ($documents as $document_id => $document) {
            foreach ($document as $language_id => $values) {
                $language = EapLanguage::query()->find($language_id);

                if (array_key_exists('file', $values) && $values['name']) {
                    if ($old_document = $menu_point->eap_footer_menu_documents()->where(['footer_menu_id' => $id, 'language_id' => $language_id, 'translation_of' => $document_id])->first()) {
                        Storage::deleteDirectory(substr((string) $old_document->getAttribute('path'), 0, strrpos((string) $old_document->getAttribute('path'), '/')));
                        $old_document->delete();
                    }

                    $this->storeDocument($menu_point, $values, $language, $document_id, true);
                }
                if (! $values['name']) {
                    continue;
                }
                if (! ($existing_document_translation = EapFooterMenuDocument::query()->where(['footer_menu_id' => $id, 'language_id' => $language_id, 'translation_of' => $document_id])->first())) {
                    continue;
                }
                $existing_document_translation->update([
                    'name' => $values['name'],
                    'description' => $values['description'],
                ]);
            }
        }

        return redirect()->back();
    }

    public function delete_document($id): void
    {
        try {
            EapFooterMenuDocument::destroy($id);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }

    private function storeDocument($menu_point, array $document, $language, $translation_of = null, bool $forceNew = false): void
    {
        if (array_key_exists('file', $document)) {
            if ($forceNew) {
                $current_document = $menu_point->eap_footer_menu_documents()->create([
                    'language_id' => $language->id,
                    'translation_of' => $translation_of,
                    'name' => $document['name'],
                    'description' => $document['description'],
                ]);
            } else {
                $current_document = EapFooterMenuDocument::query()->where('id', $document['id'])->first();
                $current_document->update([
                    'language_id' => $language->id,
                    'translation_of' => $translation_of,
                    'footer_menu_id' => $menu_point->id,
                    'name' => $document['name'],
                    'description' => $document['description'],
                ]);
            }

            $filename = pathinfo((string) $document['file']->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = pathinfo((string) $document['file']->getClientOriginalName(), PATHINFO_EXTENSION);

            $current_document->update([
                'path' => 'eap-online/footer-documents/'.$menu_point->id.'/'.$current_document->id.'/'.time().'-'.Str::slug($filename).'.'.$extension,
            ]);

            $document['file']->storeAs('eap-online/footer-documents/'.$menu_point->id.'/'.$current_document->id.'/', time().'-'.Str::slug($filename).'.'.$extension);
        }
    }
}
