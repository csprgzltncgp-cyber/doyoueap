<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Employee Or Family Member</th>
        <th>Company</th>
        <th>Gender</th>
        <th>Age</th>
        <th>City</th>
        <th>Country</th>
        <th>Is Crisis</th>
        <th>Problem Details</th>
        <th>Consulting Type</th>
        <th>Source</th>
        <th>Problem Type</th>
        <th>Case Creation Date</th>
        <th>Number Of Consultations</th>
        <th>Dates Of Consultations</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $case_data)
        <tr>
            <td>{{$case_data['case_identifier']}}</td>
            <td>{{$case_data['employee_or_family_member']}}</td>
            <td>{{$case_data['company']}}</td>
            <td>{{$case_data['gender']}}</td>
            <td>{{$case_data['age']}}</td>
            <td>{{$case_data['city']}}</td>
            <td>{{$case_data['country']}}</td>
            <td>{{$case_data['is_crisis']}}</td>
            <td>{{$case_data['problem_details']}}</td>
            <td>{{$case_data['consulting_type']}}</td>
            <td>{{$case_data['source']}}</td>
            <td>{{$case_data['problem_type']}}</td>
            <td>{{$case_data['creation_date']}}</td>
            <td>{{$case_data['number_of_consultations']}}</td>
            <td>{{$case_data['dates_of_consultations']}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
