<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class FrontendController extends Controller
{
    public function frontend_index(): View
    {

        return view('v1.frontend.pages.welcome');
    }
}
