<table>
    <thead>
    <tr>
        <th>Case Creation Date</th>
        <th>ID</th>
        <th>Company</th>
        <th>Problem Type</th>
        <th>Contact Type</th>
        <th>Employee Or Family Member</th>
        <th>Consulting Language</th>
        <th>Gender</th>
        <th>Age</th>
        <th>Source</th>
        <th>Consulting type</th>
        <th>Ages in company</th>
        <th>Problem details</th>
        <th>City</th>
        <th>Status</th>
        <th>Case Status</th>
        <th>Function</th>
        <th>Number Of Consultations</th>
        <th>Dates Of Consultations</th>
        <th>Customer satisfaction</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $case_data)
        <tr>
            <td>{{$case_data['creation_date']}}</td>
            <td>{{$case_data['case_identifier']}}</td>
            <td>{{$case_data['company']}}</td>
            <td>{{$case_data['problem_type']}}</td>
            <td>{{$case_data['contact_type']}}</td>
            <td>{{$case_data['employee_or_family_member']}}</td>
            <td>{{$case_data['consulting_language']}}</td>
            <td>{{$case_data['gender']}}</td>
            <td>{{$case_data['age']}}</td>
            <td>{{$case_data['source']}}</td>
            <td>{{$case_data['consulting_type']}}</td>
            <td>{{$case_data['ages_in_company']}}</td>
            <td>{{$case_data['problem_details']}}</td>
            <td>{{$case_data['city']}}</td>
            <td>{{$case_data['status']}}</td>
            <td>{{$case_data['case_status']}}</td>
            <td>{{$case_data['function']}}</td>
            <td>{{$case_data['number_of_consultations']}}</td>
            <td>{{$case_data['dates_of_consultations']}}</td>
            <td>{{$case_data['customer_satisfaction']}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
