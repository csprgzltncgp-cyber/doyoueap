<?php

namespace Database\Seeders;

use App\Models\Amount;
use App\Models\InvoiceItem;
use App\Models\Volume;
use Illuminate\Database\Seeder;

class AddCompsychWellBeingConsultationInvoiceData extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1 -  Add invoice item (30 min)
        $item = InvoiceItem::query()->create([
            'direct_invoice_data_id' => 323,
            'company_id' => 1051, // Compsych
            'name' => 'Well Being Coaching sessions (30 minute)',
            'input' => 12,
            'shown_by_item' => 1,
        ]);

        // 2 - Add volume
        Volume::query()->create([
            'invoice_item_id' => $item->id,
            'name' => 'Consultations number',
            'value' => 0.00,
            'is_changing' => 0,
        ]);

        // 3 - Add amount
        Amount::query()->create([
            'invoice_item_id' => $item->id,
            'name' => 'Unit price',
            'value' => 45.000,
            'is_changing' => 0,
        ]);

        // 4 -  Add invoice item (15 min)
        $item = InvoiceItem::query()->create([
            'direct_invoice_data_id' => 323,
            'company_id' => 1051, // Compsych
            'name' => 'Well Being Coaching sessions (15 minute)',
            'input' => 13,
            'shown_by_item' => 1,
        ]);

        // 5 - Add volume
        Volume::query()->create([
            'invoice_item_id' => $item->id,
            'name' => 'Consultations number',
            'value' => 0.00,
            'is_changing' => 0,
        ]);

        // 6 - Add amount
        Amount::query()->create([
            'invoice_item_id' => $item->id,
            'name' => 'Unit price',
            'value' => 25.000,
            'is_changing' => 0,
        ]);
    }
}
