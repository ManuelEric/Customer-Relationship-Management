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
                            @for ($i = 0; $i < 4; $i++)
                                <option value="department_{{ $i }}">Department {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>
            <div id="user_card" class="card shadow mb-3 d-none">
                <div class="card-header">Users</div>
                <div class="card-body overflow-auto" style="max-height: 300px">
                    <input type="text" name="" id="user_id">
                    <ul class="list-group list-group-flush">
                        @for ($i = 0; $i < 20; $i++)
                            <li class="list-group-item d-flex justify-content-between cursor-pointer"
                                onclick="checkUser('user_{{ $i }}')">
                                User {{ $i }}
                                <i class="bi bi-arrow-right"></i>
                            </li>
                        @endfor
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow mb-3" id="menus">
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
                            @for ($i = 0; $i < 20; $i++)
                                <tr>
                                    <td width="80%">
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="checkbox" name="menus" id="menus_{{ $i }}"
                                                value="menus_{{ $i }}"
                                                onchange="checkMenus('{{ $i }}')">
                                            <label for="menus_{{ $i }}" class="cursor-pointer">
                                                Menus {{ $i }}
                                            </label>
                                        </div>
                                    </td>
                                    <td width="10%" align="center">
                                        <input type="checkbox" name="copy" id="copy_{{ $i }}"
                                            value="copy_{{ $i }}" onchange="checkCopy('{{ $i }}')">
                                    </td>
                                    <td width="10%" align="center">
                                        <input type="checkbox" name="export" id="export_{{ $i }}"
                                            value="export_{{ $i }}"
                                            onchange="checkExport('{{ $i }}')">
                                    </td>
                                </tr>
                            @endfor
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function checkDepartment() {
            let department = $('#department').val()

            if (department != '') {
                $('#user_card').removeClass('d-none')
            } else {
                $('#user_card').addClass('d-none')
            }
            //   Axios Here 
        }

        function checkUser(user_id) {
            let department = $('#department').val()
            $('#user_id').val(user_id)
            alert(user_id)
            //   Axios Here 
        }

        function checkMenus(menus_id) {
            let department = $('#department').val()
            let user = $('#user_id').val()
            let menus = $('#menus_' + menus_id)

            let menu_data
            if (menus.is(':checked')) {
                menu_data = true
            } else {
                menu_data = false
            }

            alert(menu_data)
        }

        function checkCopy(menus_id) {
            let department = $('#department').val()
            let user = $('#user_id').val()
            let menus = $('#copy_' + menus_id)

            let menu_data
            if (menus.is(':checked')) {
                menu_data = true
            } else {
                menu_data = false
            }

            alert(menu_data)
        }

        function checkExport(menus_id) {
            let department = $('#department').val()
            let user = $('#user_id').val()
            let menus = $('#export_' + menus_id)
            
            let menu_data
            if (menus.is(':checked')) {
                menu_data = true
            } else {
                menu_data = false
            }

            alert(menu_data)
        }
    </script>
@endsection
