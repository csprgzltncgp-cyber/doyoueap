<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AffiliateSearchCreated;
use App\Models\AffiliateSearch;
use App\Models\AffiliateSearchAttachment;
use App\Models\AffiliateSearchCompletionPoint;
use App\Models\City;
use App\Models\Country;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class AffiliateSearchController extends Controller
{
    public function all()
    {
        $affiliateSearches = AffiliateSearch::query()->with(['from', 'to'])->orderBy('created_at')->get();
        $admins = User::query()->whereIn('type', ['admin', 'eap_admin', 'account_admin', 'affiliate_search_admin'])->has('assigned_affiliate_search')->orderBy('name')->get();

        return view('admin.affiliate_search.all', ['affiliateSearches' => $affiliateSearches, 'admins' => $admins]);
    }

    public function index()
    {
        return view('admin.affiliate_search.index');
    }

    public function create()
    {
        $admins = User::query()
            ->whereIn('type', ['admin', 'eap_admin', 'account_admin', 'affiliate_search_admin'])
            ->where('connected_account', null)
            ->orderBy('name')
            ->get();

        $connectable_users = User::query()
            ->whereIn('type', ['admin', 'eap_admin', 'account_admin', 'affiliate_search_admin'])
            ->where('connected_account', null)
            ->whereNotIn('id', [auth()->id()])
            ->get();

        $countries = Country::query()->orderBy('name')->get();
        $cities = City::query()->orderBy('name')->get();
        $permissions = Permission::query()->get();

        session()->put('affiliate-search-prev-url', url()->previous());

        $forwarded_description = session()->get('forwarded-affiliate-search-description');
        $forwarded_country = session()->get('forwarded-affiliate-search-country');
        $forwarded_city = session()->get('forwarded-affiliate-search-city');
        $forwarded_permission = session()->get('forwarded-affiliate-search-permission');

        return view('admin.affiliate_search.create', ['connectable_users' => $connectable_users, 'admins' => $admins, 'countries' => $countries, 'cities' => $cities, 'forwarded_description' => $forwarded_description, 'forwarded_country' => $forwarded_country, 'forwarded_city' => $forwarded_city, 'forwarded_permission' => $forwarded_permission, 'permissions' => $permissions]);
    }

    public function store()
    {
        $validated = request()->validate([
            'description' => ['required'],
            'to_id' => ['exists:users,id', 'required'],
            'deadline' => ['date', 'required'],
            'deadline_type' => ['required'],
            'attachments' => ['nullable'],
            'attahcments.*' => ['file', 'mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar'],
            'country_id' => ['exists:countries,id', 'required'],
            'permission_id' => ['exists:permissions,id', 'required'],
        ]);

        $affiliate_search = AffiliateSearch::query()->create(array_merge(
            collect($validated)->except('attachments')->toArray(),
            [
                'from_id' => auth()->id(),
                'status' => AffiliateSearch::STATUS_CREATED,
            ]
        ));

        if (request()->has('connected_users')) {
            $connectable_user_ids = array_unique(request()->get('connected_users'));
            $affiliate_search->connected_users()->attach($connectable_user_ids);
        }

        if (request()->has('attachments')) {
            foreach (request()->file('attachments') as $attachment) {
                $affiliate_search->attachments()->create([
                    'filename' => $attachment->getClientOriginalName(),
                    'path' => $attachment->store('affiliate-search-attachments/'.$affiliate_search->id, 'private'),
                ]);
            }
        }

        $affiliate_search->load(['from', 'to']);

        if ((config('app.env') === 'production' || config('app.env') === 'local') && $affiliate_search->to_id != auth()->id()) {
            Mail::to($affiliate_search->to)->send(new AffiliateSearchCreated($affiliate_search));
        }

        if (session()->get('affiliate-search-prev-url') == route(auth()->user()->type.'.affiliate_searches.create')) {
            return redirect()->route(auth()->user()->type.'.affiliate_searches.index');
        }

        return redirect(session()->get('affiliate-search-prev-url'));
    }

    public function statistics()
    {
        $over_deadline = AffiliateSearchCompletionPoint::query()
            ->where('type', AffiliateSearchCompletionPoint::TYPE_OVER_DEADLINE)
            ->get()
            ->groupBy('user_id')
            ->map(function ($points, $user_id): array {
                $user = User::query()->with('affiliate_search_completion_points')->find($user_id);
                $all_points = $user->affiliate_search_completion_points->count();

                return [
                    'user' => $user->name,
                    'points' => $points->count(),
                    'all_points' => $all_points,
                    'ratio' => $points->count() / $all_points,
                ];
            })->sortByDesc('ratio');

        $last_day = AffiliateSearchCompletionPoint::query()
            ->where('type', AffiliateSearchCompletionPoint::TYPE_LAST_DAY)
            ->get()
            ->groupBy('user_id')
            ->map(function ($points, $user_id): array {
                $user = User::query()->with('affiliate_search_completion_points')->find($user_id);
                $all_points = $user->affiliate_search_completion_points->count();

                return [
                    'user' => $user->name,
                    'points' => $points->count(),
                    'all_points' => $all_points,
                    'ratio' => $points->count() / $all_points,
                ];
            })->sortByDesc('ratio');

        $within_deadline = AffiliateSearchCompletionPoint::query()
            ->where('type', AffiliateSearchCompletionPoint::TYPE_WITHIN_DEADLINE)
            ->get()
            ->groupBy('user_id')
            ->map(function ($points, $user_id): array {
                $user = User::query()->with('affiliate_search_completion_points')->find($user_id);
                $all_points = $user->affiliate_search_completion_points->count();

                return [
                    'user' => $user->name,
                    'points' => $points->count(),
                    'all_points' => $all_points,
                    'ratio' => $points->count() / $all_points,
                ];
            })->sortByDesc('ratio');

        return view('admin.affiliate_search.statistics', ['over_deadline' => $over_deadline, 'last_day' => $last_day, 'within_deadline' => $within_deadline]);
    }

    public function delete($id)
    {
        $affiliate_search = AffiliateSearch::query()->find($id);
        $affiliate_search->delete();

        if (session()->get('affiliate-search-prev-url') == route(auth()->user()->type.'.affiliate_searches.create')) {
            return redirect()->route(auth()->user()->type.'.affiliate_searches.index');
        }

        return response()->json('ok');
    }

    public function download_attachment($id)
    {
        if (! $attachment = AffiliateSearchAttachment::query()->find($id)) {
            return response()->json(['error' => 'Attachment not found']);
        }

        return response()->download(storage_path('app/'.$attachment->path), $attachment->filename);
    }

    public function filter()
    {
        $admins = User::query()
            ->whereIn('type', ['admin', 'eap_admin'])
            ->where('connected_account', null)
            ->orderBy('name')
            ->get();

        $countries = Country::query()->orderBy('name')->get();
        $cities = City::query()->orderBy('name')->get();
        $permissions = Permission::query()->get();

        return view('admin.affiliate_search.filter', ['admins' => $admins, 'countries' => $countries, 'cities' => $cities, 'permissions' => $permissions]);
    }

    public function filter_result()
    {
        $filters = array_filter(request()->all());
        $builder = AffiliateSearch::query()
            ->where('to_id', auth()->id());

        foreach ($filters as $key => $value) {
            $builder = $builder->where($key, $value);
        }

        $affiliate_searches = $builder->get();

        return view('admin.affiliate_search.result', ['affiliate_searches' => $affiliate_searches]);
    }
}
