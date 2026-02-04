@extends('layout.client.master', ['bg' => 'password'])

@section('content')
    <div class="p-24 bg-green-light bg-opacity-50 flex flex-col justify-center">
        <form method="post" class="flex">
            @csrf
            <label class="grow mr-5">
                <input
                        class="outline-none w-full bg-white px-2 py-3 2xl:text-xl focus:ring-0 focus:ring-offset-0 border-0"
                        type="password" name="password"
                        placeholder="{{__('common.password')}}">
            </label>
            <label class="grow mr-5">
                <input
                        class="outline-none w-full bg-white px-2 py-3 2xl:text-xl focus:ring-0 focus:ring-offset-0 border-0"
                        type="password" name="password_confirmation"
                        placeholder="{{__('common.password-again')}}">
            </label>
            <button type="submit"
                    class="grow-0 2xl:text-xl outline-none mx-auto uppercase font-light text-white px-16 py-3 bg-green rounded-full translation-all duration-300 hover:bg-opacity-30 hover:text-green">
               ok
            </button>
        </form>

        @if($errors->has('password'))
            <p class="mt-5 text-red-500 font-bold">{{ trans('common.force-change-password.validation') }}</p>
        @elseif($errors->has('password_mismatch'))
            <p class="mt-5 text-red-500 font-bold">{{$errors->first()}}</p>
        @elseif($errors->has('old_password'))
            <p class="mt-5 text-red-500 font-bold">{{$errors->first()}}</p>
        @endif

        @if(session()->has('success'))
            <p class="mt-5 text-green font-bold">{{session()->get('success')}}</p>
        @endif
    </div>
@endsection
