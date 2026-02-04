<?php

namespace Database\Seeders;

use App\Models\DirectInvoice;
use Illuminate\Database\Seeder;

class RenameOrientationDatasToOtherActivityDatas extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $direct_invoices = DirectInvoice::query()->get();

        foreach ($direct_invoices as $direct_invoice) {
            $direct_invoice_data = $direct_invoice->data;

            if (isset($direct_invoice_data['orientation_datas'])) {
                $direct_invoice_data['other_activity_datas'] = $direct_invoice_data['orientation_datas'];
                unset($direct_invoice_data['orientation_datas']);
            }
            $direct_invoice->data = $direct_invoice_data;
            $direct_invoice->save();
        }
    }
}
