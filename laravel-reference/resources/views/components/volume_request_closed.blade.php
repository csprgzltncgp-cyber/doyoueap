@props([
    'message',
    'title',
])

<div
        x-data="{show:false}"
        x-init="setTimeout(()=>{show = true},100)"
        x-show="show"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="transform scale-50 opacity-0"
        x-transition:enter-end="transform scale-100 opacity-100"
        x-transition:leave="transition ease-out duration-300"
        x-transition:leave-start="transform scale-100 opacity-100"
        x-transition:leave-end="transform scale-50 opacity-0"
        class="z-50 fixed top-0 left-0 flex justify-center items-center w-full h-full mt-10"
>
    <div class="rounded-xl bg-purple p-10 max-w-xl z-20" style="box-shadow: 0px 20px 20px rgba(0,0,0,0.3);">
        <div class="d-flex flex-col items-center space-y-10">
            <div class="text-white text-center break-words">
                <h1 class="text-3xl mb-5 font-bold">{{$title}}</h1>
                @if(!empty($message))
                    <p class="text-xl">{{$message}}</p>
                @endif
            </div>
            <div class="flex justify-center items-center space-x-5 text-white capitalize">
                    <button
                            type="button"
                            x-on:click="show = false"
                            class="transition duration-300 ease-in-out px-12 py-2 border-2 border-white rounded-full hover:bg-white hover:bg-opacity-40">
                        OK
                    </button>
                </div>
        </div>
    </div>
</div>
