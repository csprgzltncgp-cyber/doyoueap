@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="{{asset('assets/css/cases/new.css')}}?t={{ time() }}">
    <link rel="stylesheet" href="{{asset('assets/css/workshops.css')}}?t={{ time() }}">
@endsection

@section('extra_js')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
     document.addEventListener("DOMContentLoaded", () => {
        initializeFlatpickr();

        Livewire.on('validationError', function (data) {
            Swal.fire({
                title: data.message,
                icon: 'error'
            });
        });

        Livewire.on('stepChanged', () => {
            initializeFlatpickr();
        });
    });

    function initializeFlatpickr() {
        flatpickr('.datepicker', {});

        flatpickr('.timepicker', {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
        });
    }
</script>
@endsection

@section('content')
    {{ Breadcrumbs::render('activity-plan.category.case.create', $activity_plan_category, $company,$country) }}

    @livewire('admin.activity-plan-category-case.create', [
        'activity_plan_category' => $activity_plan_category,
        'country' => $country,
        'company' => $company,
    ])
@endsection
