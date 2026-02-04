<table>
      <thead>
        <tr>
            @foreach($data['header'] as $value)
              <th>{{$value}}</th>
            @endforeach
        </tr>
      </thead>
      <tbody>
          @foreach($data['data'] as $value)
            <tr>
              @foreach($data['header'] as $v)
                <td>{{isset($value[$v]) ? $value[$v] : null}}</td>
              @endforeach
            </tr>
          @endforeach
      </tbody>
</table>
