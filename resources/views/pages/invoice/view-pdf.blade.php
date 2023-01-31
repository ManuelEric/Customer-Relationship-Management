<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>View Invoice</title>
</head>

<body>
    <div id="pspdfkit" style="height: 98vh"></div>

    <script src="{{ asset('js/pspdf/pspdfkit.js') }}"></script>
    <script>
        PSPDFKit.load({
                licenseKey: '',
                container: "#pspdfkit",
                document: "{{ asset('document.pdf') }}", // Add the path to your document here.
            })
            .then(async function(instance) {
                const pagesAnnotations = await Promise.all(
                    Array.from({
                        length: instance.totalPageCount
                    }).map((_, pageIndex) =>
                        instance.getAnnotations(pageIndex)
                    )
                );
                const annotationIds = pagesAnnotations.flatMap(pageAnnotations =>
                    pageAnnotations.map(annotation => annotation.id).toArray()
                );
                await instance.delete(annotationIds[0])

                console.log(annotationIds[0])

                // Toolbar 
                const items = instance.toolbarItems;
                instance.setToolbarItems(items.filter((item) => item.type === "sidebar-thumbnails" ||
                    item.type === "zoom-in" ||
                    item.type === "zoom-out" ||
                    item.type === "zoom-mode" ||
                    item.type === "search" ||
                    item.type === "print" ||
                    item.type === "export-pdf"
                ));

                // End Toolbar 
            })
            .catch(function(error) {
                console.error(error.message);
            });
    </script>
</body>

</html>
