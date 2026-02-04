<?php

namespace Database\Seeders;

use App\Models\VolumeRequest;
use Illuminate\Database\Seeder;

class DeleteVolumeRequestsWithDeletedVolume extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VolumeRequest::query()->get()->each(function (VolumeRequest $volume_request): void {
            if (! $volume_request->volume || ! $volume_request->volume->invoice_item) {
                $volume_request->delete();
            }
        });
    }
}
