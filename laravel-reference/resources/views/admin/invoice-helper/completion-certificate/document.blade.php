<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style type="text/css">
        @font-face {
            font-family: 'Calibri';
            font-style: normal;
            font-weight: 400;
            src: url("file://{!! public_path('assets/fonts/Calibri.ttf') !!}") format('truetype');
        }

        @font-face {
            font-family: 'Calibri';
            font-style: normal;
            font-weight: 700;
            src: url("file://{!! public_path('assets/fonts/CALIBRIB.TTF') !!}") format('truetype');
        }

        @font-face {
            font-family: 'Calibri';
            font-style: italic;
            font-weight: 400;
            src: url("file://{!! public_path('assets/fonts/CALIBRII.TTF') !!}") format('truetype');
        }

        body {
            font-family: "Calibri" !important;
        }

        html{
            padding: 0;
            margin: 0;
        }

        .header{
            width: 100%;
            height: 140px;
            position: absolute;
            top: 0;
            background-color: #00575f;
        }

        .signature{
            position: relative;
        }

        .signature img{
            height: 50px;
            position: absolute;
            top: -55px;
            left: 140px;
            z-index: 2;
        }

        .signature .stamp{
            height: 70px;
            position: absolute;
            top: -55px;
            left: 0;
            z-index: 1;
        }

        .header img{
            height: 100%;
            width: 100%;
        }

        .alt{
            margin-top: 15px;
            margin-left: 50px;
            margin-right: 50px;
            font-weight: normal;
            font-size: 10px;
        }

        .container{
            width: 100%;
            height: 100vh;
        }

        .content{
            position: relative;
            top: 50%;
            transform: translateY(-50%);
        }

        .content h1{
            margin-top: 0 !important;
            padding-top: 0 !important;
            margin-bottom: 100px;
        }

        .attribute{
            margin-bottom: 10px;
            margin-left: 50px;
            margin-right: 50px;
        }


        .line{
            width: 40%;
            height: 1px;
            background-color: #000;
            margin-top: 10px;
            margin: auto;
        }

        .signature-holder {
            margin-top: 150px;
            padding:0;
            list-style-type: none;
            display: table;
            table-layout: fixed;
            width: 100%;
        }

        .signature {
            display: table-cell;
            text-align: center;
            vertical-align: middle;
        }

        .signature p{
            margin-top: 0;
            margin-bottom: 10px;
        }

        .signature div{
            margin-top: 0;
            margin-bottom: 10px;
        }

    </style>
</head>

