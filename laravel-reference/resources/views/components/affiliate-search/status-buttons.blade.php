@props(['affiliateSearch'])

<style>
    button.status-button {
        width: auto !important;
        background: rgb(222, 240, 241) !important;
    }
</style>

<div class="col-12">
    <p style="margin-bottom:0">Feladat státusza:</p>
</div>
<div class="col-12 button-holder d-flex align-items-center flex-wrap justify-content-start mb-3">
    <button type="button" wire:click='setStatus({{\App\Models\AffiliateSearch::STATUS_SEARCH_STARTED}})' class="button status-button mt-2"
        @if($affiliateSearch->status >= \App\Models\AffiliateSearch::STATUS_SEARCH_STARTED && $affiliateSearch->status > \App\Models\AffiliateSearch::STATUS_OPENED) style="background: rgb(102, 16, 242) !important;" @else style="color:black;" @endif
    >
        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; margin-bottom: 1px;" class="mr-1 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>

        {{__('affiliate-search-workflow.status.search_started')}}
    </button>

    <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; margin-bottom: 1px;" class="mt-2 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
    </svg>

    <button type="button" wire:click='setStatus({{\App\Models\AffiliateSearch::STATUS_AFFILIATE_FOUND}})' class="button status-button mt-2 mr-2"
        @if($affiliateSearch->status >= \App\Models\AffiliateSearch::STATUS_AFFILIATE_FOUND) style="background:  rgb(102, 16, 242) !important; color: white;" @else style="color:black;" @endif
    >
        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; margin-bottom: 1px;" class="mr-1 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
        </svg>

        {{__('affiliate-search-workflow.status.affiliate_found')}}
    </button>

    <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; margin-bottom: 1px;" class="mt-2 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
    </svg>

    <button type="button" wire:click='setStatus({{\App\Models\AffiliateSearch::STATUS_AFFILIATE_CONTACTED}})' class="button status-button mt-2 mr-2"
        @if($affiliateSearch->status >= \App\Models\AffiliateSearch::STATUS_AFFILIATE_CONTACTED) style="background:  rgb(102, 16, 242) !important; color: white;" @else style="color:black;" @endif
    >
        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; margin-bottom: 1px;" class="mr-1 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 3h5m0 0v5m0-5l-6 6M5 3a2 2 0 00-2 2v1c0 8.284 6.716 15 15 15h1a2 2 0 002-2v-3.28a1 1 0 00-.684-.948l-4.493-1.498a1 1 0 00-1.21.502l-1.13 2.257a11.042 11.042 0 01-5.516-5.517l2.257-1.128a1 1 0 00.502-1.21L9.228 3.683A1 1 0 008.279 3H5z" />
        </svg>

        {{__('affiliate-search-workflow.status.affiliate_contacted')}}
    </button>

    <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; margin-bottom: 1px;" class="mt-2 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
    </svg>

    <button type="button" wire:click='setStatus({{\App\Models\AffiliateSearch::STATUS_CONTRACT_SENT}})' class="button status-button mt-2 mr-2"
        @if($affiliateSearch->status >= \App\Models\AffiliateSearch::STATUS_CONTRACT_SENT) style="background:  rgb(102, 16, 242) !important; color: white;" @else style="color:black;" @endif
    >
        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; margin-bottom: 1px;" class="mr-1 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
        </svg>

        {{__('affiliate-search-workflow.status.contract_sent')}}
    </button>

    <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; margin-bottom: 1px;" class="mt-2 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
    </svg>

    <button type="button" wire:click='setStatus({{\App\Models\AffiliateSearch::STATUS_CONTRACT_SIGNED}})' class="button status-button mt-2 mr-2"
        @if($affiliateSearch->status >= \App\Models\AffiliateSearch::STATUS_CONTRACT_SIGNED) style="background: rgb(257, 190, 17) !important; color: white; " @else style="color:black;" @endif
    >
        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; margin-bottom: 1px;" class="mr-1 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
        </svg>

        {{__('affiliate-search-workflow.status.contract_signed')}}
    </button>

    <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; margin-bottom: 1px;" class="mt-2 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
    </svg>

    <button type="button" wire:click='setStatus({{\App\Models\AffiliateSearch::STATUS_ACTIVE_ON_DASBOARD}})' class="button status-button mt-2 mr-2"
        @if($affiliateSearch->status >= \App\Models\AffiliateSearch::STATUS_ACTIVE_ON_DASBOARD) style="background: rgb(257, 190, 17) !important; color: white;" @else style="color:black;" @endif
    >
        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; margin-bottom: 1px;" class="mr-1 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{__('affiliate-search-workflow.status.active_on_dashboard')}}
    </button>
</div>
