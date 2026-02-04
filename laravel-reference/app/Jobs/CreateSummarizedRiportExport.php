<?php

namespace App\Jobs;

use App\Exports\RiportSummarizeExport;
use App\Mail\SummarizedRiportExportCreated;
use App\Models\Company;
use App\Models\Riport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class CreateSummarizedRiportExport implements ShouldQueue
{
    use \App\Traits\Riport;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $timeout = 3600;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public $quarter, public $company_prefix, public $name, public $email) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $summarized_values = null;

        $company_ids = Company::query()
            ->where('name', 'like', '%'.$this->company_prefix.'%')->pluck('id');

        if ($this->quarter == 'cumulated') {
            // check if today is in the first quarter of the current year
            $from = Carbon::now()->quarter == 1
                ? Carbon::now()->subYear()->startOfYear()
                : Carbon::now()->startOfYear();

            $to = $this->get_riport_interval(get_last_quarter())['to'];
        } else {
            $current_interval = $this->get_riport_interval($this->quarter);
            $from = $current_interval['from'];
            $to = $current_interval['to'];
        }

        $riports = Riport::query()
            ->whereIn('company_id', $company_ids)
            ->where('from', '>=', $from)
            ->where('to', '<=', $to)
            ->where('is_active', 1)
            ->with(['values', 'company.countries'])
            ->get();

        $summarized_values = collect();

        foreach ($riports as $riport) {
            foreach ($riport->company->countries as $country) {
                $quarter = $this->quarter == 'cumulated' ? get_last_quarter() : $this->quarter;
                $values = $this->generate_quearter_riport_values($riports, $country, $quarter, false, $riport->company);
                $summarized_values->put($country->id, $values);
            }
        }

        $summarized_values->unique('country_id');

        $filename = $this->company_prefix.'-summarized-riport-'.uniqid().'.xlsx';

        Excel::store(new RiportSummarizeExport($summarized_values), '/summarized-riport-exports/'.$filename, 'private');

        $quarter_text = $this->quarter == 'cumulated' ? 'Q'.implode('+Q', range(1, get_last_quarter())) : 'Q'.$this->quarter;
        Mail::to($this->email)->send(new SummarizedRiportExportCreated($this->name, $quarter_text, $filename));
    }
}
