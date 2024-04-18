@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.css">
<style>
    .iti {
        display: block !important;
    }
</style>
@endpush

<table class="table table-bordered table-hover nowrap align-middle w-100" id="programTable">
    <thead class="bg-secondary text-white">
        <tr>
            <th class="bg-info text-white">#</th>
            <th class="bg-info text-white">Client Name</th>
            <th>Program Name</th>
            <th>Invoice ID</th>
            <th>Payment Method</th>
            <th>Created At</th>
            <th>Due Date</th>
            <th>Total Price</th>
            <th class="bg-info text-white">Action</th>
        </tr>
    </thead>
    <tfoot class="bg-light text-white">
        <tr>
            <td colspan="7"></td>
        </tr>
    </tfoot>
</table>

<div class="modal fade" id="reminderModal" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <span>
                    Reminder
                </span>
                <i class="bi bi-pencil-square"></i>
            </div>
            <div class="modal-body w-100 text-start">
                {{-- <form action="" method="POST" id="reminderForm"> --}}
                    {{-- @method('put') --}}
                    <div class="form-phone">
                        <h5>Select a recipient</h5>
                        <div id="reminder-parent">
                            <div class="form-check form-check-inline ms-4">
                                <input type="radio" class="form-check-input" name="recipients" value="parent" checked id="prPhoneInput">
                                <label for="prPhoneInput">
                                    <div class="form-group">
                                        <label for="">Parent</label>
                                        <input type="text" name="pr_phone" class="form-control w-100" value="" id="prPhone">
                                        <input type="hidden" id="prPhoneR" name="pr_phone_r" value="">
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div class="form-check form-check-inline ms-4">
                            <input type="radio" class="form-check-input" name="recipients" value="children" id="chPhoneInput">
                            <label for="chPhoneInput">
                                <div class="form-group">
                                    <label for="">Child</label>
                                    <input type="text" name="ch_phone" class="form-control w-100" value="" id="chPhone">
                                    <input type="hidden" id="chPhoneR" name="ch_phone_r" value="">
                                </div>
                            </label>
                        </div>
                        {{-- <label for="">Phone Number Parent</label>
                        <input type="text" name="phone" id="phone" class="form-control w-100"> --}}
                        <input type="hidden" id="parent_id_checked">
                        <input type="hidden" name="client_id" id="client_id">
                        <input type="hidden" name="clientprog_id" id="clientprog_id">
                        <input type="hidden" name="parent_fullname" id="fullname">
                        <input type="hidden" name="program_name" id="program_name">
                        <input type="hidden" name="invoice_duedate" id="invoice_duedate">
                        <input type="hidden" name="total_payment" id="total_payment">
                        <input type="hidden" name="payment_method" id="payment_method">
                        <input type="hidden" name="parent_id" id="parent_id">
                        <input type="hidden" name="send_to">
                        <input type="hidden" name="target_phone">
                    </div>
                    {{-- <hr> --}}
                    <div class="d-flex justify-content-between">
                        <button type="button" href="#" class="btn btn-outline-danger btn-sm"
                        data-bs-dismiss="modal">
                            <i class="bi bi-x-square me-1"></i>
                            Cancel</button>
                        <button type="button" onclick="sendWhatsapp()" class="btn btn-primary btn-sm">
                            <i class="bi bi-save2 me-1"></i>
                            Send</button>
                    </div>
                {{-- </form> --}}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>
<script>
    $(document).on('click', '.prPhoneInput', function () {
        sendTo('parent', $(this).data('index'), $(this).data('parentid'));
    });

    $(document).on('click', '#chPhoneInput', function () {
        sendTo('child', 0, null);
    });
