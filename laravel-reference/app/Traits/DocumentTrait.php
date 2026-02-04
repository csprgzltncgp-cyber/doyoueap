<?php

namespace App\Traits;

use App\Models\PrizeGame\Section;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait DocumentTrait
{
    public function update_document($file, $button_text, $model, string $path_prefix): void
    {
        if ($old_document = $model->documents()->first()) {
            Storage::delete($path_prefix.'/documents/'.$old_document->filename);
            $old_document->delete();
        }

        $this->save_document($file, $button_text, $model, $path_prefix);
    }

    public function save_document($file, $button_text, $model, string $path_prefix): void
    {
        if ($file) {
            $extension = $file->getClientOriginalExtension();
            $name = time().'-'.Str::random(10).'.'.$extension;

            // if model is a prizagame section we need to save the button text in the translations table
            if ($model instanceof Section) {
                $new_document = $model->documents()->create([
                    'filename' => $name,
                ]);

                $new_document->translations()->create([
                    'language_id' => $model->content->language_id,
                    'value' => $button_text ?? '',
                ]);
            } else {
                $model->documents()->create([
                    'filename' => $name,
                ]);
            }
            $file->storeAs($path_prefix.'/documents', $name);
        }
    }
}
