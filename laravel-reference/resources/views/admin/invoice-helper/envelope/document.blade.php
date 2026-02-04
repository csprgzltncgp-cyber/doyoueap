<!DOCTYPE html>
<html>
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
            font-size: 18px;
        }

        .column {
            position: relative;
            float: left;
            width: 32%;
            height: 140px;
        }

        .column table{
            position: absolute;
            bottom:0;
            left: 0;
        }

        .row:after {
            content: "";
            display: table;
            clear: both;
        }

    </style>
</head>
<body>
    <div class="row">
        <div class="column">
        </div>
        <div class="column">
        </div>
        <div class="column">
          <table>
            <tr>
                <td><b>{{$data['invoice_data']['name']}}</b></td>
            </tr>
            <tr>
                <td>{{$data['invoice_data']['city']}}</td>
            </tr>
            <tr>
                <td>{{$data['invoice_data']['street'] . ' ' . $data['invoice_data']['house_number']}}</td>
            </tr>
            <tr>
                <td>{{$data['invoice_data']['postal_code']}}</td>
            </tr>

            @if($data['billing_data']['show_contact_holder_name_on_post'])
                <tr>
                    <td><i>{{$data['billing_data']['contact_holder_name']}}</i></td>
                </tr>
            @endif
          </table>
        </div>
      </div>
</body>
<html>
