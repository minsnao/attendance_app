<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttendanceListController extends Controller
{
    public function index() 
    {
        return view('admin.attendances_list');
    }

        public function edit() 
    {
        return view('admin.admin_attendances_edit');
    }
}
