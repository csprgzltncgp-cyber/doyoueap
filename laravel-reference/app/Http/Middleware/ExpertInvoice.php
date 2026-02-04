<?php

namespace App\Http\Middleware;

use App\Enums\InvoicingType;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExpertInvoice
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User $user */
        $user = auth()->user();

        // Out of date
        if (! (Carbon::now()->day >= 1 && Carbon::now()->day <= 10) && ! has_invoicing_opened($user)) {
            return redirect()->route('expert.invoices.main');
        }

        // Invoice already created
        if ($user->invoices()->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count() > 0) {
            return redirect()->route('expert.invoices.main');
        }

        // No currency
        if (
            empty($user->invoice_datas()->first()->currency) ||
            empty($user->invoice_datas()->first()->hourly_rate_50) && ! in_array($user->invoice_datas()->first()->invoicing_type, [InvoicingType::TYPE_CUSTOM, InvoicingType::TYPE_FIXED]) ||
            (empty($user->invoice_datas()->first()->hourly_rate_30) && ! in_array($user->invoice_datas()->first()->invoicing_type, [InvoicingType::TYPE_CUSTOM, InvoicingType::TYPE_FIXED]) && ($user->hasPermission(2) || $user->hasPermission(3) || $user->hasPermission(7)))
        ) {
            return redirect()->route('expert.invoices.main');
        }

        // Need currency change
        if (currency_change_documnet_missing() && $user->invoice_datas()->first()->invoicing_type !== InvoicingType::TYPE_CUSTOM) {
            return redirect()->route('expert.invoices.main');
        }

        return $next($request);
    }
}
