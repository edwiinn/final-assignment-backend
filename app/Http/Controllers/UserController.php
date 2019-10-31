<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getRecentPublicKey(Request $request)
    {
        $publicKey = Session::get('publicKey');
        return var_dump($publicKey); 
    }

    public function savePublicKey(Request $request)
    {
        $request->validate([
            'publicKey' => 'required'
        ]);
        Session::put('publicKey', $request->publicKey);
        $response = json_encode([
            'status' => 'OK'
        ]);
        return $response;
    }
}
