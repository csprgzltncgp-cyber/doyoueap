<?php

namespace App\Http\Livewire\Admin\AffiliateSearch;

use App\Mail\AffiliateSearchCommentCreated;
use App\Mail\AffiliateSearchCompleted;
use App\Models\AffiliateSearch;
use App\Models\AffiliateSearchCompletionPoint;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Show extends Component
{
    public $affiliateSearch;

    public $newComment;

    protected $rules = [
        'newComment' => 'required',
    ];

    protected $listeners = [
        'completeAffiliateSearch' => 'completeAffiliateSearch',
    ];

    public function mount(AffiliateSearch $affiliateSearch): void
    {
        session()->put('affiliate-search-prev-url', url()->previous());

        $this->affiliateSearch = $affiliateSearch;

        if ($this->affiliateSearch->status == AffiliateSearch::STATUS_CREATED) {
            $this->affiliateSearch->update([
                'status' => AffiliateSearch::STATUS_OPENED,
            ]);
        }

        $this->affiliateSearch->comments->where('user_id', '!=', auth()->id())->map(function ($comment): void {
            $comment->seen = true;
            $comment->save();
        });
    }

    public function render()
    {
        $this->affiliateSearch->load(['comments', 'comments.user', 'from', 'to', 'attachments', 'country', 'city', 'affiliate_type']);

        return view('livewire.admin.affiliate-search.show')->extends('layout.master');
    }

    public function forwardAffiliateSearch()
    {
        if (! in_array(auth()->id(), [$this->affiliateSearch->from_id, $this->affiliateSearch->to_id])) {
            return null;
        }

        session()->flash('forwarded-affiliate-search-description', $this->affiliateSearch->description);
        session()->flash('forwarded-affiliate-search-country', $this->affiliateSearch->country->id);
        session()->flash('forwarded-affiliate-search-city', optional($this->affiliateSearch->city)->id);
        session()->flash('forwarded-affiliate-search-type', $this->affiliateSearch->affiliate_type);

        return redirect()->route(auth()->user()->type.'.affiliate_searches.create');
    }

    public function saveComment(): void
    {
        $this->validate();

        $this->affiliateSearch->comments()->create([
            'user_id' => auth()->id(),
            'value' => $this->newComment,
        ]);

        if (config('app.env') === 'production' || config('app.env') === 'local') {
            Mail::to($this->affiliateSearch->from->email)->send(new AffiliateSearchCommentCreated($this->affiliateSearch, auth()->user(), $this->affiliateSearch->from));
        }

        $this->newComment = '';
        $this->emit('commentSaved');
        $this->affiliateSearch->load(['comments', 'comments.user']);
    }

    public function completeAffiliateSearch(): void
    {
        if ($this->affiliateSearch->to_id != $this->affiliateSearch->from_id) {
            auth()->user()->affiliate_search_completion_points()->create([
                'affiliate_search_id' => $this->affiliateSearch->id,
                'type' => $this->getCompletionPointType(),
            ]);
        } else {
            $this->affiliateSearch->update([
                'completed' => true,
            ]);
        }

        $this->affiliateSearch->update([
            'status' => AffiliateSearch::STATUS_COMPLETED,
        ]);

        if ((config('app.env') === 'production' || config('app.env') === 'local') && $this->affiliateSearch->to_id != auth()->id()) {
            Mail::to($this->affiliateSearch->from->email)->send(new AffiliateSearchCompleted($this->affiliateSearch));
        }

        $this->emit('affiliateSearchCompleted');

        $this->affiliateSearch->refresh();
    }

    public function setStatus($status_id): void
    {
        if (! in_array(auth()->id(), [$this->affiliateSearch->from_id, $this->affiliateSearch->to_id])) {
            return;
        }

        $this->affiliateSearch->update([
            'status' => $status_id,
        ]);
    }

    public function reopen(): void
    {
        $this->affiliateSearch->update([
            'status' => AffiliateSearch::STATUS_OPENED,
            'completed' => false,
        ]);

        if (auth()->user()->affiliate_search_completion_points()->where('affiliate_search_id', $this->affiliateSearch->id)->exists()) {
            auth()->user()->affiliate_search_completion_points()->where('affiliate_search_id', $this->affiliateSearch->id)->delete();
        }

        $this->emit('affiliateSearchReopened');
    }

    public function backToList()
    {
        if (session()->get('affiliate-search-prev-url') == route(auth()->user()->type.'.affiliate_searches.show', ['affiliateSearch' => $this->affiliateSearch])) {
            return redirect()->route(auth()->user()->type.'.affiliate_searches.index');
        }

        return redirect(session()->get('affiliate-search-prev-url'));
    }

    private function getCompletionPointType(): int
    {
        if (date('Y-m-d', strtotime((string) $this->affiliateSearch->deadline)) === date('Y-m-d')) {
            return AffiliateSearchCompletionPoint::TYPE_LAST_DAY;
        }
        if (strtotime((string) $this->affiliateSearch->deadline) < time()) {
            return AffiliateSearchCompletionPoint::TYPE_OVER_DEADLINE;
        }

        return AffiliateSearchCompletionPoint::TYPE_WITHIN_DEADLINE;
    }
}
