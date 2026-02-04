<table>
    <tbody>
    <tr>
        <td><b>{{Str::upper(__('riport.closed_cases'))}}:</b></td>
        <td>{{collect($cumulated_data['cumulated']['closed'])->sum()}}</td>
    <tr>

    <tr>
        <td><b>{{Str::upper(__('riport.interrupted_cases'))}}:</b></td>
        <td>{{collect($cumulated_data['cumulated']['interrupted'])->sum()}}</td>
    <tr>

    <tr>
        <td><b>{{Str::upper(__('riport.client_unreachable_cases'))}}:</b></td>
        <td>{{collect($cumulated_data['cumulated']['client_unreachable'])->sum()}}</td>
    <tr>

    <tr>
        <td><b>{{Str::upper(__('riport.workshop_participants'))}}:</b></td>
        <td>{{collect($cumulated_data['cumulated']['workshop'])->sum()}}</td>
    <tr>

    <tr>
        <td><b>{{Str::upper(__('riport.orientation_participants'))}}:</b></td>
        <td>{{collect($cumulated_data['cumulated']['orientation'])->sum()}}</td>
    <tr>

    <tr>
        <td><b>{{Str::upper(__('riport.health_day_participants'))}}:</b></td>
        <td>{{collect($cumulated_data['cumulated']['health_day'])->sum()}}</td>
    <tr>

    <tr>
        <td><b>{{Str::upper(__('riport.expert_outplacement_participants'))}}:</b></td>
        <td>{{collect($cumulated_data['cumulated']['expert_outplacement'])->sum()}}</td>
    <tr>

    <tr>
        <td><b>{{Str::upper(__('riport.prizegame_participants'))}}:</b></td>
        <td>{{collect($cumulated_data['cumulated']['prizegame'])->sum()}}</td>
    <tr>

    <tr>
        <td><b>{{Str::upper(__('riport.eap_logins'))}}:</b></td>
        <td>{{collect($cumulated_data['cumulated']['eap_logins'])->sum()}}</td>
    <tr>

    @if(array_key_exists('problem_type', $cumulated_data))
        <tr>
            <td><b>{{Str::upper(__('riport.problem_type'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($cumulated_data['problem_type'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp

        @foreach($cumulated_data['problem_type'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(), $total_count)}}%</td>
            </tr>
        @endforeach
    @endif

    <tr></tr>

    @if(array_key_exists('is_crisis', $cumulated_data) && count($cumulated_data['is_crisis']) > 1)
        <tr>
            <td><b>{{Str::upper(__('riport.is_crisis'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($cumulated_data['is_crisis'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp


        @foreach($cumulated_data['is_crisis'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(), $total_count)}}%</td>
            </tr>
        @endforeach

        <tr></tr>
    @endif

    @if(array_key_exists('problem_details', $cumulated_data))
        <tr>
            <td><b>{{Str::upper(__('riport.problem_details'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($cumulated_data['problem_details'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp

        @foreach($cumulated_data['problem_details'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(), $total_count)}}%</td>
            </tr>
        @endforeach
    @endif

    <tr></tr>

    @if(array_key_exists('gender', $cumulated_data))
        <tr>
            <td><b>{{Str::upper(__('riport.gender'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($cumulated_data['gender'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp


        @foreach($cumulated_data['gender'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(),$total_count)}}%</td>
            </tr>
        @endforeach

    @endif

    <tr></tr>

    @if(array_key_exists('employee_or_family_member', $cumulated_data))
        <tr>
            <td><b>{{Str::upper(__('riport.employee_or_family_member'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($cumulated_data['employee_or_family_member'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp


        @foreach($cumulated_data['employee_or_family_member'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(),$total_count)}}%</td>
            </tr>
        @endforeach

    @endif

    <tr></tr>

    @if(array_key_exists('age', $cumulated_data))
        <tr>
            <td><b>{{Str::upper(__('riport.age'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

           foreach ($cumulated_data['age'] as $value){
               $total_count += collect($value['count'])->sum();
           }
        @endphp


        @foreach($cumulated_data['age'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(),$total_count)}}%</td>
            </tr>
        @endforeach

    @endif

    <tr></tr>

    @if(array_key_exists('language', $cumulated_data))
        <tr>
            <td><b>{{Str::upper(__('riport.language'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($cumulated_data['language'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp


        @foreach($cumulated_data['language'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(),$total_count)}}%</td>
            </tr>
        @endforeach
    @endif

    <tr></tr>

    @if(array_key_exists('place_of_receipt', $cumulated_data))
        <tr>
            <td><b>{{Str::upper(__('riport.place_of_receipt'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

           foreach ($cumulated_data['place_of_receipt'] as $value){
               $total_count += collect($value['count'])->sum();
           }
        @endphp


        @foreach($cumulated_data['place_of_receipt'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(),$total_count)}}%</td>
            </tr>
        @endforeach

    @endif

    <tr></tr>

    @if(array_key_exists('source', $cumulated_data))
        <tr>
            <td><b>{{Str::upper(__('riport.source'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($cumulated_data['source'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp


        @foreach($cumulated_data['source'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(),$total_count)}}%</td>
            </tr>
        @endforeach

    @endif

    <tr></tr>
    </tbody>
</table>
