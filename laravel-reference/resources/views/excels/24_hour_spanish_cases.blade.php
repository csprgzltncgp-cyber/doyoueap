<table>
    <thead>
      <tr>
          <th style="background-color: #a6a6a6; font-weight: bold;">Eset azon.</th>
          <th style="background-color: #a6a6a6; font-weight: bold;">Eset lap létrejötte</th>
          <th style="background-color: #a6a6a6; font-weight: bold;">Ország</th>
          <th style="background-color: #a6a6a6; font-weight: bold;">Jogosultság</th>
          <th style="background-color: #a6a6a6; font-weight: bold;">Cég</th>
      </tr>
    </thead>
    <tbody>
        @foreach($cases as $case)
          <tr>
            <td>{{$case->case_identifier}}</td>
            <td>{{$case->created_at->format('Y.m.d H:i')}}</td>
            <td>{{$case->country->name}}</td>
            <td>{{$case->case_type->permission->slug}}</td>
            <td>{{$case->company->name}}</td>
          </tr>
        @endforeach
    </tbody>
</table>
