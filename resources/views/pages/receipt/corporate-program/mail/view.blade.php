<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>
</head>
<body>
    Please sign the document that has been attached to this email<br>
    <br>
    Click <a href="{{ route('receipt.corporate.sign_document', ['receipt' => $param['receipt_identifier'], 'currency' => $param['currency']]) }}?token={{ csrf_token() }}">here</a> to upload the signed document
</body>
</html>