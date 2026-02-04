<table>
    <thead>
      <tr>
          <th style="background-color: #a6a6a6; font-weight: bold;">Cég</th>
          <th style="background-color: #a6a6a6; font-weight: bold;">Ország</th>
          <th style="background-color: #a6a6a6; font-weight: bold;">Esetek száma</th>
          <th style="background-color: #a6a6a6; font-weight: bold;">Létszám</th>
          <th style="background-color: #a6a6a6; font-weight: bold;">Utilizáció</th>
      </tr>
    </thead>
    <tbody>
        @foreach($companies as $company => $countries)
            @foreach($countries as $country => $data)
                <tr>
                    <td>{{$company}}</td>
                    <td>{{$country}}</td>
                    <td>{{$data['original_case_count']}}</td>
                    <td>{{$data['head_count']}}</td>
                    <td>{{$data['utilization']}}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
