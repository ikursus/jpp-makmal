<?php

namespace App\Http\Controllers;

class PublicController extends Controller
{
    public function __invoke()
    {
        return view('public.index');
    }
}
