<?php

namespace App\Traits\InvoiceHelper;

use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use iio\libmergepdf\Merger;

trait EnvelopeTrait
{
    public function download_envelope($company_id, $date)
    {
        $company = Company::query()->find($company_id);

        $direct_invoices = $company->direct_invoices()
            ->with('envelope')
            ->has('envelope')
            ->whereDate('to', Carbon::parse($date)->endofMonth()->format('Y-m-d'))
            ->get();

        if ($direct_invoices->count() === 0) {
            return null;
        }

        $merger = new Merger;

        foreach ($direct_invoices as $direct_invoice) {
            if (! array_key_exists('data', $direct_invoice->getAttributes())) {
                continue;
            }

            $envelope = $direct_invoice->envelope;

            if (! $envelope) {
                continue;
            }

            $envelope->printed_at = now();
            $envelope->save();

            $html = view('admin.invoice-helper.envelope.document', [
                'data' => $direct_invoice->data,
            ])->render();

            $pdf = Pdf::loadHTML($html)->setPaper([0, 0, 595.28, 311.81], 'portrait')->output();

            $merger->addRaw($pdf);

            if (property_exists($this, 'direct_invoice')) {
                $this->direct_invoice->load('envelope');
            }
        }

        $pdf = $merger->merge();

        return response()->streamDownload(
            function () use ($pdf): void {
                echo $pdf;
            },
            'envelope.pdf'
        );
    }
}
