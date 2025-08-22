<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShowRequestsController extends Controller
{
    public function index() {
        return view('admin.request');
    }
    public function show() {
        return view('admin.requests_list');
    }
}
