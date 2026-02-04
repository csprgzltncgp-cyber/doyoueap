<?php

namespace App\Traits\InvoiceHelper;

use App\Models\Company;
use Carbon\Carbon;

trait SearchSortPaginateTrait
{
    public function toggleOpenSelf($date): void
    {
        if (! property_exists($this, 'date') || ! property_exists($this, 'opened')) {
            return;
        }

        if (Carbon::parse($date)->eq(Carbon::parse($this->date))) {
            $this->opened = ! $this->opened;
        }
    }

    public function toggleOpenCompany($id): void
    {
        if (! property_exists($this, 'opened_companies')) {
            return;
        }

        if (in_array($id, $this->opened_companies)) {
            $this->opened_companies = array_diff($this->opened_companies, [$id]);
        } else {
            $this->opened_companies[] = $id;
        }
    }

    public function toggleOpenCountry($id): void
    {
        if (! property_exists($this, 'opened_countries')) {
            return;
        }

        if (in_array($id, $this->opened_countries)) {
            $this->opened_countries = [];
        } else {
            $this->opened_countries = [];
            $this->opened_countries[] = $id;
        }
    }

    public function toggleOpenDirectInvoice($id): void
    {
        if (! property_exists($this, 'opened_direct_invoices')) {
            return;
        }

        if (in_array($id, $this->opened_direct_invoices)) {
            $this->opened_direct_invoices = array_diff($this->opened_direct_invoices, [$id]);
        } else {
            $this->opened_direct_invoices[] = $id;
        }
    }

    public function resetSearch(): void
    {
        if (! property_exists($this, 'search')) {
            return;
        }

        $this->search = '';
    }

    public function loadMore(): void
    {
        if (! property_exists($this, 'perPage')) {
            return;
        }

        $this->perPage += 10;
    }

    public function loadAll(): void
    {
        if (! property_exists($this, 'date') || ! property_exists($this, 'perPage')) {
            return;
        }

        $this->perPage = Company::query()
            ->when(property_exists($this, 'date'), function ($query): void {
                $query->whereHas('direct_invoices', fn ($query) => $query->whereDate('to', Carbon::parse($this->date)->endofMonth()->format('Y-m-d'))
                    ->orWhereDate('to', Carbon::parse($this->date)->endOfMonth()->subDay()->format('Y-m-d')));
            })->count();
    }
}
