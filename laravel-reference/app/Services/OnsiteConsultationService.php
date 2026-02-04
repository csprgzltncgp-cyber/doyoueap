<?php

namespace App\Services;

use App\Models\EapOnline\OnsiteConsultation;
use App\Models\EapOnline\OnsiteConsultationDate;
use App\Models\EapOnline\OnsiteConsultationDateAppointment;
use App\Models\EapOnline\OnsiteConsultationExpert;
use App\Models\EapOnline\OnsiteConsultationPlace;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class OnsiteConsultationService
{
    public function store_consultation(array $consultation_data, array $languages): void
    {
        /** @var OnsiteConsultation */
        $onsite_consultation = OnsiteConsultation::query()->create($consultation_data);
        $onsite_consultation->languages()->attach($languages);
    }

    public function store_date(int $onsite_consultation_id, string $date): OnsiteConsultationDate
    {
        /** @var OnsiteConsultationDate|null */
        return OnsiteConsultationDate::query()->create([
            'onsite_consultation_id' => $onsite_consultation_id,
            'date' => $date,
        ]);
    }

    public function store_appointment(OnsiteConsultationDate $onsite_consultation_date, array $times, ?int $expert_id): void
    {
        collect($times)->each(function ($time_json) use ($onsite_consultation_date, $expert_id): void {
            $json_string = Str::replace('`', '', $time_json);
            if (json_validate($json_string)) {
                $time = json_decode($json_string);

                // If time (from-to) coincides with another time exit the loop
                if (OnsiteConsultationDateAppointment::query()
                    ->where('onsite_consultation_date_id', $onsite_consultation_date->id)
                    ->whereBetween('from', [$time->from, $time->to])
                    ->orWhereBetween('to', [$time->from, $time->to])
                    ->where('onsite_consultation_date_id', $onsite_consultation_date->id)
                    ->exists()) {
                    return;
                }

                $onsite_consultation_date->appointments()->create([
                    'from' => $time->from,
                    'to' => $time->to,
                    'onsite_consultation_expert_id' => $expert_id,
                ]);
            }
        });
    }

    public function delete_appointment(OnsiteConsultationDateAppointment $appointment): void
    {
        /** @var Collection<int, OnsiteConsultationDateAppointment> */
        $appointments = $appointment->date->appointments;

        if ($appointments->count() > 1) {
            $appointment->delete();
        } else {
            $appointment->date->delete();
            $appointment->delete();
        }
    }

    public function update_appointment(Request $request): void
    {
        $appointment = OnsiteConsultationDateAppointment::query()
            ->where('id', $request->appointment_id)
            ->first();

        if ($appointment) {
            $appointment->update([
                'from' => $request->edit_from_time,
                'to' => $request->edit_to_time,
            ]);
        }
    }

    public function get_onsite_consultation_date_by_date(int $onsite_consultation_id, string $date): ?OnsiteConsultationDate
    {
        /** @var OnsiteConsultationDate|null */
        return OnsiteConsultationDate::query()
            ->where('onsite_consultation_id', $onsite_consultation_id)
            ->where('date', $date)
            ->first();
    }

    public function store_place(string $place, ?string $address = null): void
    {
        OnsiteConsultationPlace::query()->create([
            'name' => $place,
            'address' => $address,
        ]);
    }

    public function update_place(int $place_id, array $place_data): void
    {
        OnsiteConsultationPlace::query()
            ->where('id', $place_id)
            ->update([
                'name' => $place_data['name'],
                'address' => $place_data['address'],
            ]);
    }

    public function get_onsite_consultation_by_id(int $onsite_consultation_id): ?OnsiteConsultation
    {
        /** @var OnsiteConsultation|null */
        return OnsiteConsultation::query()
            ->where('id', $onsite_consultation_id)
            ->first();
    }

    public function create_onsite_consultation_expert(string $name, string $description, string $image): OnsiteConsultationExpert
    {
        return OnsiteConsultationExpert::query()->create([
            'name' => $name,
            'description' => $description,
            'image' => $image,
        ]);
    }

    public function update_onsite_consultation_expert(int $expert_id, array $expert_data, ?string $path = null): void
    {
        $expert = OnsiteConsultationExpert::query()
            ->where('id', $expert_id)
            ->first();

        // If new image was uploaded remove the previous one
        if ($path) {

            // Delete previous image file
            if (file_exists(public_path('assets/'.$expert->image))) {
                File::delete(public_path('assets/'.$expert->image));
            }

            $expert_data['image'] = $path;
        }

        $expert->update($expert_data);
    }
}
