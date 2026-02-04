@push('livewire_js')
    <script src="{{asset('assets/js/task/index.js')}}?v={{time()}}"></script>
    <script>
        $('#orderBySelect').on('change', function(val) {
            var orderBy = $(this).val();
            @this.orderBy = orderBy;
        });

        $('#orderBySelect option[value="{{implode(',', $this->orderBy)}}"]').attr("selected",true);

        function deleteTask(id){
            Swal.fire({
                title: '{{__('common.are-you-sure-to-delete')}}',
                text: "{{__('common.operation-cannot-undone')}}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{__('common.yes-delete-it')}}',
                cancelButtonText: '{{__('common.cancel')}}',
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'DELETE',
                        data:{
                            id: id
                        },
                        url: '/ajax/delete-task/' + id,
                        success: function (data) {
                           location.reload();
                        }
                    });
                }
            });
        }
    </script>
@endpush

<div>
    <div class="mt-5 mb-1">
        <x-tasks.sort/>
    </div>

    @forelse($tasks as $task)
        <x-tasks :task="$task" :showToUser="true" :showDelete="true"/>
    @empty
        <p>{{__('task.no_issued_tasks')}}!</p>
    @endforelse

    @if($tasks->hasMorePages())
        <div class="d-flex justify-content-center mb-5" style="margin-bottom: -25px; margin-top:25px;">
            <button class="load-more-cases btn-radius" wire:click.prevent='loadMore'>
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width: 20px; height: 20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                </svg>
                {{__('common.load-more')}}
            </button>
            <button class="load-more-cases btn-radius" wire:click.prevent='loadAll'>
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" style="width: 20px; height: 20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 13l-7 7-7-7m14-8l-7 7-7-7"></path>
                </svg>
                {{__('common.load-all')}}
            </button>
        </div>
    @endif
</div>
