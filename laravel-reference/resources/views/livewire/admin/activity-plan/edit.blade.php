<div>
    @push('livewire_js')
        <script>
            function addActivityPlanCategory() {
                Swal.fire({
                    title: '{{__('activity-plan.create-new-category')}}',
                    html:
                        `
                        <input placeholder="{{__('activity-plan.category-name')}}" id="activity_plan_category_name" class="swal2-input">
                        <label class="checkbox-container mt-0 text-left"
                            style="color: rgb(89, 198, 198); padding: 10px 0 10px 15px; border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px;">
                            {{__('activity-plan.all-companies')}}
                            <input type="checkbox" class="d-none" id="activity_plan_category_all_companies">
                            <span class="checkmark d-flex justify-content-center align-items-center"
                                style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="checked d-none"
                                    style="width: 25px; height: 25px; color: white" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="unchecked"
                                    style="width: 20px; height: 20px;" fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </span>
                        </label>
                        `,
                    showLoaderOnConfirm: true,
                    confirmButtonText: '{{__('common.save')}}',
                    stopKeydownPropagation: false,
                    onOpen: () => {
                        document.querySelector('#activity_plan_category_all_companies').addEventListener('change', function () {
                            if (this.checked) {
                                document.querySelector('svg.checked').classList.remove('d-none');
                                document.querySelector('svg.unchecked').classList.add('d-none');
                            }else{
                                document.querySelector('svg.checked').classList.add('d-none');
                                document.querySelector('svg.unchecked').classList.remove('d-none');
                            }
                        });
                    },
                    preConfirm: () => {
                        const name = document.querySelector('#activity_plan_category_name').value;
                        const all_companies = document.querySelector('#activity_plan_category_all_companies').checked;

                        return {
                            name: name,
                            all_companies: all_companies,
                        }
                    }
                }).then((result) => {
                    if (result.value?.name) {
                        Livewire.emit('create_category', result.value.name, result.value.all_companies);
                    }
                });
            }

            function deleteActivityPlanCategory(id) {
                Swal.fire({
                    title: '{{__('common.are-you-sure-to-delete')}}',
                    text: "{{__('common.operation-cannot-undone')}}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '{{__('common.yes-delete-it')}}',
                    cancelButtonText: '{{__('common.cancel')}}',
                }).then((result) => {
                    if (result.value) {
                        Livewire.emit('delete_category', id);
                    }
                });
            }

            function addActivityPlanCategoryField(activity_plan_category_id) {
                const fieldTypes = @json(
                    collect(App\Enums\ActivityPlanCategoryFieldTypeEnum::cases())
                        ->map(fn($item) => [$item->value, $item->getTranslation()])
                        ->toArray()
                );

                Swal.fire({
                    title: '{{__('activity-plan.create-new-field')}}',
                    html:
                        '<input placeholder="{{__('activity-plan.field-name')}}" id="activity_plan_category_field_name" class="swal2-input">' +
                        '<select id="activity_plan_category_field_type" class="swal2-input">' +
                            '<option value="{{null}}" disabled selected>{{__('activity-plan.field-type')}}</option>' +
                            fieldTypes.map(type => `<option value="${type[0]}">${type[1]}</option>`).join('') +
                        '</select>' +
                        `
                        <label class="mt-3 checkbox-container text-left"
                            style="color: rgb(89, 198, 198); padding: 10px 0 10px 15px; border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px;">
                           {{__('activity-plan.field-is-highlighted')}}
                            <input type="checkbox" class="d-none" id="activity_plan_category_field_is_highlighted">
                            <span class="checkmark d-flex justify-content-center align-items-center"
                                style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="checked d-none"
                                    style="width: 25px; height: 25px; color: white" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="unchecked"
                                    style="width: 20px; height: 20px;" fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </span>
                        </label>
                        `,
                    showLoaderOnConfirm: true,
                    confirmButtonText: '{{__('common.save')}}',
                    stopKeydownPropagation: false,
                    onOpen: () => {
                        document.querySelector('#activity_plan_category_field_is_highlighted').addEventListener('change', function () {
                            if (this.checked) {
                                document.querySelector('svg.checked').classList.remove('d-none');
                                document.querySelector('svg.unchecked').classList.add('d-none');
                            }else{
                                document.querySelector('svg.checked').classList.add('d-none');
                                document.querySelector('svg.unchecked').classList.remove('d-none');
                            }
                        });
                    },
                    preConfirm: () => {
                        const name = document.querySelector('#activity_plan_category_field_name').value;
                        const type = document.querySelector('#activity_plan_category_field_type').value;
                        const is_highlighted = document.querySelector('#activity_plan_category_field_is_highlighted').checked;

                        return {
                            name: name,
                            type: type,
                            is_highlighted: is_highlighted
                        }
                    }
                }).then((result) => {
                    if (result.value?.name && result.value?.type) {
                        Livewire.emit(
                            'add_new_field',
                            result.value.name,
                            result.value.type,
                            activity_plan_category_id,
                            result.value.is_highlighted
                        );
                    }
                });
            }

            function deleteActivityPlanCategoryField(activity_plan_category_id, activity_plan_category_field_id) {
                Swal.fire({
                    title: '{{__('common.are-you-sure-to-delete')}}',
                    text: "{{__('common.operation-cannot-undone')}}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '{{__('common.yes-delete-it')}}',
                    cancelButtonText: '{{__('common.cancel')}}',
                }).then((result) => {
                    if (result.value) {
                        Livewire.emit('delete_field',
                            activity_plan_category_field_id,
                            activity_plan_category_id
                        );
                    }
                });
            }

            function editActivityPlanCategoryField(activity_plan_category_id, activity_plan_category_field_id, currentValue, isHighlighted) {
                Swal.fire({
                    title: '{{__('activity-plan.edit-field')}}',
                    html: '<input placeholder="{{__('activity-plan.field-name')}}" id="activity_plan_category_field_name" class="swal2-input" value="' + currentValue + '">' +
                        `
                        <label class="checkbox-container text-left"
                            style="color: rgb(89, 198, 198); padding: 10px 0 10px 15px; border: 2px solid rgb(89,198,198) !important; font-size: 16px; margin-top: 8px;">
                           {{__('activity-plan.field-is-highlighted')}}
                            <input type="checkbox" class="d-none" id="activity_plan_category_field_is_highlighted" ${isHighlighted ? 'checked' : ''}>
                            <span class="checkmark d-flex justify-content-center align-items-center"
                                style="left:auto; right: 0; height: 100%; width: 50px; border-left: 2px solid rgb(89,198,198) !important">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="checked ${isHighlighted ? '' : 'd-none'}"
                                    style="width: 25px; height: 25px; color: white" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="unchecked ${isHighlighted ? 'd-none' : ''}"
                                    style="width: 20px; height: 20px;" fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </span>
                        </label>
                        `,
                    showLoaderOnConfirm: true,
                    confirmButtonText: '{{__('common.save')}}',
                    stopKeydownPropagation: false,
                    onOpen: () => {
                        document.querySelector('#activity_plan_category_field_is_highlighted').addEventListener('change', function () {
                            if (this.checked) {
                                document.querySelector('svg.checked').classList.remove('d-none');
                                document.querySelector('svg.unchecked').classList.add('d-none');
                            }else{
                                document.querySelector('svg.checked').classList.add('d-none');
                                document.querySelector('svg.unchecked').classList.remove('d-none');
                            }
                        });
                    },
                    preConfirm: () => {
                        const name = document.querySelector('#activity_plan_category_field_name').value;
                        const is_highlighted = document.querySelector('#activity_plan_category_field_is_highlighted').checked;

                        return {
                            name: name,
                            is_highlighted: is_highlighted
                        }
                    }
                }).then((result) => {
                    if (result.value?.name) {
                        Livewire.emit(
                            'edit_field',
                            activity_plan_category_field_id,
                            activity_plan_category_id,
                            result.value.name,
                            result.value.is_highlighted
                        );
                    }
                });
            }
        </script>
    @endpush

    <h1 class="mb-3">{{__('activity-plan.create-edit-category')}}</h1>

    <a onclick="addActivityPlanCategory()" href="#" >
        {{__('activity-plan.create-new-category')}}
    </a>

    <div class="mt-5">
        @foreach($activity_plan_categories as $activity_plan_category)
            @livewire('admin.activity-plan-category.show', ['activity_plan_category' => $activity_plan_category], key($activity_plan_category->id))
        @endforeach

        @if(!$activity_plan_categories->count())
            <center>{{__('data.no_data')}}</center>
        @endif
    </div>
</div>
