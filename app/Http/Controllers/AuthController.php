<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    function googleCallback(Request $request){
        Log::info('Google Response: ' . $request->getContent());
        return view('auth.googlecb');
    }
}
