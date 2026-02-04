<?php

namespace App\Http\Controllers\Admin\EapOnline;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Country;
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
        $mails = EapMail::with(['eap_user', 'eap_notifications'])
            ->whereNull('deleted_at')
            ->orderBy('date', 'DESC')
            ->get();
        $countries = Country::all();
        $unread_mails = [];

        foreach ($countries as $country) {
            foreach ($mails as $mail) {
                if ($mail->country_id == $country->id) {
                    foreach ($mail->eap_notifications as $notification) {
                        if ($notification->type == 'new_mail_admin') {
                            $unread_mails[$country->code] = isset($unread_mails[$country->code]) ? $unread_mails[$country->code] + 1 : 1;
                        }
                    }
                }
            }
        }

        foreach ($mails as $mail) {
            foreach ($mail->eap_notifications as $notification) {
                if ($notification->type == 'new_mail_admin') {
                    $mail->setAttribute('is_new', true);
                    break;
                }
            }
        }

        return view('admin.eap-online.mails.list', ['mails' => $mails->sortBy('date', 1, true)->sortBy('is_new', 1, true), 'countries' => $countries, 'unread_mails' => $unread_mails]);
    }

    public function view($id)
    {
        $mail = EapMail::with(['eap_messages', 'eap_notifications', 'eap_messages.eap_attachments'])->where('id', $id)->first();

        if (! empty($mail->eap_notifications)) {
            foreach ($mail->eap_notifications as $notification) {
                if ($notification->type === 'new_mail_admin') {
                    $notification->delete();
                }
            }
        }

        return view('admin.eap-online.mails.view', ['mail' => $mail]);
    }

    public function reply(Request $request, $id)
    {
        $mail = EapMail::query()->find($id);

        $message = new EapMessage([
            'message' => nl2br((string) $request->get('message')),
            'type' => 'admin',
            'user_id' => Auth::id(),
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

        if (! empty($mail->eap_notifications)) {
            foreach ($mail->eap_notifications as $notification) {
                if ($notification->type === 'new_mail_operator') {
                    $notification->delete();
                }
            }
        }

        return redirect()->back();
    }

    public function filter_view()
    {
        $countries = Country::all();
        $companies = Company::query()->where('active', 1)->get();
        $users = EapUser::all();

        return view('admin.eap-online.mails.filter', ['countries' => $countries, 'companies' => $companies, 'users' => $users]);
    }

    public function filter(Request $request)
    {
        $filters = array_filter($request->all());
        $query = EapMail::query();

        foreach ($filters as $key => $value) {
            switch ($key) {
                case 'username':
                    $query = $query->where('user_id', $value);
                    break;
                case 'country':
                    $query = $query->whereHas('eap_user', function ($q) use ($value): void {
                        $q->where('country_id', $value);
                    });
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

        return view('admin.eap-online.mails.result', ['mails' => $mails]);
    }

    public function restore_notification(Request $request)
    {
        $request->validate([
            'mail_id' => ['required'],
        ]);

        EapMailNotification::query()->firstOrCreate([
            'mail_id' => $request->input('mail_id'),
            'type' => 'new_mail_admin',
        ], [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.eap-online.mails.list');
    }
}