</script>
<script>
    function sendTo(recipient, index, parentId)
    {
        var prPhoneStatus = chPhoneStatus = false;
        switch (recipient) {
            case "parent":
                var phone = $("#prPhoneR_"+index).val();
                prPhoneStatus = true;
                $("#parent_id_checked").val(parentId);
                break;

            case "child":
                var phone = $("#chPhoneR").val();
                chPhoneStatus = true;
                break;
        }

        $('#prPhoneInput').prop('checked', prPhoneStatus);
        $('#chPhoneInput').prop('checked', chPhoneStatus);
        $("input[name=send_to]").val(recipient);
        $("input[name=target_phone]").val(phone);
    }

    function sendWhatsapp()
    {
        showLoading();

        // $('#reminderModal').modal('show'); 

        var clientprog_id = $('#clientprog_id').val();
        var parent_fullname = $('#fullname').val();
        var phone = $('input[name=target_phone]').val();
        var program_name = $('#program_name').val();
        var invoice_duedate = $('#invoice_duedate').val();
        var total_payment = $('#total_payment').val();
        var payment_method = $('#payment_method').val();
        var parent_id = $('#parent_id_checked').val();
        var client_id = $('#client_id').val();

        
        var link = '{{ url("/") }}/invoice/client-program/'+clientprog_id+'/remind/by/whatsapp';
        axios.post(link, {
                parent_fullname : parent_fullname,
                phone : phone,
                program_name : program_name,
                invoice_duedate : invoice_duedate,
                total_payment : total_payment,
                payment_method : payment_method,
                parent_id : parent_id,
                client_id : client_id,
                sendTo : $('input[name=send_to]').val(),
            })
            .then(function(response) {
                swal.close();
                $('#reminderModal').modal('hide'); 
                
                
                let obj = response.data;
                var link = obj.link;
                window.open(link)
            })
            .catch(function(error) {
                $('#reminderModal').modal('hide'); 
                swal.close();
                notification('error', error)
            })
    }

    function openModalReminder(params)
    {
        $('#reminderModal').modal('show'); 

        var clientprog_id = params[0];
        var parent_fullname = params[1];
        var parent_phone = params[2];
        var program_name = params[3];
        var invoice_duedate = params[4];
        var total_payment = params[5];
        var payment_method = params[6];
        var parent_id = params[7];
        var client_id = params[8];
        var child_phone = params[9];
        var parents = params[10];
        var count_parent = parents !== null ? parents.length : 0;

        $('input[name=send_to').val(count_parent > 0 ? 'parent' : 'child');
        $('input[name=target_phone]').val(count_parent > 0 ? parents[0].phone : child_phone);
        var html = '';

        $('#reminder-parent').html('');
        if(count_parent > 0){
            $('#parent_id_checked').val(parents[0].id);

            parents.forEach(function (item, index){
                html += '<div class="form-check form-check-inline ms-4">' +
                            '<input type="radio" class="form-check-input prPhoneInput" name="recipients" value="parent_'+index+'" '+(index === 0 ? 'checked="checked"' : null) +' id="prPhoneInput_'+ index+ '" data-index="'+ index +'" data-parentid="'+ item.id +'">' +
                            '<label for="prPhoneInput[]">'+
                                '<div class="form-group">' +
                                    '<label for="">(Parent) ' + item.first_name + ' ' + (item.last_name != null ? item.last_name : '') + '</label>' +
                                    '<input type="text" name="pr_phone[]" class="form-control w-100" value="'+ item.phone +'" id="prPhone_'+index+'">' +
                                    '<input type="hidden" id="prPhoneR_'+index+'" name="pr_phone_r[]" value="'+item.phone+'">' +
                                '</div>' +
                            '</label>' +
                        '</div>' ;
            })

            $('#reminder-parent').html(html)
        }

        var parent = number = [];
        const parentInput = [];
        var child = document.querySelector("#chPhone");

        const phoneInput1 = window.intlTelInput(child, {
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
            initialCountry: 'id',
            onlyCountries: ["id", "us", "gb", "sg", "au", "my"],
        });

        $("#chPhone").on('keyup', function(e) {
            var number1 = phoneInput1.getNumber();
            $("#chPhoneR").val(number1);
            $("input[name=target_phone]").val(number1);
        });

        for(var i=0; i<count_parent; i++){
            parent[i] = document.querySelector("#prPhone_"+ i);

            parentInput[i] = window.intlTelInput(parent[i], {
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
                initialCountry: 'id',
                onlyCountries: ["id", "us", "gb", "sg", "au", "my"],
            });

            $("#prPhone_"+i).on('keyup', function(e) {
                number[i] = parentInput[i].getNumber();
                $("#prPhoneR_"+i).val(number[i]);
                $("input[name=target_phone]").val(number[i]);
            });
        }

        // $('#phone').val(parent_phone == null ? child_phone : parent_phone)
        $('#fullname').val(parent_fullname)
        $('#program_name').val(program_name)
        $('#invoice_duedate').val(invoice_duedate)
        $('#total_payment').val(total_payment)
        $('#clientprog_id').val(clientprog_id)
        $('#payment_method').val(payment_method)
        $('#parent_id').val(parent_id)
        $('#client_id').val(client_id)

        $("input[name=pr_phone]").val(parent_phone);
        $("#prPhoneR").val(parent_phone)
        $("input[name=ch_phone]").val(child_phone);
        $("#chPhoneR").val(child_phone)


    }

    
    var widthView = $(window).width();
    $(document).ready(function() {
        // $('form :input').val('');
        var table = $('#programTable').DataTable({
            dom: 'Bfrtip',
            lengthMenu: [
                [10, 25, 50, 100, -1],
                ['10 rows', '25 rows', '50 rows', '100 rows', 'Show all']
            ],
            buttons: [
                'pageLength', {
                    extend: 'excel',
                    text: 'Export to Excel',
                }
            ],
            scrollX: true,
            fixedColumns: {
                left: (widthView < 768) ? 1 : 2,
                right: 1
            },
            processing: true,
            serverSide: true,
            ajax: '',
            columns: [{
                    data: 'clientprog_id',
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'fullname',
                },
                {
                    data: 'program_name',
                },
                {
                    data: 'inv_id',
                    name: 'tbl_inv.inv_id',
                    className:'text-center',
                },
                {
                    data: 'payment_method',
                    name: 'payment_method',
                    className:'text-center',
                    render: function(data, type, row) {
                        return data=="Full Payment" ? '<i class="bi bi-wallet me-2 text-info"></i>' + data : '<i class="bi bi-card-checklist me-2 text-warning"></i>' + data
                    }
                },
                {
                    data: 'show_created_at',
                    name: 'show_created_at',
                    className:'text-center',
                    render: function(data, type, row, meta) {
                        return moment(data).format('MMMM Do YYYY');
                    }
                },
                {
                    data: 'due_date',
                    name: 'due_date',
                    className:'text-center',
                    render: function(data, type, row, meta) {
                        return moment(data).format('MMMM Do YYYY');
                    }
                },
                {
                    data: 'total_price_idr',
                    name: 'total_price_idr',
                    className:'text-center',
                    render: function(data, type, row) {
                        return new Intl.NumberFormat("id-ID", {
                            style: "currency",
                            currency: "IDR",
                            minimumFractionDigits: 0
                        }).format(data);

                    }
                },
                {
                    data: 'clientprog_id',
                    className: 'text-center',
                    render: function(data, type, row) {
                        var difference = row.date_difference;
                        var difference = 2;

                        var link = "{{ url('invoice/client-program') }}/" + row.clientprog_id
                        var detail_btn = '<a href="' + link + '" class="btn btn-sm btn-outline-warning"><i class="bi bi-eye"></i></a>';

                        // if (difference > 0 && difference <= 7)
                        // {
                        //     let reminder_params = [
                        //         row.clientprog_id,
                        //         row.inv_id
                        //     ];

                        //     let params = JSON.stringify(reminder_params);
                            
                        //     var email_btn = <a href="#remind_parent" onclick=\'sendReminder('+params+')\' class="btn btn-sm btn-outline-warning mx-1"><i class="bi bi-send"></i></a>';
                        //     detail_btn += email_btn;
                        // }
                        
                        if ((difference > 0 && difference <= 3))
                        {
                            let whatsapp_params = [
                                row.clientprog_id,
                                row.parent_fullname,
                                row.parent_phone,
                                row.program_name,
                                row.due_date,
                                row.total_price_idr,
                                row.payment_method,
                                row.parent_id,
                                row.client_id,
                                row.child_phone,
                                (row.client !== null ? row.client.parents : null)
                            ];

                            var params = JSON.stringify(whatsapp_params);
                            params = params.replaceAll("\"",  "'");

                            // var whatsapp_btn = <a href="#remind_parent" onclick=\'openModalReminder('+params+')\' class="mx-1 btn btn-sm btn-outline-success"><i class="bi bi-whatsapp"></i></a>';
                            var whatsapp_btn = "<a href=\"#remind_parent\" onclick=\"openModalReminder("+params+")\" class=\"mx-1 btn btn-sm btn-outline-success\"><i class=\"bi bi-whatsapp\"></i></a>";
                            // var whatsapp_btn = <button data-bs-toggle="modal" data-bs-target="#reminderModal" class="mx-1 btn btn-sm btn-outline-success reminder"><i class="bi bi-whatsapp"></i></button>';

                            detail_btn += whatsapp_btn;
                        }
                        
                        return detail_btn;
                    }
                }
            ],
            createdRow: function (row, data, index) {
                var today_month = moment(data).format('MMMM')
                var today_year = moment(data).format('YYYY');
                if (today_month == moment(data.due_date).format('MMMM') && today_year == moment(data.due_date).format('YYYY'))
                    $('td', row).addClass('bg-primary text-light');
            }

        })

    });
</script>
<script>
    function sendReminder(params)
    {
        showLoading();

        var clientprog_id = params[0];
        var invoice_id = params[1];

        var link = '{{ url("/") }}/invoice/client-program/'+clientprog_id+'/remind/by/email';
        axios.post(link, {
                invoice_id : invoice_id
            })
            .then(function(response) {
                swal.close();
                notification('success', 'Reminder has been sent');
            })
            .catch(function (error) {
                swal.close();
                notification('error', error.response.data.message);
            });
    }
</script>
@endpush