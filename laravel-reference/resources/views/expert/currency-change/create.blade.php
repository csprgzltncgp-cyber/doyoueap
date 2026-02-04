<html>
    <head>
        <link rel="stylesheet" href="/assets/css/master.css?v={{time()}}">
        <link rel="stylesheet" href="{{asset('assets/css/currency-change.css')}}?v={{time()}}">
        @livewireStyles

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.7.1/dist/sweetalert2.all.min.js"></script>
    </head>
    <body  style="word-spacing:normal;">
        @livewire('expert.currency-change.create')

        @livewireScripts
        <script>
            Livewire.on('errorEvent', function(error){
                Swal.fire({
                    title: error,
                    text: '',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
            });


            document.addEventListener('DOMContentLoaded', function(){
                Swal.fire(
                    '{{__('currency-change.notification-popup-2-title')}}',
                    '{{__('currency-change.notification-popup-2-body')}}',
                    'warning'
                )
            });
        </script>
    </body>
</html>
