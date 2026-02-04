<?php

namespace App\Exports\Sheets;

use App\Models\Country;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class RiportSheet implements FromView, ShouldAutoSize, WithTitle
{
    private $country;

    public function __construct($country_id, private $data, private $with_inprogress = true)
    {
        $this->country = Country::query()->where('id', $country_id)->first();
    }

    public function title(): string
    {
        return Str::title($this->country->name);
    }

    public function view(): View
    {
        return view('excels.riport', [
            'generated_riport' => $this->data,
            'with_inprogress' => $this->with_inprogress,
        ]);
    }
}
