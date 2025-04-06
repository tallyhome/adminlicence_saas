<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiDocumentationController extends Controller
{
    /**
     * Display the API documentation page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.api-documentation');
    }
}