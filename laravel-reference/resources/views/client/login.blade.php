@extends('layout.client.master_login')

@section('content')
    <form action="{{route('client.login')}}" method="post"
          class="w-full sm:w-2/5 xl:w-2/4 bg-white bg-opacity-70 py-28 px-10 md:px-20 relative h-auto flex flex-col items-center sm:-mt-7">
        @csrf
        <div class="mb-20 w-full">
            <label class="w-full">
                <input
                        class="outline-none w-full bg-white px-2 py-3 mb-3 2xl:text-xl"
                        type="text" name="username" required placeholder="{{__('common.username')}}">

            @if($errors->any())
                <p class="mb-2 text-red-500 font-bold">{{ $errors->first() }}</p>
            @endif
            </label>
            <label class="w-full">
                <input
                        class="outline-none w-full bg-white px-2 py-3 mb-3 2xl:text-xl"
                        type="password" name="password" required
                        placeholder="{{__('common.password')}}">
            </label>
        </div>

        <button type="submit"
                class="2xl:text-xl outline-none mx-auto uppercase font-light text-white px-16 py-3 bg-green rounded-full translation-all duration-300 hover:bg-opacity-30 hover:text-green">
            {{__('common.login')}}
        </button>
    </form>
    <div class="w-full px-10 sm:px-0 sm:w-2/5 xl:w-2/4 flex justify-center sm:justify-start mt-7 items-center space-x-3">
        <img class="w-12" src="{{asset('assets/img/client/green_logo.svg')}}" alt="green logo">
        <p class="text-white uppercase font-light">
            {{__('common.green_text')}}
        </p>
    </div>
@endsection
