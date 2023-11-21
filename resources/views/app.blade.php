<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        @yield('title')
    </title>
    <link rel="shortcut icon" href="{{ asset('img/favicon.png') }}" type="image/x-icon">

    {{-- CSS  --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@3.6.12/dist/css/splide.min.css"
        integrity="sha256-sB1O2oXn5yaSW1T/92q2mGU86IDhZ0j1Ya8eSv+6QfM=" crossorigin="anonymous">
    <link rel="stylesheet" href="//cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/fixedcolumns/4.1.0/css/fixedColumns.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{asset('css/custom.css')}}">
    @yield('css')


    {{-- JS  --}}
    {{-- <script src="{{ asset('library/dashboard/vendors/js/vendor.bundle.base.js') }}"></script> --}}
    {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> --}}
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@3.6.12/dist/js/splide.min.js"
        integrity="sha256-b/fLMBwSqO9vy/phDPv6OufPpR+VfUL+OsTEkJMPg+Q=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
    <script src="//cdn.datatables.net/fixedcolumns/4.1.0/js/dataTables.fixedColumns.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script> --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script> --}}
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    {{-- <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script> --}}
    <script src="https://cdn.tiny.cloud/1/h7t62ozvqkx2ifkeh051fsy3k9irz7axx1g2zitzpbaqfo8m/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.0.3/index.global.min.js"
        integrity="sha256-3ytVDiLNNR0KlhglNHqXDFL94uOszVxoQeU7AZEALYo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"
        integrity="sha512-BNaRQnYJYiPSqHHDb58B0yaPfCu+Wgds8Gp/gU33kqBtgNS4tSPHuGibyoeqMV/TJlSKda6FXzoEyYGjTe+vXA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('js/generate-number.js') }}"></script>
    <script src="{{ asset('js/currency.js') }}"></script>
    @stack('styles')
    <style>
        
    </style>
</head>

<body>
    {{-- <div id="overlay"></div> --}}
    @yield('body')

    {{-- Delete Item  --}}
    <div class="modal modal-sm fade" tabindex="-1" id="deleteItem" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="" method="post" id="formAction">
                    @csrf
                    @method('delete')
                    <div class="modal-body text-center">
                        <h2>
                            <i class="bi bi-info-circle text-info"></i>
                        </h2>
                        <h4>Are you sure?</h4>
                        <h6>You want to delete this data?</h6>
                        <hr>
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">
                            <i class="bi bi-x-square me-1"></i>
                            Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-trash3 me-1"></i>
                            Yes, Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Deactive User Item  --}}
    <div class="modal modal-sm fade" tabindex="-1" id="deactiveUser" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="" method="post" id="formActionDeactive">
                    @csrf
                    @method('delete')
                    <div class="modal-body text-center">
                        <h2>
                            <i class="bi bi-info-circle text-info"></i>
                        </h2>
                        <h4>Are you sure?</h4>
                        <h6>You want to deactive this user?</h6>
                        <hr>
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">
                            <i class="bi bi-x-square me-1"></i>
                            Cancel</button>
                        <button type="button" id="deactivate-user--app-3103" class="btn btn-primary btn-sm">
                            <i class="bi bi-trash3 me-1"></i>
                            Yes!</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Request Sign  --}}
    <div class="modal modal-sm fade" tabindex="-1" id="requestSign--modal" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="" method="post" id="formActionRequestSign">
                    @csrf
                    @method('delete')
                    <div class="modal-body text-center">
                        <h2>
                            <i class="bi bi-info-circle text-info"></i>
                        </h2>
                        <h4>Are you sure?</h4>
                        <h6><!-- warning text here --></h6>
                        <hr>
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">
                            <i class="bi bi-x-square me-1"></i>
                            Cancel</button>
                        <button type="button" id="send-request--app-2908" class="btn btn-primary btn-sm">
                            <i class="bi bi-trash3 me-1"></i>
                            Yes!</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Send Invoice / Receipt to Client  --}}
    <div class="modal modal-sm fade" tabindex="-1" id="sendToClient--modal" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="" method="post" id="formActionSendToClient">
                    @csrf
                    @method('delete')
                    <div class="modal-body text-center">
                        <h2>
                            <i class="bi bi-info-circle text-info"></i>
                        </h2>
                        <h4>Are you sure?</h4>
                        <h6><!-- warning text here --></h6>
                        <hr>
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">
                            <i class="bi bi-x-square me-1"></i>
                            Cancel</button>
                        <button type="button" id="send-to-client--app-0604" class="btn btn-primary btn-sm">
                            <i class="bi bi-trash3 me-1"></i>
                            Yes!</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Update Lead Status  --}}
    <div class="modal modal-sm fade" tabindex="-1" id="updateLeadStatus" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                {{-- <form action="" method="post" id="formAction"> --}}
                {{-- @csrf --}}
                {{-- @method('delete') --}}
                <div class="modal-body text-center">
                    <h2>
                        <i class="bi bi-info-circle text-info"></i>
                    </h2>
                    <h4>Are you sure?</h4>
                    <h6>You want to update this data?</h6>
                    <hr>
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">
                        <i class="bi bi-x-square me-1"></i>
                        Cancel</button>
                    <button type="button" id="btn-update-lead" class="btn btn-primary btn-sm">
                        <i class="bi bi-box-arrow-in-down me-1"></i>
                        Yes, Update</button>
                </div>
                {{-- </form> --}}
            </div>
        </div>
    </div>

    <script>
        window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    </script>

    {{-- Tooltip  --}}
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>

    {{-- Loading when Submiting  --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/general-use-script.js') }}"></script>

    {{-- Realtime for Datatables  --}}
    <script>
        function realtimeData(data) {
            setInterval(() => {
                
                data.ajax.reload(null, false)
            }, 7000);
        }


        // for redirect to login page after session expired
        $(document).ready(function() {
            $.fn.dataTable.ext.errMode = function ( settings, helpPage, message ) { 

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

        function confirmUpdateLeadStatus(link, clientId, initProg, leadStatus) {
            // show modal 
            var myModal = new bootstrap.Modal(document.getElementById('updateLeadStatus'))
            myModal.show()

            $('#btn-update-lead').on('click', function() {
                showLoading()
                var link = "{{ url('client/student') }}/" + clientId + "/lead_status/" + $(this).val();
                axios.post(link, {
                        clientId: clientId,
                        initProg: initProg,
                        leadStatus: leadStatus,
                    })
                    .then(function(response) {
                        myModal.hide()
                        swal.close();

                        notification('success', response.data.message)

                    })
                    .catch(function(error) {
                        myModal.hide()
                        // swal.close();
                        notification('error', error)
                    })
            });

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
        tinymce.init({
            selector: 'textarea',
            height: "250",
            menubar: false,
            // plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        });
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

    @stack('scripts')
</body>

</html>
