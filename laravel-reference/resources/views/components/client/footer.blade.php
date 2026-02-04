@props([
    'textColor' => 'white'
])

<div class="w-screen mt-auto text-{{$textColor}} pb-5">
    <div class="w-4/5 mx-auto flex flex-col-reverse sm:flex-row justify-between" style="max-width: 2060px">
        <p class="uppercase font-light text-sm pt-5 sm:pt-0">{{now()->format('Y')}} © cgp europe</p>
        <div class="flex flex-wrap ">
            <a href="https://chestnutce.com/{{app()->getLocale()}}/privacy-notice"
               target="_blank"
               class="uppercase font-light text-sm mr-1 sm:mr-2">Privacy notice</a>
            <span class="mr-1 sm:mr-2">|</span>
            <a href="https://chestnutce.com/{{app()->getLocale()}}/privacy-policy"
               target="_blank"
               class="uppercase font-light text-sm mr-1 sm:mr-2">Privacy Policy</a>
            <span class="mr-1 sm:mr-2">|</span>
            <a href="https://chestnutce.com/{{app()->getLocale()}}/data-breach-policy"
               target="_blank"
               class="uppercase font-light text-sm mr-1 sm:mr-2">Data Breach Policy</a>
        </div>
    </div>
</div>
