<div class="row case-list-in-progress ml-0">
    <p>
        {{optional($mail->eap_user)->username}} - {{$mail->subject}} - {{$mail->category}}
        - {{optional(optional($mail->eap_user)->company)->name}}
        - {{$mail->date}}
    </p>

    <a class="btn-radius" href="{{route('operator.eap-online.mails.view', ['id' => $mail->id, 'page' => $page])}}">
        <img src="{{asset('assets/img/select.svg')}}" class="mr-1" style="height: 20px; width: 20px" alt="">
        {{__('common.select')}}
    </a>

    @if(!empty($mail->deleted_at))
        <p class="_3month">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg> {{__('eap-online.mails.deleted_mail')}}
        </p>
    @elseif($mail->has_operator_notification)
        <p class="not-accepted"> {{__('eap-online.mails.unread_mail')}}</p>
    @endif
</div>