<body>
    <div class="container">
        @if($with_header)
            <div class="header">
                <img src="{{asset('assets/img/invoice-helper/header.png')}}" alt="logo">
            </div>
        @endif

        <div class="content">
            <h1 style="text-align: center; font-weight:bold;">{{__('invoice-helper.completion-certificate.title', [], $language)}}</h1>

            <div class="attribute">
                <b><span>{{__('invoice-helper.completion-certificate.company-name', [], $language)}}:</span></b>
                <span class="normal">{{$direct_invoice->data['invoice_data']['name']}}</span>
            </div>

            <div class="attribute">
                <b><span>{{__('invoice-helper.completion-certificate.settlement', [], $language)}}:</span></b>
                <span class="normal">{{ucwords($direct_invoice->data['invoice_data']['city'])}}</span>
            </div>

            <div class="attribute">
                <b><span>{{__('invoice-helper.completion-certificate.date', [], $language)}}:</span></b>
                <span class="normal">{{Carbon\Carbon::now()->format('Y.m.d.')}}</span>
            </div>

            <div class="attribute">
                @php
                    $name = [];

                    foreach($direct_invoice->data['invoice_items'] as $invoice_item){
                        if(intval($invoice_item['input']) === App\Models\InvoiceItem::INPUT_TYPE_WORKSHOP && collect($direct_invoice->data['workshop_datas'])->sum('price') <= 0){
                            continue;
                        }

                        if(intval($invoice_item['input']) === App\Models\InvoiceItem::INPUT_TYPE_CRISIS && collect($direct_invoice->data['crisis_datas'])->sum('price') <= 0){
                            continue;
                        }

                        if (isset($direct_invoice->data['orientation_datas'])) {
                            if(intval($invoice_item['input']) === App\Models\InvoiceItem::INPUT_TYPE_OTHER_ACTIVITY && collect($direct_invoice->data['orientation_datas'])->sum('price') <= 0){
                                continue;
                            }
                        } else {
                            if(intval($invoice_item['input']) === App\Models\InvoiceItem::INPUT_TYPE_OTHER_ACTIVITY && collect($direct_invoice->data['other_activity_datas'])->sum('price') <= 0){
                                continue;
                            }
                        }

                        array_push($name, $invoice_item['name']);
                    }
                @endphp
                <b><span>{{__('invoice-helper.completion-certificate.subject-of-fulfillment', [], $language)}}:</span></b>
                <span class="normal">
                    {{implode(', ', $name)}}
                </span>
            </div>

            @php
                $totalPrice = 0;
            @endphp

            <div class="attribute">
                <b><span>{{__('invoice-helper.completion-certificate.completion-period', [], $language)}}:</span></b>
                <span class="normal">{{Carbon\Carbon::parse($direct_invoice->from)->format('Y.m.d.')}} - {{Carbon\Carbon::parse($direct_invoice->to)->format('Y.m.d.')}}</span>
            </div>

            @foreach($direct_invoice->data['invoice_items'] as $invoice_item)
                @if(intval($invoice_item['input']) === App\Models\InvoiceItem::INPUT_TYPE_WORKSHOP && collect($direct_invoice->data['workshop_datas'])->sum('price') <= 0)
                    @continue
                @endif

                @if(intval($invoice_item['input']) === App\Models\InvoiceItem::INPUT_TYPE_CRISIS && collect($direct_invoice->data['crisis_datas'])->sum('price') <= 0)
                    @continue
                @endif

                @if (isset($direct_invoice->data['orientation_datas']))
                    @if(intval($invoice_item['input']) === App\Models\InvoiceItem::INPUT_TYPE_OTHER_ACTIVITY && collect($direct_invoice->data['orientation_datas'])->sum('price') <= 0)
                        @continue
                    @endif
                @else
                    @if(intval($invoice_item['input']) === App\Models\InvoiceItem::INPUT_TYPE_OTHER_ACTIVITY && collect($direct_invoice->data['other_activity_datas'])->sum('price') <= 0)
                        @continue
                    @endif
                @endif

                @php
                    switch ($invoice_item['input']){
                        case App\Models\InvoiceItem::INPUT_TYPE_MULTIPLICATION:
                            if(!empty($invoice_item['volume'])){
                                $quantity = intval(str_replace(' ', '',$invoice_item['volume']['value']));
                            }else{
                                $quantity = 1;
                            }

                            $netUnitPrice = floatval(str_replace(' ', '', $invoice_item['amount']['value']));
                            $totalPrice += $quantity * $netUnitPrice;
                            break;
                        case App\Models\InvoiceItem::INPUT_TYPE_AMOUNT:
                            $quantity = 1;
                            $netUnitPrice = floatval(str_replace(' ', '', $invoice_item['amount']['value']));
                            $totalPrice += $quantity * $netUnitPrice;
                            break;
                        case App\Models\InvoiceItem::INPUT_TYPE_WORKSHOP:
                            $quantity = 1;
                            $netUnitPrice = collect($direct_invoice->data['workshop_datas'])->sum('price');
                            $totalPrice += $quantity * $netUnitPrice;
                            break;
                        case App\Models\InvoiceItem::INPUT_TYPE_CRISIS:
                            $quantity = 1;
                            $netUnitPrice = collect($direct_invoice->data['crisis_datas'])->sum('price');
                            $totalPrice += $quantity * $netUnitPrice;
                            break;
                        case App\Models\InvoiceItem::INPUT_TYPE_OTHER_ACTIVITY:

                            $quantity = 1;
                            if (isset($direct_invoice->data['orientation_datas'])) {
                                $netUnitPrice = collect($direct_invoice->data['orientation_datas'])->sum('price');
                            } else {
                                $netUnitPrice = collect($direct_invoice->data['other_activity_datas'])->sum('price');
                            }

                            $totalPrice += $quantity * $netUnitPrice;
                            break;
                    }
                @endphp

                <div class="attribute">
                    <b><span>{{ $invoice_item['name'] }} {{__('invoice-helper.completion-certificate.value-of-completion-excluding-vat', [], $language)}}:</span></b>
                    <span class="normal">
                        @if($quantity > 1)
                            {{Carbon\Carbon::parse($direct_invoice->from)->startOfMonth()->format('Y.m.d.')}}
                            {{Carbon\Carbon::parse($direct_invoice->from)->endOfMonth()->format('Y.m.d.')}} -
                            {{ $netUnitPrice }} {{strtoupper($direct_invoice->data['billing_data']['currency'])}}
                            /  {{__('invoice-helper.completion-certificate.employee', [], $language)}} / {{__('invoice-helper.completion-certificate.month', [], $language)}}
                            x {{ $quantity }} {{__('invoice-helper.completion-certificate.employee', [], $language)}} = {{strtoupper($direct_invoice->data['billing_data']['currency'])}} {{ $netUnitPrice * $quantity }}
                        @else
                            {{strtoupper($direct_invoice->data['billing_data']['currency'])}} {{ $netUnitPrice * $quantity }}
                        @endif
                    </span>
                </div>
            @endforeach


            <div class="attribute">
                <b><span>{{__('invoice-helper.completion-certificate.total-value-of-completion-excluding-vat', [], $language)}}:</span></b>
                <span class="normal">{{strtoupper($direct_invoice->data['billing_data']['currency'])}} {{ $totalPrice }}</span>
            </div>

            <p class="alt">
                {{__('invoice-helper.completion-certificate.alt-text', ['date' => Carbon\Carbon::parse($direct_invoice->from)->endOfMonth()->format('Y.m.d.')], $language)}}
            </p>

            <div class="signature-holder">
                <div class="signature">
                    <div class="line"></div>
                    <p class="name"><b>{{__('invoice-helper.completion-certificate.signature', [], $language)}}</b></p>
                    <p class="alt">({{__('invoice-helper.completion-certificate.buyer', [], $language)}})</p>
                </div>

                <div class="signature">
                    <div class="line"></div>

                    @if($with_header)
                        <img src="{{asset('assets/img/invoice-helper/signature.png')}}">
                        <img class="stamp" src="{{asset('assets/img/invoice-helper/stamp.png')}}">
                    @endif

                    <p class="name"><b>{{__('invoice-helper.completion-certificate.signature', [], $language)}}</b></p>
                    <p class="alt">({{__('invoice-helper.completion-certificate.agent', [], $language)}})</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
