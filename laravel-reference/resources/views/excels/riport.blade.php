<table>
    <tbody>
    <tr>
        <td><b>{{Str::upper(__('riport.closed_cases'))}}:</b></td>
        <td>{{collect($generated_riport['case_numbers']['closed'])->sum() + collect($generated_riport['case_numbers']['in_progress'])->last()}}</td>
    </tr>
    <tr>
        <td><b>{{Str::upper(__('riport.interrupted_cases'))}}:</b></td>
        <td>{{collect($generated_riport['case_numbers']['interrupted'])->sum()}}</td>
    </tr>
    <tr>
        <td><b>{{Str::upper(__('riport.client_unreachable_cases'))}}:</b></td>
        <td>{{collect($generated_riport['case_numbers']['client_unreachable'])->sum()}}</td>
    <tr>

    <tr>
        <td><b>{{Str::upper(__('riport.consultations'))}}:</b></td>
        <td>{{collect($generated_riport['consultations']['count'])->sum() + collect($generated_riport['ongoing_consultations']['count'])->last()}}</td>
    <tr>

    <tr>
        <td><b>{{Str::upper(__('riport.workshop_participants'))}}:</b></td>
        <td>{{collect($generated_riport['workshop']['participants_number'])->sum()}}</td>
    </tr>

    <tr>
        <td><b>{{Str::upper(__('riport.crisis_participants'))}}:</b></td>
        <td>{{collect($generated_riport['crisis']['participants_number'])->sum()}}</td>
    </tr>

    <tr>
        <td><b>{{Str::upper(__('riport.orientation_participants'))}}:</b></td>
        <td>{{collect($generated_riport['orientation']['participants_number'])->sum()}}</td>
    </tr>

    <tr>
        <td><b>{{Str::upper(__('riport.health_day_participants'))}}:</b></td>
        <td>{{collect($generated_riport['health_day']['participants_number'])->sum()}}</td>
    </tr>

    <tr>
        <td><b>{{Str::upper(__('riport.expert_outplacement_participants'))}}:</b></td>
        <td>{{collect($generated_riport['expert_outplacement']['participants_number'])->sum()}}</td>
    </tr>

    <tr>
        <td><b>{{Str::upper(__('riport.prizegame_participants'))}}:</b></td>
        <td>{{collect($generated_riport['prizegame']['participants_number'])->sum()}}</td>
    <tr>
        
    {{-- Show onsite consultation numbers for:
        - Google Switzerland (215)
        - Tesco Hungary (1255)
        - Tesco Slovakia (1254)
        - Tesco Czech Republic (1253)
    --}}
    @if(data_get($generated_riport, 'onsite_consultations') && in_array(auth()->user()->companies->first()->id, [215, 1255, 1254, 1253]))
        <tr>
            <td><b>{{Str::upper(__('riport.onsite_consultations_number'))}}:</b></td>
            <td>{{collect($generated_riport['onsite_consultations']['count'])->sum()}}</td>
        <tr>
    @endif

    <tr>
        <td><b>{{Str::upper(__('riport.eap_logins'))}}:</b></td>
        <td>{{collect($generated_riport['eap_logins'])->sum()}}</td>
    <tr>

        @if(array_key_exists('problem_type', $generated_riport))
        <tr>
            <td><b>{{Str::upper(__('riport.problem_type'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($generated_riport['problem_type'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp

        @foreach($generated_riport['problem_type'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(), $total_count)}}%</td>
            </tr>
        @endforeach
    @endif

    <tr></tr>

    @if(array_key_exists('is_crisis', $generated_riport) && count($generated_riport['is_crisis']) > 1)
        <tr>
            <td><b>{{Str::upper(__('riport.is_crisis'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($generated_riport['is_crisis'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp


        @foreach($generated_riport['is_crisis'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(), $total_count)}}%</td>
            </tr>
        @endforeach

        <tr></tr>
    @endif

    @if(array_key_exists('problem_details', $generated_riport))
        <tr>
            <td><b>{{Str::upper(__('riport.problem_details'))}}:</b></td>
        </tr>
        
        @foreach(collect($generated_riport['problem_details'])->groupBy(fn($value) => is_array($value['permission']) ? collect($value['permission'])->first() : $value['permission'], true) as $permission_group_name => $values)
            <tr></tr>
            <tr>
                <td>
                    <b>{{\Illuminate\Support\Str::title( $permission_group_name )}}</b>
                </td>
            </tr>
            @foreach($values as $type_name => $type_values)
                @php
                    foreach ($type_values as $key => $value) {
                        // Do not sum the 'permission' key, as it is not a numeric value.
                        if (is_array($value) && $key !== 'permission') {
                            $type_values[$key] = collect($value)->sum();
                        }
                    };

                    $total_count = 0;
                    foreach ($values as $value){
                        $total_count += collect($value['count'])->sum();
                    }

                    $type_values['total_count'] = $total_count;


                    // Remove the word "suicide" from the case input value line 59.
                    if ($type_values['id'] === 59) {
                        $type_name = Str::of($type_name)->afterLast(', ')->ucfirst();
                    }

                    if(($percentage = (int) calculate_percentage($type_values['count'], $type_values['total_count'])) <= 0){
                        continue;
                    }
                @endphp
                <tr>
                    <td>{{$type_name}}</td>
                    <td>{{calculate_percentage(collect($type_values['count'])->sum(), $type_values['total_count'])}}%</td>
                </tr>
            @endforeach
        @endforeach
    @endif

    <tr></tr>

    @if(array_key_exists('gender', $generated_riport))
        <tr>
            <td><b>{{Str::upper(__('riport.gender'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($generated_riport['gender'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp


        @foreach($generated_riport['gender'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(),$total_count)}}%</td>
            </tr>
        @endforeach

    @endif

    <tr></tr>

    @if(array_key_exists('employee_or_family_member', $generated_riport))
        <tr>
            <td><b>{{Str::upper(__('riport.employee_or_family_member'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($generated_riport['employee_or_family_member'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp


        @foreach($generated_riport['employee_or_family_member'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(),$total_count)}}%</td>
            </tr>
        @endforeach

    @endif

    <tr></tr>

    @if(array_key_exists('age', $generated_riport))
        <tr>
            <td><b>{{Str::upper(__('riport.age'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

           foreach ($generated_riport['age'] as $value){
               $total_count += collect($value['count'])->sum();
           }
        @endphp


        @foreach($generated_riport['age'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(),$total_count)}}%</td>
            </tr>
        @endforeach

    @endif

    <tr></tr>

    @if(data_get($generated_riport, 'type_of_problem'))

        <tr>
            <td><b>{{Str::upper(__('riport.type_of_problem'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

        foreach ($generated_riport['type_of_problem'] as $value){
            $total_count += collect($value['count'])->sum();
        }
        @endphp


        @foreach($generated_riport['type_of_problem'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(),$total_count)}}%</td>
            </tr>
        @endforeach

    @endif

    <tr></tr>

    @if(array_key_exists('language', $generated_riport))
        <tr>
            <td><b>{{Str::upper(__('riport.language'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($generated_riport['language'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp


        @foreach($generated_riport['language'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(),$total_count)}}%</td>
            </tr>
        @endforeach
    @endif

    <tr></tr>

    @if(array_key_exists('place_of_receipt', $generated_riport))
        <tr>
            <td><b>{{Str::upper(__('riport.place_of_receipt'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

           foreach ($generated_riport['place_of_receipt'] as $value){
               $total_count += collect($value['count'])->sum();
           }
        @endphp


        @foreach($generated_riport['place_of_receipt'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(),$total_count)}}%</td>
            </tr>
        @endforeach

    @endif

    <tr></tr>

    @if(array_key_exists('source', $generated_riport))
        <tr>
            <td><b>{{Str::upper(__('riport.source'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($generated_riport['source'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp


        @foreach($generated_riport['source'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(),$total_count)}}%</td>
            </tr>
        @endforeach

    @endif

    @if(array_key_exists('valeo_workplace_1', $generated_riport))
        <tr></tr>

        <tr>
            <td><b>{{Str::upper(__('riport.valeo_workplace_1'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($generated_riport['valeo_workplace_1'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp


        @foreach($generated_riport['valeo_workplace_1'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(),$total_count)}}%</td>
            </tr>
        @endforeach

    @endif

    @if(array_key_exists('valeo_workplace_2', $generated_riport))
        <tr></tr>

        <tr>
            <td><b>{{Str::upper(__('riport.valeo_workplace_2'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($generated_riport['valeo_workplace_2'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp


        @foreach($generated_riport['valeo_workplace_2'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(),$total_count)}}%</td>
            </tr>
        @endforeach

    @endif

    @if(array_key_exists('hydro_workplace', $generated_riport))
        <tr></tr>

        <tr>
            <td><b>{{Str::upper(__('riport.hydro_workplace'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($generated_riport['hydro_workplace'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp


        @foreach($generated_riport['hydro_workplace'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(),$total_count)}}%</td>
            </tr>
        @endforeach

    @endif

    @if(array_key_exists('pse_workplace', $generated_riport))
        <tr></tr>

        <tr>
            <td><b>{{Str::upper(__('riport.pse_workplace'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($generated_riport['pse_workplace'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp


        @foreach($generated_riport['pse_workplace'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(),$total_count)}}%</td>
            </tr>
        @endforeach

    @endif

    @if(array_key_exists('michelin_workplace', $generated_riport))
        <tr></tr>

        <tr>
            <td><b>{{Str::upper(__('riport.michelin_workplace'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($generated_riport['michelin_workplace'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp


        @foreach($generated_riport['michelin_workplace'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(),$total_count)}}%</td>
            </tr>
        @endforeach

    @endif

    @if(array_key_exists('sk_battery_workplace', $generated_riport))
        <tr></tr>

        <tr>
            <td><b>{{Str::upper(__('riport.sk_battery_workplace'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($generated_riport['sk_battery_workplace'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp


        @foreach($generated_riport['sk_battery_workplace'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(),$total_count)}}%</td>
            </tr>
        @endforeach

    @endif

    @if(array_key_exists('grupa_workplace', $generated_riport))
        <tr></tr>

        <tr>
            <td><b>{{Str::upper(__('riport.grupa_workplace'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($generated_riport['grupa_workplace'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp


        @foreach($generated_riport['grupa_workplace'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(),$total_count)}}%</td>
            </tr>
        @endforeach

    @endif

    @if(array_key_exists('robert_bosch_workplace', $generated_riport))
        <tr></tr>

        <tr>
            <td><b>{{Str::upper(__('riport.robert_bosch_workplace'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($generated_riport['robert_bosch_workplace'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp


        @foreach($generated_riport['robert_bosch_workplace'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(),$total_count)}}%</td>
            </tr>
        @endforeach

    @endif

    @if(array_key_exists('gsk_workplace', $generated_riport))
        <tr></tr>

        <tr>
            <td><b>{{Str::upper(__('riport.gsk_workplace'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($generated_riport['gsk_workplace'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp


        @foreach($generated_riport['gsk_workplace'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(),$total_count)}}%</td>
            </tr>
        @endforeach

    @endif

    @if(array_key_exists('johnson_and_johnson_workplace', $generated_riport))
        <tr></tr>

        <tr>
            <td><b>{{Str::upper(__('riport.johnson_and_johnson_workplace'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($generated_riport['johnson_and_johnson_workplace'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp


        @foreach($generated_riport['johnson_and_johnson_workplace'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(),$total_count)}}%</td>
            </tr>
        @endforeach

    @endif

    @if(array_key_exists('syngenta_workplace', $generated_riport))
        <tr></tr>

        <tr>
            <td><b>{{Str::upper(__('riport.syngenta_workplace'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($generated_riport['syngenta_workplace'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp


        @foreach($generated_riport['syngenta_workplace'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(),$total_count)}}%</td>
            </tr>
        @endforeach

    @endif

    @if(array_key_exists('nestle_workplace', $generated_riport))
        <tr></tr>

        <tr>
            <td><b>{{Str::upper(__('riport.nestle_workplace'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($generated_riport['nestle_workplace'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp


        @foreach($generated_riport['nestle_workplace'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(),$total_count)}}%</td>
            </tr>
        @endforeach

    @endif

    @if(data_get($generated_riport, 'mahle_pl_workplace'))
        <tr></tr>

        <tr>
            <td><b>{{Str::upper(__('riport.mahle_pl_workplace'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($generated_riport['mahle_pl_workplace'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp


        @foreach($generated_riport['mahle_pl_workplace'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(),$total_count)}}%</td>
            </tr>
        @endforeach

    @endif

    @if(data_get($generated_riport, 'lpp_workplace'))
        <tr></tr>

        <tr>
            <td><b>{{Str::upper(__('riport.lpp_workplace'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($generated_riport['lpp_workplace'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp


        @foreach($generated_riport['lpp_workplace'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(),$total_count)}}%</td>
            </tr>
        @endforeach

    @endif

    @if(data_get($generated_riport, 'amrest_workplace'))
        <tr></tr>

        <tr>
            <td><b>{{Str::upper(__('riport.amrest_workplace'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($generated_riport['amrest_workplace'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp


        @foreach($generated_riport['amrest_workplace'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(),$total_count)}}%</td>
            </tr>
        @endforeach

    @endif

    @if(data_get($generated_riport, 'kuka_workplace'))
        <tr></tr>

        <tr>
            <td><b>{{Str::upper(__('riport.kuka_workplace'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($generated_riport['kuka_workplace'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp


        @foreach($generated_riport['kuka_workplace'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(),$total_count)}}%</td>
            </tr>
        @endforeach

    @endif

    <tr></tr>
    </tbody>
</table>
