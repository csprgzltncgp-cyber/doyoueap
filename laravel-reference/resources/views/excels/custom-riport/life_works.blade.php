<table>
    <thead>
    <tr>
        <th>Case Open Date</th>
        <th>Source Case Id</th>
        <th>Org ID</th>
        <th>Location</th>
        <th>Modality</th>
        <th>Client Category</th>
        <th>Gender</th>
        <th>Year Of Birth</th>
        <th>Source of Information</th>
        <th>Service</th>
        <th>Presenting Issue Category</th>
        <th>Presenting Sub-issue</th>
        <th>Business Unit</th>
        <th>Years of Service</th>
        <th>Job Band</th>
        <th>HR Business</th>
        <th>Referral Source</th>
        <th>Client Status</th>
        <th>COVID-19</th>
        <th># Sessions Provided</th>
        <th>Session Date(s)</th>
        <th>Case Status</th>
        <th>Notes</th>
        <th>Org Name</th>
        <th>January</th>
        <th>February</th>
        <th>March</th>
        <th>April</th>
        <th>May</th>
        <th>June</th>
        <th>July</th>
        <th>August</th>
        <th>September</th>
        <th>October</th>
        <th>November</th>
        <th>December</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $case_data)
        <tr>
            <td>{{Carbon\Carbon::parse($case_data['creation_date'])->format('Y-m-d')}}</td>
            <td>{{$case_data['case_identifier']}}</td>
            <td>{{$case_data['org_id']}}</td>
            <td>{{$case_data['location']}}</td>
            <td>{{$case_data['modality']}}</td>
            <td>{{$case_data['client_category']}}</td>
            <td>{{$case_data['gender']}}</td>
            <td>{{$case_data['year_of_birth']}}</td>
            <td>{{$case_data['source_of_information']}}</td>
            <td>{{$case_data['service']}}</td>
            <td>{{$case_data['issue']}}</td>
            <td>{{$case_data['sub-issue']}}</td>
            <td>{{$case_data['business_unit']}}</td>
            <td>{{$case_data['years_of_service']}}</td>
            <td>{{$case_data['job_band']}}</td>
            <td>{{$case_data['hr_business']}}</td>
            <td>{{$case_data['referral_source']}}</td>
            <td>{{$case_data['client_status']}}</td>
            <td>{{$case_data['covid_19']}}</td>
            <td>{{$case_data['sessions_provided']}}</td>
            <td>{{$case_data['session_dates']}}</td>
            <td>{{$case_data['case_status']}}</td>
            <td>{{$case_data['notes']}}</td>
            <td>{{$case_data['org_name']}}</td>
            @for($i = 1; $i <= 12; $i++)
                @if(intval($i) == intval(Carbon\Carbon::parse(str_replace('.', '-', $case_data['creation_date']))->month))
                    <td>{{$case_data['consultations_count']}}</td>
                @else
                    <td></td>
                @endif
            @endfor
        </tr>
    @endforeach
    </tbody>
</table>
