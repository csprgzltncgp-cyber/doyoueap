<table>
    <thead>
        <tr>
            <th>Date of intake</th>
            <th>Company name</th>
            <th>Language</th>
            <th>Type of counselling</th>
            <th>Type of issue</th>
            <th>Employee type</th>
            <th>Gender</th>
            <th>Age</th>
            <th>Referral source</th>
            <th>Issue</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $case_data)
            <tr>
                <td>{{$case_data['date_of_intake']}}</td>
                <td>{{$case_data['company_name']}}</td>
                <td>{{$case_data['language']}}</td>
                <td>{{$case_data['type_of_consuelling']}}</td>
                <td>{{$case_data['type_of_issue']}}</td>
                <td>{{$case_data['employee_type']}}</td>
                <td>{{$case_data['gender']}}</td>
                <td>{{$case_data['age']}}</td>
                <td>{{$case_data['refferral_source']}}</td>
                <td>{{$case_data['issue']}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
