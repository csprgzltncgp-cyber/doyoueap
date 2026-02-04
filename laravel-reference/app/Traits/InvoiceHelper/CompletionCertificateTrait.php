<?php

namespace App\Traits\InvoiceHelper;

use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use iio\libmergepdf\Merger;

trait CompletionCertificateTrait
{
    public function download_completion_certificate($company_id, $date)
    {
        $company = Company::query()->find($company_id);

        $direct_invoices = $company->direct_invoices()
            ->with('completion_certificate')
            ->has('completion_certificate')
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

            $completion_certificate = $direct_invoice->completion_certificate;

            if (! $completion_certificate) {
                continue;
            }

            $completion_certificate->printed_at = now();
            $completion_certificate->save();

            $html = view('admin.invoice-helper.completion-certificate.document', [
                'with_header' => false,
                'company' => $company,
                'direct_invoice' => $direct_invoice,
                'language' => strtolower($direct_invoice->data['billing_data']['invoice_language'] ?? 'en'),
            ])->render();

            $pdf = Pdf::loadHTML($html)->output();

            $merger->addRaw($pdf);

            if (property_exists($this, 'direct_invoice')) {
                $this->direct_invoice->load('completion_certificate');
            }
        }

        $pdf = $merger->merge();

        return response()->streamDownload(
            function () use ($pdf): void {
                echo $pdf;
            },
            'completion_certificate.pdf'
        );
    }
}
