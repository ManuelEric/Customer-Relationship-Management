<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Page Title' }}</title>
        <link rel="shortcut icon" href="{{ asset('img/favicon.webp') }}" type="image/x-icon">
        <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
        <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/splide.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/dataTables/dataTables.bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/dataTables/fixedColumns.dataTables.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/dataTables/buttons.dataTables.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
        <link rel="stylesheet" href="{{ asset('library/dashboard/css/vertical-layout-light/style.css') }}">
        <link rel="stylesheet" href="{{ asset('css/step.css') }}">
        <style>
            @media (min-width: 768px) {
                .position-md-static {
                    position: static !important;
                }

                .position-md-relative {
                    position: relative !important;
                }

                .position-md-absolute {
                    position: absolute !important;
                }

                .position-md-fixed {
                    position: fixed !important;
                }

                .position-md-sticky {
                    position: sticky !important;
                }
            }

            /* This selector targets the editable element (excluding comments). */
            .ck-editor__editable_inline:not(.ck-comment__input *) {
                min-height: 200px;
                overflow-y: auto;
            }
        </style>
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
        {{-- <script src="{{ asset('js/ckeditor.js') }}"></script> --}}
        <script src="https://cdn.ckeditor.com/ckeditor5/12.3.1/classic/ckeditor.js"></script>


        <script src="{{ asset('js/generate-number.js') }}"></script>
        <script src="{{ asset('js/currency.js') }}"></script>
        <script src="{{ asset('library/dashboard/js/off-canvas.js') }}"></script>
        <script src="{{ asset('library/dashboard/js/hoverable-collapse.js') }}"></script>
        <script src="{{ asset('library/dashboard/js/template.js') }}"></script>
        <script src="{{ asset('library/dashboard/js/settings.js') }}"></script>
        <script src="{{ asset('library/dashboard/js/jquery.cookie.js') }}" type="text/javascript"></script>

        {{-- <script src="{{ asset('assets/dist/pspdfkit.js')}}"></script> --}}

        {{-- Sheet Js --}}
        <script lang="javascript" src="https://cdn.sheetjs.com/xlsx-0.19.2/package/dist/xlsx.full.min.js"></script>
        <livewire:styles />
    </head>
    <body>
        <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
                <div class="me-3">
                    <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
                        <span class="bi bi-list"></span>
                    </button>
                </div>
                <div>
                    <a class="navbar-brand brand-logo" href="
                        @if ($isSuperAdmin || $isSalesAdmin || $isSales)
                            {{ url('dashboard/sales/client-program') }}">
                        @elseif ($isPartnership)
                            {{ url('dashboard/partnership/agenda') }}">
                        @elseif ($isDigital)
                            {{ url('dashboard/digital') }}">
                        @elseif ($isFinance)
                            {{ url('dashboard/finance/outstanding-payment') }}">
                        @endif

                        <img loading="lazy"  src="{{ asset('img/logo.webp') }}" alt="logo" class="h-auto" />
                    </a>
                    <a class="navbar-brand brand-logo-mini" href="
                        @if ($isSuperAdmin || $isSalesAdmin || $isSales)
                            {{ url('dashboard/sales/client-program') }}">
                        @elseif ($isPartnership)
                            {{ url('dashboard/partnership/agenda') }}">
                        @elseif ($isDigital)
                            {{ url('dashboard/digital') }}">
                        @elseif ($isFinance)
                            {{ url('dashboard/finance/outstanding-payment') }}">
                        @endif

                        <img loading="lazy"  src="{{ asset('library/dashboard/images/logo-mini.svg') }}" alt="logo" />
                    </a>
                </div>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-top border-bottom">
                <ul class="navbar-nav">
                    <li class="nav-item font-weight-semibold d-none d-lg-block ms-0">
                        <h1 class="welcome-text">Welcome Back, <span
                                class="text-black fw-bold">{{ Auth::user()->full_name }}</span></h1>
                    </li>
                    @env('local')
                        @if(env('DB_HOST') != '127.0.0.1')
                            <li class="nav-item font-weight-semibold d-none d-lg-block ms-2">
                                <div class="alert alert-danger" role="alert">
                                    You're currently using a production database!
                                </div>
                            </li>
                        @endif
                    @endenv
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown d-none d-lg-block user-dropdown me-lg-3 me-0" id="loading-import">
                    </li>

                    <li class="nav-item dropdown d-none d-lg-block user-dropdown me-lg-3 me-0">
                        <a class="nav-link" href="#" role="button" data-bs-toggle="modal" data-bs-target="#follow_up"
                            aria-expanded="false" title="Follow-up Reminder">
                            <i class="bi bi-chat"></i>
                            @if (isset($followUp))
                                <span
                                    class="position-absolute ms-1 top-1 start-100 translate-middle badge rounded-pill bg-danger"
                                    style="font-size: 11px">
                                    <small>
                                        {{ count($followUp) }}
                                    </small>
                                </span>
                            @endif
                        </a>
                    </li>

                    <li class="nav-item dropdown d-none d-lg-block user-dropdown me-lg-3 me-0">
                        <a class="nav-link" href="#" role="button" data-bs-toggle="modal" data-bs-target="#birthday"
                            title="Mentee Birthday">
                            <i class="bi bi-gift"></i>
                            @if (isset($birthDay))
                                <span
                                    class="position-absolute ms-1 top-1 start-100 translate-middle badge rounded-pill bg-danger"
                                    style="font-size: 11px">
                                    <small>
                                        {{ count($birthDay) }}
                                    </small>
                                </span>
                            @endif
                        </a>
                    </li>

                    @if ($isSuperAdmin || $isSales || $isDigital)
                        <li class="nav-item dropdown d-none d-lg-block user-dropdown me-lg-3 me-0">
                            <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="bi bi-bell"></i>
                                @if (isset($countAlarm))
                                    @php
                                        if ($isSales) {
                                            $count = $countAlarm['sales'];
                                        } elseif ($isSuperAdmin) {
                                            $count = $countAlarm['general'];
                                        } elseif ($isDigital) {
                                            $count = $countAlarm['digital'];
                                        }
                                    @endphp
                                    @if ($count > 0)
                                        <span
                                            class="position-absolute ms-1 top-1 start-100 translate-middle badge rounded-pill bg-danger"
                                            style="font-size: 11px">
                                            <small>
                                                {{ $count }}
                                            </small>
                                        </span>
                                    @endif
                                @endif
                            </a>

                            @if (isset($notification))
                                <ul class="dropdown-menu dropdown-menu-right navbar-dropdown py-2 px-4"
                                    style="width: 400px;">
                                    @php
                                        if ($isSales) {
                                            $notification = $notification['sales'];
                                        } elseif ($isDigital) {
                                            $notification = $notification['digital'];
                                        } elseif ($isSuperAdmin) {
                                            $notification = $notification['general'];
                                        }
                                    @endphp
                                    @foreach ($notification as $notif)
                                        @if (isset($notif))
                                            <li class="d-flex align-items-center border-bottom py-2">
                                                <i class="bi bi-exclamation-circle me-2 text-warning"></i>
                                                <span class="lh-sm text-capitalize">
                                                    {!! $notif !!}
                                                </span>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endif


                    <li class="nav-item dropdown d-none d-lg-block user-dropdown">
                        <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <img loading="lazy"  class="img-xs rounded-circle" src="{{ asset('img/user.webp') }}" alt="Profile image"> </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                            <div class="dropdown-header text-center">
                                <img loading="lazy"  class="img-md rounded-circle w-25" src="{{ asset('img/user.webp') }}"
                                    alt="Profile image">
                                <p class="mb-1 mt-3 font-weight-semibold">{{ Auth::user()->first_name }}
                                    {{ Auth::user()->last_name }}</p>
                                <p class="fw-light text-muted mb-0">{{ Auth::user()->email }}</p>
                            </div>
                            <a class="dropdown-item text-center" href="{{ route('profile.index') }}">
                                <i class="bi bi-file-lock2 text-primary me-2"></i>
                                Change Password
                            </a>
                            <a class="dropdown-item text-center" href="{{ route('logout') }}">
                                <i class="bi bi-box-arrow-down-left text-primary me-2"></i>
                                Sign Out
                            </a>
                        </div>
                    </li>
                </ul>

                {{-- Mobile --}}
                <div class="dropdown d-block d-lg-none me-3">
                    @if ($isSuperAdmin || $isSales || $isDigital)
                        <a class="" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell" style="font-size: 2em;"></i>
                            @if (isset($countAlarm))
                                @php
                                    if ($isSales) {
                                        $count = $countAlarm['sales'];
                                    } elseif ($isSuperAdmin) {
                                        $count = $countAlarm['general'];
                                    } elseif ($isDigital) {
                                        $count = $countAlarm['digital'];
                                    }
                                @endphp
                                @if ($count > 0)
                                    <span
                                        class="position-absolute ms-1 top-1 start-100 translate-middle badge rounded-pill bg-danger"
                                        style="font-size: 11px">
                                        <small>
                                            {{ $count }}
                                        </small>
                                    </span>
                                @endif
                            @endif
                        </a>


                        @if (isset($notification))
                            <ul class="dropdown-menu py-2 px-4" style="width: 300px; left:-250px;">
                                @foreach ($notification as $notif)
                                    @if (isset($notif))
                                        <li class="d-flex align-items-center border-bottom py-2">
                                            <i class="bi bi-exclamation-circle me-2"></i>
                                            <span class="lh-sm">
                                                {!! $notif !!}
                                            </span>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif
                    @endif
                </div>
                <div class="dropdown d-block d-lg-none user-dropdown me-0">
                    <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <img loading="lazy"  class="img-xs rounded-circle" src="{{ asset('img/user.webp') }}" alt="Profile image"> </a>
                    <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown"
                        style="left:-150px;">
                        <div class="dropdown-header text-center">
                            <img loading="lazy"  class="img-md rounded-circle w-25" src="{{ asset('img/user.webp') }}"
                                alt="Profile image">
                            <p class="mb-1 mt-3 font-weight-semibold">{{ Auth::user()->first_name }}
                                {{ Auth::user()->last_name }}</p>
                            <p class="fw-light text-muted mb-0">{{ Auth::user()->email }}</p>
                        </div>
                        <a class="dropdown-item text-center" href="{{ route('profile.index') }}">
                            <i class="bi bi-file-lock2 text-primary me-2"></i>
                            Change Password
                        </a>
                        <a class="dropdown-item text-center" href="{{ route('logout') }}">
                            <i class="bi bi-box-arrow-down-left text-primary me-2"></i>
                            Sign Out
                        </a>
                    </div>
                </div>
                <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
                    data-bs-toggle="offcanvas">
                    <span class="bi bi-list"></span>
                </button>
                {{-- End Mobile  --}}
            </div>
        </nav>

        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:partials/_sidebar.html -->
            <x-apps.sidebar />

            <!-- partial -->
            <div class="main-panel position-md-absolute end-0 px-0">
                <div class="content-wrapper">
                    <div class="row">
                        <div class="col-sm-12 px-0">
                            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                                <ol class="breadcrumb bg-light border-0 rounded px-2 mb-1 justify-content-end">
                                    @yield('breadcrumb')
                                </ol>
                            </nav>
                            {{ $slot }}
                        </div>
                    </div>
                </div>
                <!-- content-wrapper ends -->
                <!-- partial:partials/_footer.html -->

                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">EduALL</span>
                        <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Copyright © 2023. All
                            rights
                            reserved.</span>
                    </div>
                </footer>
                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->

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
        <script>
            function otherOption(value) {
                if (value == 'other') {
                    $('.classReason').addClass('d-none')
                    $('#inputReason').removeClass('d-none')
                    $('#inputReason input').focus()
                } else {
                    $('#inputReason').addClass('d-none')
                    $('.classReason').removeClass('d-none')
                }
            }

            function resetOption() {
                $('.classReason').removeClass('d-none')
                $('#selectReason').val(null).trigger('change')
                $('#inputReason').addClass('d-none')
                $('#inputReason input').val(null)
            }

            $("#main_lead").on('change', function() {

                var program = $("#program_name option:selected")
                var lead = $(this).select2().find(":selected").data('lead')
                let programName = program.text()

                // if (programName) {
                    if (lead.includes('EduALL Event')) {

                        $("#event").removeClass('d-none')
                        $("#edufair").addClass("d-none")
                        $("#kol").addClass("d-none")
                        $("#partner").addClass("d-none")
                        $("#referral").addClass("d-none")

                    } else if (lead.includes('External Edufair')) {

                        $("#event").addClass("d-none")
                        $("#edufair").removeClass("d-none")
                        $("#kol").addClass("d-none")
                        $("#partner").addClass("d-none")
                        $("#referral").addClass("d-none")

                    } else if (lead.includes('KOL')) {

                        $("#event").addClass("d-none")
                        $("#edufair").addClass("d-none")
                        $("#kol").removeClass("d-none")
                        $("#partner").addClass("d-none")
                        $("#referral").addClass("d-none")

                    } else if (lead.includes('EduALL Partners')) {

                        $("#event").addClass("d-none")
                        $("#edufair").addClass("d-none")
                        $("#kol").addClass("d-none")
                        $("#partner").removeClass("d-none")
                        $("#referral").addClass("d-none")

                    } else if (lead.includes('Referral')) {

                        $("#event").addClass("d-none")
                        $("#edufair").addClass("d-none")
                        $("#kol").addClass("d-none")
                        $("#partner").addClass("d-none")
                        $("#referral").removeClass("d-none")


                    } else {

                        $("#event").addClass("d-none")
                        $("#edufair").addClass("d-none")
                        $("#kol").addClass("d-none")
                        $("#partner").addClass("d-none")
                        $("#referral").addClass("d-none")

                    }
                // } else {
                //     notification('warning', 'Please, select program name first!')
                //     $('#main_lead').select2('destroy');
                //     $('#main_lead').val(null);
                //     $('#main_lead').select2({
                //         placeholder: "Select value",
                //         allowClear: true
                //     });
                //     $('#program_name').select2('open');
                // }
            })


            function changeProgramStatus() {

                // prevent to trigger this function if options within select program name are null
                if ($("#program_name > option").length <= 1)
                    return

                var program = $("#program_name option:selected")

                let programName = program.text()
                let prog_mentor = program.data('pmentor')
                let programMainProg = program.data('mprog')
                let programSubProg = program.data('sprog')
                let programStatus = $('#program_status').val()
                $('.program-detail').addClass('d-none')
                $('.mentor-tutor').addClass('d-none')

                // if (programName) {
                try {
                    switch (parseInt(programStatus))
                    {
                        // program status = pending
                        case 0:
                            if (programMainProg.includes('Admissions Mentoring'))
                            {
                                // open form detail of admissions mentoring
                                $("#pending_mentoring").removeClass('d-none')
                            }
                            else if (programMainProg.includes('Test Preparation') || programMainProg.includes('Subject Tutoring') || programMainProg.includes('Competition') || programMainProg.includes('Skillset Tutoring'))
                            {
                                // open form detail of tutoring program
                                $("#pending_tutoring").removeClass('d-none')

                                resetDetailTutoring(programMainProg, 0)
                            }
                        break;

                        // program status = success
                        case 1:
                            $("#success_date").removeClass('d-none')
                            $("#running_status").removeClass('d-none')

                            if (programMainProg.includes('Admissions Mentoring'))
                            {
                                // open form detail of admissions mentoring
                                $("#success_mentoring").removeClass('d-none')
                            }
                            else if (programMainProg.includes('Test Preparation') || programMainProg.includes('Subject Tutoring') || programMainProg.includes('Competition') || programMainProg.includes('Skillset Tutoring'))
                            {
                                if (programSubProg.includes('SAT'))
                                {
                                    // open form detail of SAT
                                    $('#success_sat_act').removeClass('d-none')
                                }
                                else
                                {
                                    // default open form for test preparation exclude SAT/ACT, subject tutoring, competition, and skillset tutoring
                                    resetDetailTutoring(programMainProg, 1, programSubProg)
                                    $("#success_tutoring").removeClass('d-none')
                                }
                            }

                            // Mentor & Tutor Needs Check
                            switch (prog_mentor) {
                                case "Mentor":
                                    $("#available-mentor").removeClass("d-none")
                                    $("#available-tutor").addClass("d-none")

                                    break;

                                case "Tutor":
                                    $("#available-mentor").addClass("d-none")
                                    $("#available-tutor").removeClass("d-none")
                                    if (programMainProg.includes('Test Preparation'))
                                    {
                                        if (programSubProg.includes('SAT'))
                                        {
                                            $('#tutoring').addClass('d-none')
                                            $('#sat-act').removeClass('d-none')
                                        }
                                        else
                                        {
                                            $('#tutoring').removeClass('d-none')
                                            $('#sat-act').addClass('d-none')
                                        }
                                    }
                                    else if (programMainProg.includes('Tutoring') || programSubProg.includes('Tutoring') || programSubProg.includes('Competition')) {
                                        $('#tutoring').removeClass('d-none')
                                        $('#sat-act').addClass('d-none')
                                    } else if (programMainProg.includes('ACT') || programSubProg.includes('ACT') || programMainProg.includes('SAT') || programSubProg.includes('SAT')) {
                                        $('#tutoring').addClass('d-none')
                                        $('#sat-act').removeClass('d-none')
                                    } else if (programStatus == 4) { // hold
                                        $('#reason').removeClass('d-none')

                                    }
                                    break;
                            }
                        break;

                        // program status = failed
                        case 2:
                            $('#failed_date').removeClass('d-none')
                            $('#reason').removeClass('d-none')
                            $('#reason_notes').removeClass('d-none')
                        break;

                        // program status = refund
                        case 3:
                            $('#refund_date').removeClass('d-none')
                            $('#refund_notes').removeClass('d-none')
                            $('#reason').removeClass('d-none')
                            $('#reason_notes').removeClass('d-none')
                        break;
                    }

                } catch (error) {
                    notification('error', error.message)
                    console.error("Error: ", error.name, '-', error.message, '-', error.lineNumber)
                }
                // } else {
                //     notification('warning', 'Please, select program name first!')
                //     $('#program_status').select2('destroy').val(null).select2({
                //         placeholder: "Select value",
                //         allowClear: true
                //     }).select2('open');
                // }

            }

            function resetDetailTutoring(programMainProg, programStatus, programSubProg = '')
            {
                var stringField = programStatus == 0 ? 'pending' : 'success'
                if (programMainProg == "Test Preparation")
                {
                    if (programSubProg.includes("SAT"))
                    {
                        $(`.${stringField}-tutoring-test-preparation-field`).addClass('d-none')
                        $(`.${stringField}-tutoring-subject-tutoring-field`).addClass('d-none')
                        $(`.${stringField}-tutoring-competition-field`).addClass('d-none')
                        $(`.${stringField}-tutoring-skillset-tutoring-field`).addClass('d-none')
                        $(`.${stringField}-tutoring-sat-act-field`).removeClass('d-none')
                    }
                    else
                    {
                        $(`.${stringField}-tutoring-test-preparation-field`).removeClass('d-none')
                        $(`.${stringField}-tutoring-subject-tutoring-field`).addClass('d-none')
                        $(`.${stringField}-tutoring-competition-field`).addClass('d-none')
                        $(`.${stringField}-tutoring-skillset-tutoring-field`).addClass('d-none')
                        $(`.${stringField}-tutoring-sat-act-field`).addClass('d-none')
                    }
                }
                else if (programMainProg == "Subject Tutoring")
                {
                    $(`.${stringField}-tutoring-test-preparation-field`).addClass('d-none')
                    $(`.${stringField}-tutoring-subject-tutoring-field`).removeClass('d-none')
                    $(`.${stringField}-tutoring-competition-field`).addClass('d-none')
                    $(`.${stringField}-tutoring-skillset-tutoring-field`).addClass('d-none')
                    $(`.${stringField}-tutoring-sat-act-field`).addClass('d-none')
                }
                else if (programMainProg == "Competition")
                {
                    $(`.${stringField}-tutoring-test-preparation-field`).addClass('d-none')
                    $(`.${stringField}-tutoring-subject-tutoring-field`).addClass('d-none')
                    $(`.${stringField}-tutoring-competition-field`).removeClass('d-none')
                    $(`.${stringField}-tutoring-skillset-tutoring-field`).addClass('d-none')
                    $(`.${stringField}-tutoring-sat-act-field`).addClass('d-none')
                }
                else if (programMainProg == "Skillset Tutoring")
                {
                    $(`.${stringField}-tutoring-test-preparation-field`).addClass('d-none')
                    $(`.${stringField}-tutoring-subject-tutoring-field`).addClass('d-none')
                    $(`.${stringField}-tutoring-competition-field`).addClass('d-none')
                    $(`.${stringField}-tutoring-skillset-tutoring-field`).removeClass('d-none')
                    $(`.${stringField}-tutoring-sat-act-field`).addClass('d-none')
                }

            }

            function getSubProgram(main_prog_id) {
                showLoading()
                $("#program_name").html('<option data-placeholder="true"></option>').select2({
                    placeholder: 'Select value',
                    allowClear: true
                }).attr('disabled', true);
                @if (isset($clientProgram))
                    $("#program_status").val({{ $clientProgram->status }}).change() // trigger to change status into pending when changing main program
                @else
                    $("#program_status").val(0).change() // trigger to change status into pending when changing main program
                @endif
                var link = '{{ url('api/get/sub-program/main') }}/' + main_prog_id

                axios.get(link, {
                    headers: {
                        'crm-authorization': '{{env("CRM_AUTHORIZATION_KEY")}}'
                    }
                })
                .then(function (response) {
                    let obj = response.data;

                    // if main program doesn't have sub program
                    if (obj.length == 0)
                    {
                        $("#sub_program").html('<option data-placeholder="true"></option>').select2({
                            placeholder: 'Select value',
                            allowClear: true
                        }).attr('disabled', true);
                        getProgramName(main_prog_id, '')
                        return
                    }

                    let html = '<option data-placeholder="true"></option>';
                    $.each(obj, function (i, item) {
                        html += '<option value="' + item.id + '">' + item.sub_prog_name + '</option>';
                    });

                    $('#sub_program').html(html).select2({
                        placeholder: 'Select value',
                        allowClear: true
                    }).attr('disabled', false);
                    swal.close()

                    // count the html of program name
                    if ( $("#program_name > option").length > 1 ) {

                        $("#program_name").html('<option data-placeholder="true"></option>').select2({
                            placeholder: 'Select value',
                            allowClear: true
                        }).attr('disabled', true);
                        $("#program_name").val(null).trigger('change')
                    }

                    // trigger to change() sub program
                    @if (old('sub_program') !== null)
                        $("#sub_program").select2().val("{{ old('sub_program') }}").trigger('change');
                    @elseif ( isset($clientProgram))
                        $("#sub_program").select2().val("{{ $clientProgram->program->sub_prog_id }}").trigger('change');
                        @if (!isset($edit))
                            $("#sub_program").attr('disabled', true)
                        @endif
                    @endif
                })
                .catch(function (error) {
                    swal.close()
                    notification('error', error.message)
                })
            }

            function getProgramName(main_prog_id, sub_prog_id) {
                showLoading()
                var link = '{{ url('api/get/program/') }}/main/' + main_prog_id + '/sub/'
                if ( sub_prog_id )
                    link = link + sub_prog_id


                axios.get(link, {
                    headers: {
                        'crm-authorization': '{{env("CRM_AUTHORIZATION_KEY")}}'
                    }
                })
                .then(function (response) {
                    let obj = response.data;
                    let html = '<option data-placeholder="true"></option>';
                    $.each(obj, function (i, item) {
                        var prog_mentor = item.prog_mentor
                        var main_prog_name = item.main_prog.prog_name
                        var sub_prog_name = item.sub_prog?.sub_prog_name
                        var prog_id = item.prog_id
                        var prog_program = item.prog_program

                        html += '<option data-pmentor="'+ prog_mentor + '"' +
                                ' data-mprog="' + main_prog_name + '"' +
                                ' data-sprog="' + sub_prog_name + '"' +
                                ' value="' + prog_id + '">' + prog_program + '</option>';
                    });

                    @if (!isset($edit))

                        $('#program_name').html(html).select2({
                            placeholder: 'Select value',
                            allowClear: true
                        }).attr('disabled', true);
                    @else

                        $('#program_name').html(html).select2({
                            placeholder: 'Select value',
                            allowClear: true
                        }).attr('disabled', false);
                    @endif
                    swal.close()

                    // trigger to change() sub program
                    @if (old('prog_id') !== null)
                        var current_selected_main_program = $("#main_program").val()
                        var old_selected_main_program = "{{ old('main_prog') }}"
                        if (current_selected_main_program == old_selected_main_program)
                            $("#program_name").select2().val("{{ old('prog_id') }}").trigger('change');
                    @elseif (isset($clientProgram))
                        $("#program_name").select2().val("{{ $clientProgram->prog_id }}").trigger('change');
                    @endif

                })
                .catch(function (error) {
                    swal.close()
                    notification('error', error.message)
                })
            }

        </script>
        <script>
            $(document).ready(function() {


                // Check Program bought
                $('.check-package').on('click', function(){
                    showLoading()
                    var clientprog_id = $(this).data('clientprog-id');
                    var phase_lib_id = $(this).data('phase-lib-id') == '-' ? 'null' : $(this).data('phase-lib-id');
                    var phase_detail_id = $(this).data('phase-detail-id');
                    var link = null;

                    if(!$('#check-'+ phase_detail_id).is(":checked")){
                        $('#qty-' + phase_detail_id).addClass('uncheck')
                        link = '{{ url('api/program-phase') }}/' + clientprog_id + '/phase-detail/' + phase_detail_id + '/phase-lib/' + phase_lib_id

                        axios.delete(link, {
                            headers:{
                                'Authorization': 'Bearer ' + '{{ Session::get("access_token") }}',
                                'crm-authorization': '{{env("CRM_AUTHORIZATION_KEY")}}'
                            }
                        })
                        .then(function(response) {

                            let obj = response.data;
                            $('#quota-' + phase_detail_id).prop("disabled", true);
                            $('#quota-' + phase_detail_id).val(0);
                            Swal.close()
                            notification('success', "Successfully remove item program bought");
                        })
                        .catch(function(error) {
                            Swal.close()
                            notification('error', error)
                        })
                    }else{
                        $('#qty-' + phase_detail_id).removeClass('uncheck')
                        link = '{{ url('api/program-phase/') }}/' + clientprog_id + '/phase-detail/' + phase_detail_id + '/phase-lib/' + phase_lib_id

                        axios.post(link, null,
                            {
                                headers:{
                                    'Authorization': 'Bearer ' + '{{ Session::get("access_token") }}',
                                    'crm-authorization': '{{env("CRM_AUTHORIZATION_KEY")}}'
                                }
                            }
                        )
                        .then(function(response) {

                            let obj = response.data;

                            $('#quota-' + phase_detail_id).prop("disabled", false);
                            Swal.close()
                            notification('success', "Successfully add item program bought");
                        })
                        .catch(function(error) {
                            Swal.close()
                            notification('error', error)
                        })
                    }
                });

                // Counting program bought
                $('.quota-program-bought').on('change', function(){
                    showLoading()
                    var clientprog_id = $(this).data('clientprog-id');
                    var phase_lib_id = $(this).data('phase-lib-id') == '-' ? 'null' : $(this).data('phase-lib-id');
                    var phase_detail_id = $(this).data('phase-detail-id');
                    var quota = this.value;

                    link = '{{ url('api/program-phase') }}/' + clientprog_id + '/phase-detail/' + phase_detail_id + '/phase-lib/' + phase_lib_id + '/quota'

                    axios.patch(link, {quota: quota}, {
                        headers:{
                            'Authorization': 'Bearer ' + '{{ Session::get("access_token") }}',
                            'crm-authorization': '{{env("CRM_AUTHORIZATION_KEY")}}'
                        }
                    })
                    .then(function(response) {

                        let obj = response.data;
                        Swal.close()
                        notification('success', "Successfully update quota program bought");
                    })
                    .catch(function(error) {
                        Swal.close()
                        notification('error', error)
                    })
                });

                var baseUrl = "{{ url('/') }}/api/v1/get/referral/list";

                $(".select-referral").select2({
                    placeholder: 'Referral Name...',
                    // width: '350px',
                    allowClear: true,
                    ajax: {
                        url: baseUrl,
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                term: params.term || '',
                                page: params.page || 1
                            }
                        },
                        cache: true
                    }
                });

                @if (old('referral_code') !== NULL)
                    // Set the value, creating a new option if necessary
                    if ($('#referral_code').find("option[value= {{ old('referral_code') }} ]").length) {
                        $('#referral_code').val('{{ old("referral_code") }}').trigger('change');
                    } else {
                        // Create a DOM Option and pre-select by default
                    var newOption = new Option('{{ old("old_refname") }}', '{{ old("referral_code") }}', true, true);
                        // Append it to the select
                        $('#referral_code').append(newOption).trigger('change');
                    }
                @endif

                $('#referral_code').on('change', function(){
                    $('#old_refname').val($("option:selected", this).text())
                })

                $("input[name=session]").on('change', function() {
                    var start_date = $("input[name=prog_start_date]").val();
                    var end_date = $("input[name=prog_end_date]").val();

                    var start_date_local = start_date + "T00:00";
                    var end_date_local = end_date + "T23:59";

                    if (start_date == '' || end_date == '') {
                        notification('error',
                            'Please fill the start date and end date before fill the schedule session.');
                        $(this).val(null);
                        return;
                    }

                    var val = $(this).val();

                    if (val < 1) {
                        $(this).val(1)
                        val = 1;
                    }

                    var i = 1;
                    var html = '';

                    while (i <= val) {

                        html += '<div class="row mb-3 schedule-' + i + '">' +
                            '<div class="col-md-3">' +
                            '<label>Session ' + i + '.<sup class="text-danger">*</sup></label>' +
                            '</div>' +
                            '<div class="col-md-5">' +
                            '<small>Schedule</small>' +
                            '<input type="datetime-local" required class="form-control form-control-sm rounded" min="' +
                            start_date_local + '" max="' + end_date_local + '" name="sessionDetail[]">' +
                            '</div>' +
                            '<div class="col-md-4">' +
                            '<small>Zoom link</small>' +
                            '<input type="url" required class="form-control form-control-sm rounded" name="sessionLinkMeet[]">' +
                            '</div>' +
                            '</div>';

                        i++;
                    }

                    $("#section-session").html(html);
                })

                @if (isset($clientProgram) && $clientProgram->status !== false)
                    $("#program_status").val("{{ $clientProgram->status }}").trigger('change');
                @endif

                @if (old('status'))
                    $("#program_status").val("{{ old('status') }}").trigger('change');
                @endif

                @error('followup_date')
                    $("#plan").modal('show')
                @enderror

                const documentReady = () => {
                    @if (old('main_prog') !== null)
                        $("#main_program").select2().val("{{ old('main_prog') }}").trigger('change');
                    @elseif (isset($clientProgram))
                        $("#main_program").select2().val("{{ $clientProgram->program->main_prog_id }}").trigger('change');
                    @endif

                    @if (isset($p) && $p !== null)
                        $("#program_name").select2().val("{{ $p }}").trigger('change');
                    @elseif (isset($clientProgram->prog_id))
                        $("#program_name").select2().val("{{ $clientProgram->prog_id }}").trigger('change');
                    @elseif (old('prog_id') !== null)
                        $("#program_name").select2().val("{{ old('prog_id') }}").trigger('change');
                    @endif

                    @if (old('lead_id') !== null)
                        $("#main_lead").select2().val("{{ old('lead_id') }}").trigger('change');
                    @elseif (isset($clientProgram))
                        @if ($clientProgram->lead?->main_lead == 'KOL')
                            $("#main_lead").select2().val("kol").trigger('change');
                        @else
                            $("#main_lead").select2().val("{{ $clientProgram->lead_id }}").trigger('change');
                        @endif
                    @endif

                    @if (old('event_id') !== null)
                        $("#event_id").select2().val("{{ old('event_id') }}").trigger('change');
                    @endif

                    @if (old('kol_lead_id') !== null)
                        $("#kol_lead_id").select2().val("{{ old('kol_lead_id') }}").trigger('change');
                    @endif

                    @if (old('eduf_id') !== null)
                        $("#eduf_id").select2().val("{{ old('eduf_id') }}").trigger('change');
                    @endif

                    @if (old('partner_id') !== null)
                        $("#partner_id").select2().val("{{ old('partner_id') }}").trigger('change');
                    @endif

                    @if (old('referral_code') !== null)
                        $("#referral_code").select2().val("{{ old('referral_code') }}").trigger('change');
                    @endif

                    @if (old('status') !== null)
                        $("#program_status").select2().val("{{ (int) old('status') }}").trigger('change');
                    @endif

                }

                documentReady();
            })
        </script>
        <livewire:scripts />
    </body>
</html>
