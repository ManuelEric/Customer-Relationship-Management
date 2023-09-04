<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanks</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.6/flowbite.min.css" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.6/flowbite.min.js"></script>
</head>
<body>
    <div class="min-h-screen flex items-center bg-gray-200">
        <div class="max-w-screen-md w-full mx-auto p-4 text-center">
            <h2 class="text-3xl mb-4 font-bold">
                Thank you! Enjoy the Event!
            </h2>
            <h4 class="text-xl">
                Your information has been successfully received.
            </h4>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"
    integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(function() {
            $(window).on('load', function() {
                setTimeout(() => {
                    parent.submitUpdate();
                }, 4000);
            });
        });
    </script>
</body>
</html> 