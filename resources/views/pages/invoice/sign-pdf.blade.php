<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>View Invoice</title>
    <style>
        .btn-danger {
            background: red;
        }
    </style>
</head>

<body>
    <div id="pspdfkit" style="height: 98vh"></div>
    <script src="{{ asset('js/pspdf/pspdfkit.js') }}"></script>
    <script>
        async function savePDF(instance) {
            const arrayBuffer = await instance.exportPDF();
            const blob = new Blob([arrayBuffer], {
                type: 'application/pdf'
            });
            const formData = new FormData();
            formData.append("file", blob);
            await fetch("/upload", {
                method: "POST",
                body: formData
            });
        }


        PSPDFKit.load({
                container: "#pspdfkit",
                document: "{{ asset('document.pdf') }}", // Add the path to your document here.
                autoSaveMode: PSPDFKit.AutoSaveMode.DISABLED
            })
            .then(async function(instance) {

                instance.setToolbarItems((items) => {
                    items.push({
                        type: "custom",
                        id: "save-button",
                        title: "Save",
                        class: 'btn-danger',
                        onPress: () => {
                            savePDF(instance);
                        }
                    });
                    return items;
                });

            })
            .catch(function(error) {
                console.error(error.message);
            });
    </script>
</body>

</html>
