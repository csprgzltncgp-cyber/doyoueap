<?php

namespace App\Http\Resources;

use App\Models\LiveWebinar;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin LiveWebinar
 */
class LiveWebinarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'permission_id' => $this->permission_id,
            'expert' => $this->expert->name,
            'language_id' => $this->language_id,
            'topic' => $this->topic,
            'from' => $this->from->format('Y-m-d H:i:s'),
            'duration' => $this->duration,
            'description' => $this->description,
            'image' => $this->image,
            'zoom_meeting_id' => $this->zoom_meeting_id,
            'zoom_join_url' => $this->zoom_join_url,
            'zoom_passcode' => $this->zoom_passcode,
            'recording_status' => $this->recording_status,
            'vimeo_video_url' => $this->vimeo_video_url,
            'recording_archived_at' => $this->recording_archived_at,
        ];
    }
}
