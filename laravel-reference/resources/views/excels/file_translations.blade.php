<table>
    <thead>
      <tr>
          <th style="background-color: #a6a6a6; font-weight: bold;">Reference key</th>
          <th style="background-color: #a6a6a6; font-weight: bold;">English translation</th>
          <th style="background-color: #a6a6a6; font-weight: bold;">Current translation</th>
          <th style="background-color: #a6a6a6; font-weight: bold;">Correct translation</th>
      </tr>
    </thead>
    <tbody>
        @foreach($translations as $key => $value)
        <tr>
            <td>{{$key}}</td>
            <td>{{$english_translations[$key]}}</td>
            <td>{{$value}}</td>
            <td></td>
        </tr>
        @endforeach
    </tbody>
</table>
