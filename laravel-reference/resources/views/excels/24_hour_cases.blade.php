<table>
    <thead>
      <tr>
          <th style="background-color: #a6a6a6; font-weight: bold;">Eset azon.</th>
          <th style="background-color: #a6a6a6; font-weight: bold;">Eset lap létrejötte</th>
          <th style="background-color: #a6a6a6; font-weight: bold;">Kiközvetités napja</th>
          <th style="background-color: #a6a6a6; font-weight: bold;">Ország</th>
          <th style="background-color: #a6a6a6; font-weight: bold;">Jogosultság</th>
          <th style="background-color: #a6a6a6; font-weight: bold;">Szakértő</th>
          <th style="background-color: #a6a6a6; font-weight: bold;">Szakértő díjazása</th>
          <th style="background-color: #a6a6a6; font-weight: bold;">Cég</th>
      </tr>
    </thead>
    <tbody>
        @foreach($cases as $case)
          <tr>
            <td>{{$case->case_identifier}}</td>
            <td>{{$case->created_at->format('Y.m.d H:i')}}</td>
            <td>{{optional($case->first_assigned_expert_by_date())->pivot->created_at->format('Y-m-d H:i')}}</td>
            <td>{{$case->country->name}}</td>
            <td>{{$case->case_type->permission->slug}}</td>
            <td>{{optional($case->first_assigned_expert_by_date())->name ?? 'Nincs szakértő'}}</td>
            @if($case->first_assigned_expert_by_date())
                <td>
                    @switch($case->first_assigned_expert_by_date()->invoice_datas->invoicing_type)
                        @case(\App\Enums\InvoicingType::TYPE_FIXED)
                            FIX ({{$case->first_assigned_expert_by_date()->invoice_datas->fixed_wage}} {{$case->first_assigned_expert_by_date()->invoice_datas->currency}})
                            @break

                        @case(\App\Enums\InvoicingType::TYPE_CUSTOM)
                            EGYEDI
                            @break

                        @default
                            @if($case->first_assigned_expert_by_date()->invoice_datas->hourly_rate_30) 30 perc: {{$case->first_assigned_expert_by_date()->invoice_datas->hourly_rate_30}} {{$case->first_assigned_expert_by_date()->invoice_datas->currency}} / @endif
                            50 perc: {{$case->first_assigned_expert_by_date()->invoice_datas->hourly_rate_50}} {{$case->first_assigned_expert_by_date()->invoice_datas->currency}}
                    @endswitch
                </td>
            @endif
            <td>{{$case->company->name}}</td>
          </tr>
        @endforeach
    </tbody>
</table>
