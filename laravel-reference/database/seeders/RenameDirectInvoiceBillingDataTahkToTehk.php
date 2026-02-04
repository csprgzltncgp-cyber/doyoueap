<?php

namespace Database\Seeders;

use App\Models\DirectInvoice;
use Illuminate\Database\Seeder;

class RenameDirectInvoiceBillingDataTahkToTehk extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DirectInvoice::query()->cursor()->each(function ($direct_invoice): void {
            $data = $direct_invoice->data;
            if (isset($data['billing_data']['tahk'])) {

                // Set TEHK bool value to the same value as TAHK
                $data['billing_data']['tehk'] = $data['billing_data']['tahk'];

                // Unset TAHK from the billing data array
                unset($data['billing_data']['tahk']);

                $direct_invoice->data = $data;
                $direct_invoice->save();
            }
        });
    }
}
