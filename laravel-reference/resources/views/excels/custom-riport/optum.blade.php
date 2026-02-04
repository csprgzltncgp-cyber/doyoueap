<table>
    <thead>
    <tr>
        <th>Contract Organisation</th>
        <th>Contract Report ID</th>
        <th>Reference Number</th>
        <th>Optum Reference</th>
        <th>Country</th>
        <th>Case Start Date</th>
        <th>Service Received</th>
        <th>Presenting Issue</th>
        <th>Level of Functioning at Case Opening (0-10)</th>
        <th>Level of Functioning at Case Closure (0-10)</th>
        <th>Level of Stress at Case Opening (0-10)</th>
        <th>Level of Stress at Case Closure (0-10)</th>
        <th>Days Absent of Work</th>
        <th>Male or Female</th>
        <th>Information Source</th>
        <th>Employee Type</th>
        <th>Age Rang</th>
        <th>Total Number of Sessions In Total</th>
        <th>Case Outcome</th>
        <th>Date Case Closed</th>
        <th>PHQ9 - Opening Score</th>
        <th>PHQ9 - Closing Score</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $case_data)
        <tr>
            <td>{{$case_data['contract_organisation']}}</td>
            <td>{{$case_data['contract_report_id']}}</td>
            <td>{{$case_data['reference_id']}}</td>
            <td>{{$case_data['optum_reference']}}</td>
            <td>{{$case_data['country']}}</td>
            <td>{{$case_data['case_start_date']}}</td>
            <td>{{$case_data['service_received']}}</td>
            <td>{{$case_data['presenting_issue']}}</td>
            <td>{{$case_data['level_of_functioning_at_case_opening']}}</td>
            <td>{{$case_data['level_of_functioning_at_case_closure']}}</td>
            <td>{{$case_data['level_of_stress_at_case_opening']}}</td>
            <td>{{$case_data['level_of_stress_at_case_closure']}}</td>
            <td>{{$case_data['days_absent_of_work']}}</td>
            <td>{{$case_data['male_or_female']}}</td>
            <td>{{$case_data['information_source']}}</td>
            <td>{{$case_data['employee_type']}}</td>
            <td>{{$case_data['age_rang']}}</td>
            <td>{{$case_data['total_number_of_sessions']}}</td>
            <td>{{$case_data['case_outcome']}}</td>
            <td>{{$case_data['date_case_closed']}}</td>
            <td>{{$case_data['opening_score']}}</td>
            <td>{{$case_data['closing_score']}}</td>
        </tr>
    @endforeach
    </tbody>
</table>