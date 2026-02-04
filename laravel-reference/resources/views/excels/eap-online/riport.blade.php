<table>
    <tbody>
    @if(array_key_exists('all_registers' , $generated_riport))
        <tr>
            <td><b>{{Str::upper(__('eap-online.riports.all_registration_statistics'))}}:</b></td>
            <td>{{collect($generated_riport['all_registers'])->last()}}</td>
        <tr>
    @endif

    @if(array_key_exists('logins' , $generated_riport))
        <tr>
            <td><b>{{Str::upper(__('eap-online.riports.login_statistics'))}}:</b></td>
            <td>{{collect($generated_riport['logins'])->sum()}}</td>
        <tr>
    @endif

    @if(array_key_exists('articles', $generated_riport))
        <tr>
            <td><b>{{Str::upper(__('eap-online.articles.articles'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($generated_riport['articles'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp

        @foreach($generated_riport['articles'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(), $total_count)}}%</td>
            </tr>
        @endforeach
    @endif

    <tr></tr>

    @if(array_key_exists('videos', $generated_riport))
        <tr>
            <td><b>{{Str::upper(__('eap-online.videos.menu'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($generated_riport['videos'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp

        @foreach($generated_riport['videos'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(), $total_count)}}%</td>
            </tr>
        @endforeach
    @endif

    <tr></tr>

    @if(array_key_exists('podcasts', $generated_riport))
        <tr>
            <td><b>{{Str::upper(__('eap-online.podcasts.menu'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($generated_riport['podcasts'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp

        @foreach($generated_riport['podcasts'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(), $total_count)}}%</td>
            </tr>
        @endforeach
    @endif

    <tr></tr>

    @if(array_key_exists('self_help', $generated_riport))
        <tr>
            <td><b>{{Str::upper(__('eap-online.menu-visibilities.self_help'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($generated_riport['self_help'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp

        @foreach($generated_riport['self_help'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(), $total_count)}}%</td>
            </tr>
        @endforeach
    @endif

    <tr></tr>

    @if(array_key_exists('assessment', $generated_riport))
        <tr>
            <td><b>{{Str::upper(__('eap-online.menu-visibilities.assessment'))}}:</b></td>
        </tr>

        @php
            $total_count = 0;

            foreach ($generated_riport['assessment'] as $value){
                $total_count += collect($value['count'])->sum();
            }
        @endphp

        @foreach($generated_riport['assessment'] as $translation => $value)
            <tr>
                <td>{{$translation}}</td>
                <td>{{calculate_percentage(collect($value['count'])->sum(), $total_count)}}%</td>
            </tr>
        @endforeach
    @endif

    <tr></tr>
    </tbody>
</table>
