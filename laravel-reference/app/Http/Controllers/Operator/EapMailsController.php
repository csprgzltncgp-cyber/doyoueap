<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\EapOnline\EapLanguageLines;
use App\Models\EapOnline\EapMail;
use App\Models\EapOnline\EapMailNotification;
use App\Models\EapOnline\EapMessage;
use App\Models\EapOnline\EapUser;
use App\Notifications\EapOnline\EapMessageCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EapMailsController extends Controller
{
    public function index()
    {
        $mails = EapMail::with('eap_user')
            ->whereNull('deleted_at')
            ->where('country_id', Auth::user()->country->id)
            ->whereRelation('first_message', 'type', 'user')
            ->withExists(['eap_notifications as has_operator_notification' => function ($query): void {
                $query->where('type', 'new_mail_operator');
            }])
            ->orderByDesc('has_operator_notification')
            ->orderBy('date', 'DESC')
            ->paginate(10);

        return view('operator.eap-online.mails.list', ['mails' => $mails]);
    }

    public function view(int $id, int $page)
    {
        $mail = EapMail::with(['eap_messages', 'eap_notifications'])->where('id', $id)->first();

        if (! empty($mail->eap_notifications)) {
            foreach ($mail->eap_notifications as $notification) {
                if ($notification->type === 'new_mail_operator' || $notification->type === 'new_mail_admin') {
                    $notification->delete();
                }
            }
        }

        return view('operator.eap-online.mails.view', ['mail' => $mail, 'page' => $page]);
    }

    public function reply(Request $request, $id)
    {
        $mail = EapMail::query()->find($id);

        $message = new EapMessage([
            'message' => nl2br((string) $request->get('message')),
            'type' => 'operator',
            'user_id' => Auth::user()->id,
        ]);

        $user_notification = new EapMailNotification([
            'type' => 'new_mail_user',
        ]);

        $mail->eap_messages()->save($message);
        $mail->eap_notifications()->save($user_notification);

        $eap_user = EapUser::query()->find($mail->user_id);
        $language_line = EapLanguageLines::query()->where('key', 'new_operator_message_notification')->first();
        $message = data_get($language_line->text, ($eap_user->language) ? $eap_user->language->code : 'en');

        EapUser::query()->find($mail->user_id)->notify(new EapMessageCreated($message));

        return redirect()->back();
    }

    public function filter_view()
    {
        $companies = Company::query()->where('active', 1)->get();
        $users = EapUser::all();

        return view('operator.eap-online.mails.filter', ['companies' => $companies, 'users' => $users]);
    }

    public function filter(Request $request)
    {
        $filters = array_filter($request->all());
        $query = EapMail::query();

        $query = $query->whereHas('eap_user', function ($q): void {
            $q->where('country_id', Auth::user()->country->id);
        });

        foreach ($filters as $key => $value) {
            switch ($key) {
                case 'username':
                    $query = $query->where('user_id', $value);
                    break;
                case 'company':
                    $query = $query->whereHas('eap_user', function ($q) use ($value): void {
                        $q->where('company_id', $value);
                    });
                    break;
                case 'date':
                    if (! empty($value[0]) && ! empty($value[1])) {
                        $query = $query->whereBetween('date', [$value[0], $value[1]]);
                    }
                    break;
            }
        }

        $mails = $query->get();

        return view('operator.eap-online.mails.result', ['mails' => $mails]);
    }
}
