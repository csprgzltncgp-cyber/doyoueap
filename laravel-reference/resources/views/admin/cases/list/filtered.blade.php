@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/cases/list.css?v={{time()}}">
@endsection

@section('extra_js')
    <script>
        var select = 0;

        async function selectAllCases() {
            const query = window.location.search;
            try {
                return await $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'GET',
                    url: '/ajax/select-all-cases/' + query,
                });
            } catch (e) {
                Swal.fire(
                    'Hiba',
                    'Hiba történt a művelet végrehajtása közben (S3R)!',
                    'error'
                );
            }
        }

        async function selectAllClick() {
            select = !select;
            if (select) {
                $('#selectAllButton').addClass('active');
                $('form[name="excel_export"] button[type="submit"]').attr('disabled', true).css('opacity', '30%');

                const caseIds = await selectAllCases();

                JSON.parse(caseIds).forEach(function (id) {
                    const input = '<input type="hidden" name="cases[]" value="' + id + '">';
                    $('form[name="excel_export"]').append(input);
                });

                $('form[name="excel_export"] button[type="submit"]').attr('disabled', false).css('opacity', '100%');
            } else {
                $('form[name="excel_export"] input[name!="_token"]').remove();
                $('#selectAllButton').removeClass('active');
            }

            $('.case-list-holder .case-list').each(async function () {
                $(this).toggleClass('selected');
            });
        }

        function selectClick() {
            select = !select;
            if (select) {
                $('#selectButton').addClass('active');
            } else {
                $('form[name="excel_export"] input[name!="_token"]').remove();
                $('#selectButton').removeClass('active');
                $('.case-list.selected').removeClass('selected');
            }
        }

        $(function () {
            clickOnCase();
        });

        function clickOnCase() {
            $('.case-list-holder').on('click', '.case-list', function (e) {
                if (!select) {
                    const url = $(this).data('href');
                    window.location.href = url;
                }

                $(this).toggleClass('selected');
                const id = $(this).data('id');
                if ($(this).hasClass('selected')) {
                    const input = '<input type="hidden" name="cases[]" value="' + id + '">';
                    $('form[name="excel_export"]').append(input);
                } else {
                    $('form[name="excel_export"] input[value="' + id + '"]').remove();
                }
            });
        }

        function deleteCases() {
            Swal.fire({
                title: 'Biztosan törölni szeretnéd a kiválasztott eseteket?',
                text: "Ez a művelet nem vonható vissza!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Igen, töröljük!',
                cancelButtonText: 'Mégse'
            }).then((result) => {
                if (result.value) {
                    $('form[name="excel_export"]').attr('action', 'delete-all').submit();
                }
            });
        }

    </script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            {{ Breadcrumbs::render('closed-cases.filtered') }}

            <h1>
                {{ __('common.filter-results') }} ({{$cases->total()}} db) 
                @if($total_consultations)
                    - Konzultációk száma ({{$total_consultations}} db) 
                    - Időszak ({{$consultations_from}} - {{$consultations_to}})
                    - Szakértő ({{$expert->name}})
                @endif
            </h1>
        </div>
        <div class="col-12 button-holder">
            <div class="myBtn">
                <a class="button btn-radius d-flex" href="{{route('admin.cases.filter')}}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height:20px; width:20px" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    {{ __('common.new-filter')  }}</a>
            </div>
            <div class="myBtn">
                <form name="excel_export" method="post" action="export">
                    {{csrf_field()}}
                    <button class="button btn-radius" type="submit">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height: 20px; margin-bottom:3px"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        {{ __('common.save-excel') }}</button>
                </form>
            </div>
            <div class="myBtn">
                <button onclick="deleteCases()" class="button btn-radius d-flex align-items-center" href="{{route('admin.cases.filter')}}" style="padding: 12px;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="height:20px; width:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                </button>
            </div>

            <div class="myBtn float-right">
                <button class="button btn-radius" id="selectAllButton" onClick="selectAllClick()" style="margin-right: 0px !important;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height: 20px; margin-bottom:3px"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                    </svg>
                    {{ __('common.select-all') }}</button>
            </div>
            <div class="myBtn float-right" style="margin-right:0px;">
                <button class="button btn-radius" id="selectButton" onClick="selectClick()" >
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="height: 20px; margin-bottom:3px"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    {{ __('common.select-to-export') }}</button>
            </div>
        </div>
        <div class="col-12 case-list-holder">
            @foreach($cases as $case)
                @component('components.cases.list',['case' => $case])@endcomponent
            @endforeach
        </div>
        {{$cases->appends(request()->all())->links()}}
    </div>
@endsection
