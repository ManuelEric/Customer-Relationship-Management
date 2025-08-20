<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    <script type="text/javascript">
        var redirectUrl = "{{ $redirectUrl }}";
        $(document).ready(function() {
            setTimeout("location.href = redirectUrl;", 5000)
        })
    </script>
</body>
</html>
