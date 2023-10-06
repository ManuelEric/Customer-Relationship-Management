@extends('layout.main')

@section('title', 'Menus - Bigdata Platform')

@section('content')

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-3">
                <div class="card-body">
                    <div class="mb-2">
                        <label for="">Department</label>
                        <select name="" id="department" class="select w-100" onchange="checkDepartment()">
                            <option value=""></option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->dept_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div id="user_card" class="card shadow mb-3 d-none">
                <div class="card-header">Users</div>
                <div class="card-body overflow-auto" style="max-height: 300px">
                    <input type="hidden" name="" id="user_id">
                    <ul class="list-group list-group-flush" id="list-users">
                        <!-- user list shows here -->
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow mb-3 d-none" id="menus">
                <div class="card-header">Menus</div>
                <div class="card-body">
                    <table class="table mb-2">
                        <tr>
                            <th width="80%">Menus Name</th>
                            <th width="10%" align="center">Copy</th>
                            <th width="10%" align="center">Export</th>
                        </tr>
                    </table>
                    <div class="overflow-auto" style="max-height: 60vh">
                        <table class="table">
                            @foreach ($menus as $key => $value)
                                <tr class="bg-secondary text-light">
                                    <td class="py-3"><b class="ms-3">{{ $key }}</b></td>
                                    <td align="center" class="mx-4"><input type="checkbox" name="copy_{{ $key }}" data-total-child="{{ $value->count() }}" onchange="checkAll('copy', '{{ $key }}')"></td>
                                    <td align="center" class="mx-4"><input type="checkbox" name="export_{{ $key }}" data-total-child="{{ $value->count() }}" onchange="checkAll('export', '{{ $key }}')"></td>
                                </tr>
                                @foreach ($value as $menu)
                                <tr>
                                    <td width="80%">
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="checkbox" name="menus" id="menus_{{ $menu['menu_id'] }}"
                                                value="{{ $menu['menu_id'] }}"
                                                onchange="checkMenus('menu', '{{ $menu['menu_id'] }}')">
                                            <label for="menus_{{ $menu['menu_id'] }}" class="cursor-pointer">
                                                {{ $menu['submenu_name'] }}
                                            </label>
                                        </div>
                                    </td>
                                    <td width="10%" align="center" class="mx-4">
                                        <input type="checkbox" class="my-1" name="copy" data-master="{{ $key }}" id="copy_{{ $menu['menu_id'] }}" onchange="checkMenus('copy', '{{ $menu['menu_id'] }}')">
                                    </td>
                                    <td width="10%" align="center">
                                        <input type="checkbox" class="my-1" name="export" data-master="{{ $key }}" id="export_{{ $menu['menu_id'] }}" onchange="checkMenus('export', '{{ $menu['menu_id'] }}')">
                                    </td>
                                </tr>
                                @endforeach
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function checkAll(policy, key)
        {
            showLoading();
            var elementor_checked = $("input[name='"+policy+"_"+key+"']").prop('checked');
            $("input[name="+policy+"][data-master='"+key+"']").prop('checked', elementor_checked).trigger('change');
        }
        
        function checkDepartment() {
            showLoading();
            let department = $('#department').val()

            // open menu card
            $("#menus").removeClass('d-none');
            
            //   Axios Here
            var link = "{{ url('/') }}/api/department/access/" + department 
            axios.get(link)
                .then(function (response) {
                    resetCheckboxes()
                    var obj = response.data
                    getActiveMenu(obj.data)
                    getActiveCopy(obj.copy)
                    getActiveExport(obj.export)

                }).catch(function (error) {
                    console.log(error)
                    notification('error', 'Something went wrong. Please try again.')
                })
            
            var link = '{{ url("/") }}/api/employee/department/' + department
            axios.get(link)
                .then(function (response) {
                    var obj = response.data;
                    $("#list-users").html(obj.html_cxt);
                    if (department != '') {
                        $('#user_card').removeClass('d-none')
                    } else {
                        $('#user_card').addClass('d-none')
                    }
                    swal.close();
                }).catch(function (error) {
                    
                    swal.close();
                    notification('error', error.message);
                })
        }

        function checkUser(user_id) {
            let department = $('#department').val()
            $('#user_id').val(user_id)

            //   Axios Here 
            var link = "{{ url('/') }}/api/department/access/" + department + "/" + user_id
            axios.get(link)
                .then(function (response) {

                    $("#" + user_id).addClass('active rounded')
                    resetCheckboxes()
                    var obj = response.data
                    getActiveMenu(obj.data)
                    getActiveCopy(obj.copy)
                    getActiveExport(obj.export)

                    var dept_access = response.data.dept_access
                    disabledDepartmentMenu(dept_access)

                    var dept_copy = response.data.dept_copy
                    disabledDepartmentCopy(dept_copy)

                    var dept_export = response.data.dept_export
                    disabledDepartmentExport(dept_export)

                }).catch(function (error) {
                    notification('error', error.message);

                })
        }

        function checkMenus(param, menus_id) {
            let department = $('#department').val()
            let user = $('#user_id').val()
            let menus = $('#menus_' + menus_id)
            
            let menu_data = menus.is(':checked')
            let copy_data = $("#copy_" + menus_id).is(':checked')
            let export_data = $("#export_" + menus_id).is(':checked')

            // if user not choosen
            // then post into department menus
            if (!user) {
                var link = '{{ url("/") }}/menus/manage/department/access'
            } 

            // if user is choosen
            // then post into user menus
            else {                
                var link = '{{ url("/") }}/menus/manage/user/access';
            }

            axios.post(link, {
                'department_id': department,
                'menu_id': menus.val(), // menu id value from checkbox
                'menu_data': menu_data,   
                'copy_data': copy_data,
                'export_data': export_data,
                'user': user,
                'param' : param,
            }).then(function (response) {

                resetCheckboxes('menus')
                let obj = response.data
                getActiveMenu(obj.data);
                swal.close();

                
            }).catch(function (error) {

                console.log(error)
                swal.close();
                notification('error', error.message)

            });
        }

        function getActiveMenu(object) 
        {
            for (var i = 0 ; i < object.length ; i++) {
                var id = "#menus_" + object[i]
                $(id).prop('checked', true)
            }

        }

        function getActiveCopy(object)
        {
            var array_master_name = [];
            for (var i = 0 ; i < object.length ; i++) {
                var id = "#copy_" + object[i]
                $(id).prop('checked', true)
                
                var master_name = $(id).data('master');
                if (array_master_name.indexOf(master_name) == -1)
                    array_master_name.push(master_name);
                
            }

            for (var i = 0; i < array_master_name.length ; i++) {
                var sum = 0;
                var master_name = array_master_name[i];
                
                var total_child = $("input[name=copy_"+master_name+"]").data('total-child');

                var total_actual_child = $("input[name=copy][data-master="+master_name+"]:checked").each(function () {
                    sum++;
                });

                if (sum == total_child)
                    $("input[name=copy_"+master_name+"]").prop('checked', true);
            }


        }

        function getActiveExport(object)
        {
            var array_master_name = [];
            for (var i = 0 ; i < object.length ; i++) {
                var id = "#export_" + object[i]
                $(id).prop('checked', true)

                var master_name = $(id).data('master');
                if (array_master_name.indexOf(master_name) == -1)
                    array_master_name.push(master_name);
            }

            for (var i = 0; i < array_master_name.length ; i++) {
                var sum = 0;
                var master_name = array_master_name[i];
                
                var total_child = $("input[name=export_"+master_name+"]").data('total-child');

                var total_actual_child = $("input[name=export][data-master="+master_name+"]:checked").each(function () {
                    sum++;
                });

                if (sum == total_child)
                    $("input[name=export_"+master_name+"]").prop('checked', true);
            }
        }

        function disabledDepartmentMenu(dept_access)
        {
            for (var i = 0; i < dept_access.length; i++) {
                $("#menus_" + dept_access[i]).prop('disabled', true);
            }
        }

        function disabledDepartmentCopy(dept_copy)
        {
            for (var i = 0; i < dept_copy.length; i++) {
                $("#copy_" + dept_copy[i]).prop('disabled', true);
            }
        }

        function disabledDepartmentExport(dept_export)
        {
            for (var i = 0; i < dept_export.length; i++) {
                $("#export_" + dept_export[i]).prop('disabled', true);
            }
        }
        
        function resetCheckboxes(param)
        {
            switch (param) {
                case "menus":
                    $("input[name=menus]").prop('checked', false);
                break;

                case "copy":
                    $("input[name=copy]").prop('checked', false);
                break;

                case "export":
                    $("input[name=export]").prop('checked', false);
                break;

                default:
                    $("input[name=menus]").prop('checked', false);
                    $("input[name=copy]").prop('checked', false);
                    $("input[name=export]").prop('checked', false);

            }
        }
    </script>
@endsection
