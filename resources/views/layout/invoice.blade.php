<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice : @yield('invoice_id') - PDF</title>
    {{-- <link rel="icon" href="#" type="image/gif" sizes="16x16"> --}}
    <style>
        /* @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap'); */
        @import url('{{ public_path('library/dashboard/css/googleapisfont.css') }}');

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-size: 10px !important;
        }

        @page {
            margin-top: 220px !important;
            /* create space for header */
            margin-bottom: 25px !important;
            /* create space for footer */
        }

        header,
        footer {
            position: fixed;
            left: 0px;
            right: 0px;
        }

        header {
            height: auto;
            margin-top: -220px;
            /* top: 0; */
        }

        footer {
            /* height: auto; */
            margin-bottom: -25px !important;
            bottom: 0;
        }

        body {
            font-family: 'Poppins', sans-serif !important;
        }

        h4 {
            font-size: 25px !important;
            font-family: 'Archivo Black', sans-serif;
            letter-spacing: 10px !important;
        }

        p {
            margin: 0;
            line-height: 1.2;
        }

        table {
            border-collapse: collapse;
        }

        table tr td,
        th {
            padding: 4px;
            line-height: 1.5;
        }

        .table-detail th {
            background: #0000ff;
            color: #fff;
            border: 1px solid #0000ff;
        }

        .table-detail td,
        th {
            border: 1px solid #dedede;
        }

        li {
            margin-left: 10px !important
        }
    </style>
</head>

<body style="padding: 0; margin:0;">
    <header>
        <img loading="lazy"  src="{{ public_path('img/pdf/edu-all-header.webp') }}" width="auto" height="50px" style="margin-left: 50px; margin-top: 50px;">
        <h4
            style="line-height:1.6; letter-spacing:3px; font-weight:bold; text-align:center; color:#247df2; font-size:16px; margin-bottom:10px; ">
            INVOICE
        </h4>
        {{-- <img loading="lazy"  src="{{ public_path('img/pdf/edu-all-watermark.webp') }}" width="85%"
        style="position:absolute; left:8%; top:32.5%; z-index:-999;"> --}}
        <img loading="lazy"  src="{{ public_path('img/pdf/confidential.webp') }}" width="85%"
        style="position:absolute; left:8%; top:25%; z-index:-999;opacity: 0.04;">

    </header>

    <footer>
        <img loading="lazy"  src="{{ public_path('img/pdf/edu-all-footer.webp') }}" width="100%">
    </footer>
    
    <main>
        <div class="" style="padding:0 30px;">
            
            @yield('body')

        </div>
    </main>

</body>

</html>
