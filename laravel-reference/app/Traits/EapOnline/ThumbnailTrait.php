<?php

namespace App\Traits\EapOnline;

use App\Models\EapOnline\EapThumbnail;

trait ThumbnailTrait
{
    public function setThumbnail($file, $model, string $type): void
    {
        $extension = $file->getClientOriginalExtension();
        $name = time().'-'.$model->slug.'.'.$extension;

        $thumbnail = new EapThumbnail([
            'filename' => $name,
            'type' => $type,
        ]);

        $model->eap_thumbnail()->save($thumbnail);
        $file->storeAs('eap-online/thumbnails/'.$type, $name);
    }
}
