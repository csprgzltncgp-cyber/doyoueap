@extends('layout.master')

@section('title')
    Admin Dashboard
@endsection

@section('extra_css')
    <link rel="stylesheet" href="/assets/css/cases/view.css?t={{time()}}">
@endsection

@section('extra_js')
    <script>
        function deleteActivityPlanCategoryCase() {
            Swal.fire({
                title: '{{__('common.are-you-sure-to-delete')}}',
                text: '{{__('common.operation-cannot-undone')}}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{__('common.yes-delete-it')}}'
            }).then((result) => {
                if (result.value) {
                    Livewire.emit('deleteActivityPlanCategoryCase')
                }
            });
        }
    </script>
@endsection

@section('content')
    {{ Breadcrumbs::render('activity-plan.category.case.show', $activity_plan_category, $activity_plan_category_case) }}

    @livewire('admin.activity-plan-category-case.show', [
        'activity_plan_category' => $activity_plan_category,
        'activity_plan_category_case' => $activity_plan_category_case,
    ])
@endsection
