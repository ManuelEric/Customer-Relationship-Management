<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SubMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $seeds = [];
        $main_menus = DB::table('tbl_main_menus')->orderBy('order_no', 'asc')->get();
        foreach ($main_menus as $main_menu) {
            if ($main_menu->id != 9)
                continue;

            $no = 1;
            $sub_menu = $this->getSubMenu($main_menu->mainmenu_name);


            foreach ($sub_menu['submenus'] as $key => $value) {
                if (!DB::table('tbl_menus')->where('mainmenu_id', $main_menu->id)->where('submenu_name', $value)->first()) {

                    $seeds[] = [
                        'mainmenu_id' => $main_menu->id,
                        'submenu_name' => $value,
                        'submenu_link' => $sub_menu['sublink'][$key],
                        'order_no' => $no,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                }
                $no++;
            }
        }

        if (count($seeds) > 0)
            DB::table('tbl_menus')->insert($seeds);
    }

    private function getSubMenu(string $type)
    {
        switch ($type) {

            case "Master":
                return [
                    'submenus' => ['Assets', 'Curriculum', 'Position', 'Lead Source', 'Major', 'Program', 'Event', 'External Edufair', 'Purchase Request', 'Vendors', 'University Tag Score', 'Sales Target'],
                    'sublink' => ['master/asset', 'master/curriculum', 'master/position', 'master/lead', 'master/major', 'master/program', 'master/event', 'master/edufair', 'master/purchase', 'master/vendor', 'master/university-tags', 'master/sales-target'],
                ];
                break;

            case "Client":
                return [
                    'submenus' => ['Students', 'Alumnis', 'Parents', 'Teacher/Counselor', 'Alumni Acceptance', 'Hot Leads'],
                    'sublink' => ['client/student?st=potential', 'client/alumni?st=mentee', 'client/parent', 'client/teacher-counselor', 'client/acceptance', 'client/hot-leads?program=Admissions+Mentoring'],
                ];
                break;

            case "Instance":
                return [
                    'submenus' => ['Partner', 'School', 'Universities'],
                    'sublink' => ['instance/corporate', 'instance/school', 'instance/university'],
                ];
                break;

            case "Program":
                return [
                    'submenus' => ['Referral', 'Client Event', 'Client Program', 'Partner Program', 'School Program'],
                    'sublink' => ['program/referral', 'program/event', 'program/client', 'program/corporate', 'program/school'],
                ];
                break;

            case "Invoice":
                return [
                    'submenus' => ['Client Program', 'Partner Program', 'School Program', 'Referral', 'Refund'],
                    'sublink' => ['invoice/client-program?s=needed', 'invoice/corporate-program/status/needed', 'invoice/school-program/status/needed', 'invoice/referral/status/needed', 'invoice/refund/status/needed'],
                ];
                break;

            case "Receipt":
                return [
                    'submenus' => ['Client Program', 'Partner Program', 'School Program', 'Referral Program'],
                    'sublink' => ['receipt/client-program', 'receipt/corporate-program', 'receipt/school-program', 'receipt/referral'],
                ];
                break;

            case "Users":
                return [
                    'submenus' => ['Employee', 'Volunteer'],
                    'sublink' => ['user/employee', 'user/volunteer'],
                ];
                break;

            case "Report":
                return [
                    'submenus' => ['Sales Tracking', 'Event Tracking', 'Partnership', 'Invoice & Receipt', 'Unpaid Payment'],
                    'sublink' => ['report/sales', 'report/event', 'report/partnership', 'report/invoice', 'report/unpaid'],
                ];

            case "Recycle":
                return [
                    'submenus' => ['Students', 'Parents', 'Teacher/Counselor', 'School'],
                    'sublink' => ['recycle/client/students', 'recycle/client/parents', 'recycle/client/teacher-counselor', 'recycle/instance/school'],
                ];
                break;
        }
    }
}
