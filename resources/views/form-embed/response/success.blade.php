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
    <div class="min-h-screen flex items-center justify-center bg-white">
        <div class="max-w-screen-md w-full">
            <div class="md:mx-auto mx-4 md:p-4 px-4 py-5 text-center shadow rounded-lg">
                <div class="flex justify-center my-4">
                    <img src="{{ asset('img/submitted.png') }}" alt="Form ALL-in Event" class="w-[200px]">
                </div>
                <h2 class="md:text-3xl text-xl mb-4 font-bold">
                    Thank you! <br> Enjoy the Event!
                </h2>
                <h4 class="md:text-xl text-md">
                    Please kindly make your way to the registration desk.
                </h4>
            </div>
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
                }, 3000);
            });
        });
    </script>
</body>
</html>
