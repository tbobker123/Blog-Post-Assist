<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $data['tinymce'] = getenv("TINYMCE");
        return view('dashboard', $data);
    }
}


