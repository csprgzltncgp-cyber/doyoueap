<?php

namespace App\Http\Livewire\Admin\AffiliateSearch;

use App\Models\AffiliateSearch;
use Illuminate\Support\Facades\Cookie;
use Livewire\Component;

class Today extends Component
{
    public $perPage = 10;

    public $orderBy;

    public function mount(): void
    {
        if (Cookie::get('orderBySelectTodayDefault') !== null) {
            $this->orderBy = explode(',', Cookie::get('orderBySelectTodayDefault'));
        } else {
            $this->orderBy = ['deadline', 'asc'];
        }
    }

    public function render()
    {
        $affiliateSearches = AffiliateSearch::query()
            ->where('deadline', today())
            ->where(function ($query): void {
                $query->where('to_id', auth()->id())->orWhereHas('connected_users', function ($query): void {
                    $query->where('user_id', auth()->id());
                });
            })
            ->where('status', '!=', AffiliateSearch::STATUS_COMPLETED)
            ->join('users', 'users.id', '=', 'affiliate_searches.from_id')
            ->orderBy($this->orderBy[0], $this->orderBy[1])
            ->select('affiliate_searches.*')
            ->paginate($this->perPage);

        return view('livewire.admin.affiliate-search.today', ['affiliateSearches' => $affiliateSearches]);
    }

    public function updated($key, $value): void
    {
        Cookie::queue(Cookie::forever('orderBySelectTodayDefault', $value));
        $this->orderBy = explode(',', (string) $value);
    }

    public function loadMore(): void
    {
        $this->perPage += 10;
    }

    public function loadAll(): void
    {
        $this->perPage = AffiliateSearch::query()->count();
    }
}
