<?php

namespace App\Traits;

use App\Helpers\CurrencyCached;
use App\Models\DirectBillingData;
use App\Models\DirectInvoice;
use App\Models\DirectInvoiceCrisisData;
use App\Models\DirectInvoiceOtherActivityData;
use App\Models\DirectInvoiceWorkshopData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

trait DirectInvoicesTrait
{
    public function filter_by_frequency(Collection $direct_invoice_datas)
    {
        return $direct_invoice_datas->filter(function ($direct_invoice_data): bool {
            if (empty($direct_invoice_data->direct_billing_data)) {
                return false;
            }

            if ($direct_invoice_data->direct_billing_data->billing_frequency === DirectBillingData::FREQUENCY_MONTHLY) {
                return true;
            }

            if ($direct_invoice_data->direct_billing_data->billing_frequency === DirectBillingData::FREQUENCY_QUARTELY) {
                return Carbon::now()->subMonthNoOverflow()->month % 3 === 0;
            }

            if ($direct_invoice_data->direct_billing_data->billing_frequency === DirectBillingData::FREQUENCY_YEARLY) {
                return Carbon::now()->subMonthNoOverflow()->month === 12;
            }

            return false;
        });
    }

    /**
     * @param  Collection<int,DirectInvoiceWorkshopData>  $workshop_datas
     * @return array
     */
    public function format_workshop_datas(Collection $workshop_datas, DirectBillingData $direct_billing_data, DirectInvoice $direct_invoice)
    {
        return $workshop_datas->map(function (DirectInvoiceWorkshopData $data) use ($direct_billing_data, $direct_invoice) {
            if (empty($data->workshop)) {
                return;
            }

            $data->update(['direct_invoice_id' => $direct_invoice->id]);

            $price = $data->workshop->workshop_price;

            if ($data->workshop->valuta && strtolower((string) $data->workshop->valuta) !== strtolower($direct_billing_data->currency)) {
                if (empty($data->workshop->workshop_price)) {
                    return;
                }

                $currency = new CurrencyCached;

                $price = $currency->convert(
                    $data->workshop->workshop_price,
                    strtoupper($direct_billing_data->currency),
                    strtoupper((string) $data->workshop->valuta)
                );
            }

            return [
                'activity_id' => '#'.$data->workshop->activity_id,
                'price' => round($price, 2),
                'currency' => $data->workshop->valuta,
            ];
        })->toArray();
    }

    /**
     * @param  Collection<int,DirectInvoiceCrisisData>  $crisis_datas
     * @return array
     */
    public function format_crisis_datas(Collection $crisis_datas, DirectBillingData $direct_billing_data, DirectInvoice $direct_invoice)
    {
        return $crisis_datas->map(function (DirectInvoiceCrisisData $data) use ($direct_billing_data, $direct_invoice) {
            if (empty($data->crisis)) {
                return;
            }

            $data->update(['direct_invoice_id' => $direct_invoice->id]);

            $price = $data->crisis->crisis_price;

            if (strtolower((string) $data->crisis->valuta) !== strtolower($direct_billing_data->currency)) {
                if (empty($data->crisis->crisis_price)) {
                    return;
                }

                $currency = new CurrencyCached;

                $price = $currency->convert(
                    $data->crisis->crisis_price,
                    strtoupper($direct_billing_data->currency),
                    strtoupper((string) $data->crisis->valuta)
                );
            }

            return [
                'activity_id' => '#'.$data->crisis->activity_id,
                'price' => round($price, 2),
                'currency' => $data->crisis->valuta,
            ];
        })->toArray();
    }

    /**
     * @param  Collection<int,DirectInvoiceOtherActivityData>  $other_activity_datas
     * @return array
     */
    public function format_other_activity_datas(Collection $other_activity_datas, DirectBillingData $direct_billing_data, DirectInvoice $direct_invoice)
    {
        return $other_activity_datas->map(function (DirectInvoiceOtherActivityData $data) use ($direct_billing_data, $direct_invoice) {
            if (empty($data->other_activity)) {
                return;
            }

            $data->update(['direct_invoice_id' => $direct_invoice->id]);

            $price = $data->other_activity->company_price;

            if (strtolower((string) $data->other_activity->company_currency) !== strtolower($direct_billing_data->currency)) {
                if (empty($data->other_activity->company_price)) {
                    return;
                }

                $currency = new CurrencyCached;

                $price = $currency->convert(
                    $data->other_activity->company_price,
                    strtoupper($direct_billing_data->currency),
                    strtoupper((string) $data->other_activity->company_currency)
                );
            }

            $activity_id = (Str::startsWith($data->other_activity->activity_id, '#')) ? $data->other_activity->activity_id : '#'.$data->other_activity->activity_id;

            return [
                'activity_id' => $activity_id,
                'price' => round($price, 2),
                'currency' => $data->other_activity->company_currency,
            ];
        })->toArray();
    }
}
