<?php

namespace Modules\Invoice\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PaymentController extends Controller
{
    public function index()
    {
        return view('invoice::index');
    }

    public function create()
    {
        return view('invoice::create');
    }

    public function show($id)
    {
        return view('invoice::show');
    }


    public function edit($id)
    {
        return view('invoice::edit');
    }

}
