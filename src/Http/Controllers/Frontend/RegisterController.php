<?php

namespace Juzaweb\Http\Controllers\Frontend;

use Juzaweb\Http\Controllers\FrontendController;

class RegisterController extends FrontendController
{
    public function index()
    {
        return view('theme::auth.register');
    }
}
