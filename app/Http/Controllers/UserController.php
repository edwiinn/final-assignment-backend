<?php

namespace App\Http\Controllers;

use App\KeyPair;
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

    public function getKeyPair(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required'
        ]);
        $keyPair = KeyPair::where('user_id', $request->user_id)->first();
        if ($keyPair == null){
            $newKeyPair = $this->generateNewKeyPair();
            $keyPair = KeyPair::create([
                'user_id' => $request->user_id,
                'private_key' => $newKeyPair["privateKey"],
                'public_key' => $newKeyPair["publicKey"]
            ]);
        }
        return response()->json($keyPair);
    }

    private function generateNewKeyPair()
    {
        $config = array(
            "digest_alg" => "sha256",
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        );

        $res = openssl_pkey_new($config);
        openssl_pkey_export($res, $privkey);
        $pubKey = openssl_pkey_get_details($res);
        $pubKey = $pubKey["key"];
        return array(
            "privateKey" => $privkey,
            "publicKey" => $pubKey
        );
    }
}
