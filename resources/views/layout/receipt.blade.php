<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Receipt : @yield('receipt_id') - PDF</title>
    {{-- <link rel="icon" href="#" type="image/gif" sizes="16x16"> --}}
    <style>
        /* @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap'); */
        @import url('{{ public_path("library/dashboard/css/googleapisfont.css") }}');

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-size: 12px !important;
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

        h3 {
            font-size: 26px !important;
            font-weight: 800;
            font-family: 'Archivo Black', sans-serif;
            letter-spacing: 5px !important;
            color: #9d9c9c;
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
            padding: 8px 7px;
            line-height: 16px;
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
    <header style="z-index:-999;">
        <img loading="lazy"  src="{{ public_path('img/pdf/edu-all-header.webp') }}" width="auto" height="50px" style="margin-left: 50px; margin-top: 50px;">
        <img loading="lazy"  src="{{ public_path('img/pdf/confidential.webp') }}" width="85%"
        style="position:absolute; left:8%; top:25%; opacity:0.04;">

    </header>

    <footer>
        <img loading="lazy"  src="{{ public_path('img/pdf/edu-all-footer.webp') }}" width="100%">
    </footer>
    
    <main>
        <div class="" style="padding:0 30px; margin-top: -75px; z-index:1">
            <h3 style="">
                PAYMENT RECEIPT
            </h3>
            
            @yield('body')

        </div>
    </main>

</body>

</html>
