<?php

namespace App\Http\Livewire\Admin\AffiliateSearch;

use App\Models\AffiliateSearch;
use Illuminate\Support\Facades\Cookie;
use Livewire\Component;

class OverDeadline extends Component
{
    public $perPage = 10;

    public $orderBy;

    public function mount(): void
    {
        if (Cookie::get('orderBySelectOverDeadlineDefault') !== null) {
            $this->orderBy = explode(',', Cookie::get('orderBySelectOverDeadlineDefault'));
        } else {
            $this->orderBy = ['deadline', 'asc'];
        }
    }

    public function render()
    {
        $affiliate_searches = AffiliateSearch::query()
            ->where('deadline', '<', today())
            ->where('status', '!=', AffiliateSearch::STATUS_COMPLETED)
            ->where(function ($query): void {
                $query->where('to_id', auth()->id())->orWhereHas('connected_users', function ($query): void {
                    $query->where('user_id', auth()->id());
                });
            })
            ->join('users', 'users.id', '=', 'affiliate_searches.from_id')
            ->select('affiliate_searches.*')
            ->orderBy($this->orderBy[0], $this->orderBy[1])
            ->paginate($this->perPage);

        return view('livewire.admin.affiliate-search.over-deadline', ['affiliate_searches' => $affiliate_searches]);
    }

    public function updated($key, $value): void
    {
        Cookie::queue(Cookie::forever('orderBySelectOverDeadlineDefault', $value));
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
