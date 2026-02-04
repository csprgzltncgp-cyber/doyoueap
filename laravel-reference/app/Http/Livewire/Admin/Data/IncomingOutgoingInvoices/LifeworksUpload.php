<?php

namespace App\Http\Livewire\Admin\Data\IncomingOutgoingInvoices;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class LifeworksUpload extends Component
{
    use WithFileUploads;

    public $file;

    public $confirmed_replace;

    public $file_months_exists;

    public $month_to_upload;

    public function mount(): void
    {
        $this->confirmed_replace = false;
        $this->file_months_exists = [];

        $this->check_existing_file_months();
    }

    public function render()
    {
        return view('livewire.admin.data.incoming-outgoing-invoices.lifeworks-upload');
    }

    public function save(): void
    {
        try {
            $this->validate([
                'file' => 'max:1024|mimes:xlsx', // 1MB Max
            ]);
        } catch (ValidationException $e) {
            $this->emit('file_upload_failed', $e->getMessage());

            return;
        }

        if (Storage::disk('private')->exists('dashboard-data/lifeworks-data-'.Carbon::now()->setMonth($this->month_to_upload)->format('Y-m').'.xlsx')
        && ! $this->confirmed_replace) {
            $this->emit('file_upload_exists');

            return;
        }

        $this->file->storeAs(
            'dashboard-data',
            'lifeworks-data-'.Carbon::now()->setMonth($this->month_to_upload)->format('Y-m').'.xlsx',
            'private'
        );

        $this->check_existing_file_months();

        Artisan::call('create:dashboard-lifeworks-datas', ['date' => Carbon::now()->setMonth($this->month_to_upload)]);

        $this->emit('file_upload_success');
        $this->confirmed_replace = false;
    }

    public function updated($propertyName): void
    {
        $this->emit('show_upload_progress');

        if ($propertyName == 'file') {
            $this->save();
        }
    }

    public function check_existing_file_months(): void
    {
        collect(Storage::disk('private')->files('dashboard-data'))->each(function ($filename): void {
            $date = str_replace(['dashboard-data/lifeworks-data-', '.xlsx'], '', $filename);

            if (preg_match('/lifeworks-data-(\d{4}-\d{2})\.xlsx$/', $filename, $matches)) {
                $date = $matches[1]; // pl. "2023-01"
            } else {
                throw new Exception('Érvénytelen fájlnév formátum');
            }

            if (Carbon::parse($date)->format('Y') != Carbon::now()->format('Y')) {
                return;
            }

            $this->file_months_exists[] = Carbon::parse($date)->format('m');
        });
    }
}
