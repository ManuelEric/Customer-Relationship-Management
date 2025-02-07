<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        @yield('title')
    </title>
    <link rel="shortcut icon" href="{{ asset('img/favicon.webp') }}" type="image/x-icon">

    {{-- CSS  --}}
    <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/splide.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dataTables/dataTables.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dataTables/fixedColumns.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dataTables/buttons.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    @yield('css')
    <style>
        /* This selector targets the editable element (excluding comments). */
        .ck-editor__editable_inline:not(.ck-comment__input *) {
            min-height: 200px;
            overflow-y: auto;
        }
    </style>


    {{-- JS  --}}
    {{-- <script src="https://code.jquery.com/jquery-3.5.1.js"></script> --}}
    {{-- <script src="//fastly.jsdelivr.net/npm/sweetalert2@11"></script> --}}
    {{-- <script src="https://fastly.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script> --}}
    {{-- <script src="https://fastly.jsdelivr.net/npm/@splidejs/splide@3.6.12/dist/js/splide.min.js"
        integrity="sha256-b/fLMBwSqO9vy/phDPv6OufPpR+VfUL+OsTEkJMPg+Q=" crossorigin="anonymous"></script> --}}
    {{-- <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script> --}}
    {{-- <script src="//cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script> --}}
    {{-- <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script> --}}
    {{-- <script src="//cdn.datatables.net/fixedcolumns/4.1.0/js/dataTables.fixedColumns.min.js"></script> --}}
    {{-- <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script> --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script> --}}
    {{-- <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script> --}}
    {{-- <script src="https://fastly.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
    {{-- <script src="https://fastly.jsdelivr.net/npm/axios/dist/axios.min.js"></script> --}}
    {{-- <script src="https://fastly.jsdelivr.net/npm/chart.js"></script> --}}
    {{-- <script src="https://fastly.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script> --}}
    {{-- <script src="https://fastly.jsdelivr.net/npm/fullcalendar@6.0.3/index.global.min.js"
        integrity="sha256-3ytVDiLNNR0KlhglNHqXDFL94uOszVxoQeU7AZEALYo=" crossorigin="anonymous"></script> --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"
        integrity="sha512-BNaRQnYJYiPSqHHDb58B0yaPfCu+Wgds8Gp/gU33kqBtgNS4tSPHuGibyoeqMV/TJlSKda6FXzoEyYGjTe+vXA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script> --}}
    {{-- <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script> --}}
    {{-- <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script> --}}
    
    <script src="{{ asset('js/jquery/jquery.js') }}"></script>
    <script src="{{ asset('js/sweetalert2/sweetalert2.js') }}"></script>
    <script src="{{ asset('js/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/splide.min.js') }}"></script>
    <script src="{{ asset('js/dataTables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/dataTables/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/moment.min.js') }}"></script>
    <script src="{{ asset('js/dataTables/dataTables.fixedColumns.min.js') }}"></script>
    <script src="{{ asset('js/dataTables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('js/jszip.min.js') }}"></script>
    <script src="{{ asset('js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script src="{{ asset('js/chart.js') }}"></script>
    <script src="{{ asset('js/chartjs-plugin-datalabels.js') }}"></script>
    <script src="{{ asset('js/fullcalendar.min.js') }}"></script>
    <script src="{{ asset('js/index.global.min.js') }}"></script>
    <script src="{{ asset('js/html2canvas.min.js') }}"></script>
    <script src="{{ asset('js/pusher.min.js') }}"></script>
    <script src="{{ asset('js/ckeditor.js') }}"></script>
    
 
    <script src="{{ asset('js/generate-number.js') }}"></script>
    <script src="{{ asset('js/currency.js') }}"></script>

    {{-- Laravel Reverb --}}
    @vite(['resources/js/app.js', 'resources/css/app.css'])

    @stack('styles')
</head>

<body>
    {{-- <div id="overlay"></div> --}}
    {{-- @env('local')
        <x-main.loadspeedindicator />
    @endenv --}}

    @yield('body')

    <x-main.modal />

    <script>
        window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    </script>

    <script>
        var myEditor;

        document.querySelectorAll('textarea:not(#review)').forEach(function(element) {
            ClassicEditor
                .create(element, {
                    toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList',
                        'blockQuote'
                    ],
                    heading: {
                        options: [{
                                model: 'paragraph',
                                title: 'Paragraph',
                                class: 'ck-heading_paragraph'
                            },
                            {
                                model: 'heading1',
                                view: 'h1',
                                title: 'Heading 1',
                                class: 'ck-heading_heading1'
                            },
                            {
                                model: 'heading2',
                                view: 'h2',
                                title: 'Heading 2',
                                class: 'ck-heading_heading2'
                            }
                        ]
                    }
                })
                .then(editor => {
                    console.log('Editor was initialized', editor);
                    myEditor = editor;
                })
                .catch(error => {
                    console.error(error);
                });
        })
    </script>

    {{-- Tooltip  --}}
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>

    {{-- Loading when Submiting  --}}
    <script src="https://fastly.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/general-use-script.js') }}"></script>

    <script>

        function initializeDataTable(selector, options, tableName) {
            var table = $(selector).DataTable({
                ...options,
                dom: 'Bfrtip',
                lengthMenu: [
                    [10, 50, 100, -1],
                    ['10 row', '50 row', '100 row', 'Show all']
                ],
                scrollX: true,
                search: {
                    return: true
                },
                processing: true,
                serverSide: true,
            });

            // listen channel datatable for datatable
            var channel_datatable = Echo.channel('channel_datatable');
            channel_datatable.listen(".my-event", function(data) {
                if(data.message == tableName){
                    table.ajax.reload(null, false)
                }
            })

            return table;


        }

        // Realtime datatable
        function realtimeData(data) {
            setInterval(() => {

                data.ajax.reload(null, false)
            }, 7000);
        }


        // for redirect to login page after session expired
        $(document).ready(function() {

            var htmlLoading = '';

            htmlLoading += '<div>'
            htmlLoading += '<span class="spinner-border spinner-border-sm text-black" aria-hidden="true"></span>'
            htmlLoading += '<span class="ms-2 text-black" role="status">Importing...</span>'
            htmlLoading += '</div>'

            @php
                $authImport = Cache::has('auth') ? Cache::get('auth') : null;
                $isStart = Cache::has('isStartImport') ? Cache::get('isStartImport') : null;
            @endphp

            @if (
                $authImport != null &&
                    $isStart != null &&
                    Auth::user() != null &&
                    Auth::user()->id == $authImport['id'] &&
                    $isStart)
                $('#loading-import').html(htmlLoading);
            @endif

            $.fn.dataTable.ext.errMode = function(settings, helpPage, message) {

                if (settings && settings.jqXHR && settings.jqXHR.status == 401) {
                    notification('error', 'Your session has expired');
                    window.location.href = "{{ route('logout.expiration') }}";
                    return;
                }
            
                notification('error', 'Oops, Something went wrong when trying to get the data')
            };
        })
    </script>

    {{-- Confirm Delete & Deactivate Modal  --}}
    <script>
        function confirmRestore(subject, id) {
            // show modal 
            var myModal = new bootstrap.Modal(document.getElementById('restoreModal'))
            myModal.show()

            // change form action 
            $('#formRestore').attr('action', '{{ url('') }}/' + subject + '/' + id);
        }

        function confirmDelete(subject, id) {
            // show modal 
            var myModal = new bootstrap.Modal(document.getElementById('deleteItem'))
            myModal.show()

            // change form action 
            $('#formAction').attr('action', '{{ url('') }}/' + subject + '/' + id);
        }

        function confirmDeactivate(subject, id) {
            var myModal = new bootstrap.Modal(document.getElementById('deactiveUser'))
            myModal.show()

            // change form action 
            $('#formActionDeactive').attr('action', '{{ url('') }}/' + subject + '/' + id);
        }

        function confirmRequestSign(subject, currency) {
            var myModal = new bootstrap.Modal(document.getElementById('requestSign--modal'))
            myModal.show()

            var warningMessage = 'You want to request his/her signature for this document?';

            //     // change form action 
            $("#formActionRequestSign h6").html(warningMessage);

            var link = subject;
            $('#send-request--app-2908').unbind('click');
            $("#send-request--app-2908").bind('click', function() {
                requestAcc(link, currency)
            })
        }

        function confirmSendToClient(subject, id, category) {
            var myModal = new bootstrap.Modal(document.getElementById('sendToClient--modal'))
            myModal.show()

            var warningMessage = 'You want to send this ' + category + ' to client?';

            // change form action 
            $("#formActionSendToClient h6").html(warningMessage);

            var link = subject + '/' + id;
            $('#send-to-client--app-0604').unbind('click');
            $("#send-to-client--app-0604").bind('click', function() {
                if (typeof updateMail == "function")
                    updateMail()

                sendToClient(link)
            })
        }

        function confirmUpdateLeadStatus(link, clientId, initProg, groupId, leadStatusOld, leadStatus) {
            // show modal 
            var myModal = new bootstrap.Modal(document.getElementById('updateLeadStatus'))
            myModal.show()
            $('#statusLeadOld').val(leadStatusOld);
            $('#clientLeadId').val(clientId);

            $('#btn-update-lead').on('click', function() {
                showLoading()
                axios.post(link, {
                        clientId: clientId,
                        initProg: initProg,
                        leadStatus: leadStatus,
                        groupId: groupId,
                    })
                    .then(function(response) {
                        console.log(response);
                        myModal.hide()
                        swal.close();
                        notification('success', response.data.message)
                    })
                    .catch(function(error) {
                        myModal.hide()
                        swal.close();
                        notification('error', error)
                    })
            });
        }

        function closeModalLeadConfirm() {
            const id = $('#clientLeadId').val();
            const old_status = $('#statusLeadOld').val().toLowerCase();

            $('.leads' + id).val(old_status);
            $('#updateLeadStatus').modal('hide');
        }
        
        function singlequote(text) {
            return `'${text}'`;
        }
    </script>

    {{-- Notification by Session  --}}
    @if (session('success') || session('error'))
        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })

            Toast.fire({
                icon: '{{ session('success') ? 'success' : 'error' }}',
                title: '{{ session('success') ? session('success') : session('error') }}'
            })
        </script>
    @endif

    <!-- Notification by Jquery/Axios -->
    <script>
        function notification(status, message) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })

            // if (status == true) {
            //     var icon = "success"
            // } else {
            //     var icon = "error"
            // }

            Toast.fire({
                icon: status,
                title: message
            })
        }
    </script>

    {{-- TinyMCE  --}}
    
    <script>
        //     tinymce.init({
        //         strict_loading_mode : true,
        //         selector: 'textarea',
        //         height: "250",
        //         menubar: false,
        //         // plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
        //         toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        //     });
        // 
    </script>

    {{-- Select2  --}}
    <script>
        $(document).ready(function() {
            $('.select').select2({
                placeholder: "Select value",
                allowClear: true
            });
        });

        function initSelect2(element) {
            $(element + '.select').select2({
                placeholder: "Select value",
                allowClear: true
            });
        }
    </script>

    <script>
        const bearer_token = `Bearer {{ Session::get('access_token') }}`;
    </script>
    @stack('scripts')
</body>

</html>
