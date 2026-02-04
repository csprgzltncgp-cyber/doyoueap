<?php

namespace App\Http\Livewire\Client\Riport;

use App\Exports\EapOnline\RiportExport as RiportExportEapOnline;
use App\Exports\RiportExport;
use App\Models\Country;
use App\Traits\EapOnline\Riport as EapOnlineRiportTrait;
use App\Traits\Riport as NormalRiportTrait;
use Exception;
use Illuminate\Support\Carbon;
use LivewireUI\Modal\ModalComponent;
use Maatwebsite\Excel\Facades\Excel;

class DownloadModal extends ModalComponent
{
    use EapOnlineRiportTrait;
    use NormalRiportTrait;

    public $type;

    public $country;

    public $quarter;

    public $cumulate;

    protected static array $maxWidths = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        '3xl' => 'sm:max-w-3xl',
        '4xl' => 'sm:max-w-4xl',
        '5xl' => 'sm:max-w-5xl',
        '6xl' => 'sm:max-w-6xl',
        '7xl' => 'sm:max-w-7xl',
        'full' => 'sm:max-w-full',
    ];

    public static function modalMaxWidth(): string
    {
        return '2xl';
    }

    public function mount($type, $country, $currentQuarter, $totalView = false): void
    {
        $this->type = $type;
        $this->country = $country;
        $this->cumulate = true;
        $this->totalView = $totalView;

        if ($type == 'normal_riport') {
            $this->quarter = range(1, $currentQuarter);
        } elseif (has_eap_riport_in_quarter($currentQuarter)) {
            $this->quarter = range(1, $currentQuarter);
        } else {
            $this->quarter = range(1, Carbon::now()->startOfQuarter()->subDay()->quarter);
        }
    }

    public function setQuarter($quarter): void
    {
        $this->quarter = [$quarter];
        $this->cumulate = false;
    }

    public function updatedCumulate(): void
    {
        if ($this->cumulate) {
            $this->quarter = array_merge($this->quarter, range(1, $this->quarter[0], 1));
        } else {
            // Get the first quarter where the company has riport
            collect($this->quarter)->each(function ($quarter): void {
                if (has_riport_in_quarter($quarter)) {
                    $this->quarter = [$quarter];
                }
            });
        }
    }

    public function download()
    {
        $riport_data = [];
        $country = Country::query()->find($this->country['id']);
        $this->quarter = array_unique($this->quarter);

        activity()
            ->causedBy(auth()->user())
            ->withProperties(['type' => $this->type, 'country' => $this->country['id'], 'quarter' => $this->quarter, 'cumulate' => $this->cumulate])
            ->event('riport_download')
            ->log('riport_download');

        $company_name = ($this->totalView && auth()->user()->hasConnectedAccounts()) ?
            'total' :
            auth()->user()->companies->first()->name;

        if ($this->type == 'normal_riport') {
            if ($this->cumulate) {
                $filename = 'report_'.
                    str_replace(str_split('\\/ '), '_', strtolower((string) $company_name)).'_'.
                    strtolower((string) $country->code).'_'.
                    implode('_', array_map(fn ($q): string => 'q'.$q, range(1, max($this->quarter)))).
                    '.xlsx';

                foreach ($this->quarter as $quarter) {
                    if ($generated_data = $this->get_cached_riport_data((int) $quarter, $country, null, $this->totalView)) {
                        $riport_data = array_merge_recursive($riport_data, $generated_data['values']);
                    }
                }

                return Excel::download(new RiportExport($riport_data), $filename);
            }
            $filename = 'report_'.
                str_replace(str_split('\\/ '), '_', strtolower((string) $company_name)).'_'.
                strtolower((string) $country->code).'_'.
                'q'.max($this->quarter).'_'.
                '.xlsx';
            $riport_data = $this->get_cached_riport_data($this->quarter[0], $country, null, $this->totalView)['values'];

            return Excel::download(new RiportExport($riport_data), $filename);
        }
        if ($this->cumulate) {
            $filename = 'eap_online_report_'.
                str_replace(str_split('\\/ '), '_', strtolower((string) $company_name)).'_'.
                strtolower((string) $country->code).'_'.
                implode('_', array_map(fn ($q): string => 'q'.$q, range(1, max($this->quarter)))).
                '.xlsx';

            foreach ($this->quarter as $quarter) {
                if (has_eap_riport_in_quarter($quarter)) {
                    // check if today is in the first quarter of the current year
                    if (Carbon::now()->quarter == 1) {
                        $from = Carbon::now()->setYear(Carbon::now()->subYear()->year)->startOfYear()->addMonths($quarter * 3 - 3);
                        $to = Carbon::now()->setYear(Carbon::now()->subYear()->year)->startOfYear()->addMonths($quarter * 3);
                    } else {
                        $from = Carbon::now()->startOfYear()->addMonths($quarter * 3 - 3);
                        $to = Carbon::now()->startOfYear()->addMonths($quarter * 3);
                    }
                    try {
                        $data = $this->get_eap_online_riport_data(
                            $from->format('Y-m-d'),
                            $to->subDay()->format('Y-m-d'),
                            $country,
                            $this->totalView
                        )['values'];
                    } catch (Exception) {
                        $data = [];
                    }

                    $riport_data = array_merge_recursive(
                        $riport_data,
                        $data
                    );
                }
            }

            return Excel::download(new RiportExportEapOnline($riport_data), $filename);
        }
        $filename = 'eap_online_report_'.
            str_replace(str_split('\\/ '), '_', strtolower((string) auth()->user()->companies->first()->name)).'_'.
            strtolower((string) $country->code).'_'.
            'q'.max($this->quarter).'_'.
            '.xlsx';

        try {
            $data = $this->get_eap_online_riport_data(
                Carbon::now()->startOfYear()->addMonths($this->quarter[0] * 3 - 3)->format('Y-m-d'),
                Carbon::now()->startOfYear()->addMonths($this->quarter[0] * 3)->subDay()->format('Y-m-d'),
                $country,
                $this->totalView
            )['values'];
        } catch (Exception) {
            $data = [];
        }

        $riport_data = $data;

        try {
            return Excel::download(new RiportExportEapOnline($riport_data), $filename);
        } catch (Exception) {
            return null;
        }
    }

    public function render()
    {
        return view('livewire.client.riport.download-modal');
    }
}
