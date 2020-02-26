<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CertificationController extends Controller
{
    private $certificateDriver = "certificates";
    private $rootCADriver = "rootCA";
    private $caKey;
    private $caPem;

    public function __construct()
    {
        $this->caKey = Storage::disk($this->rootCADriver)->get('myCA.key');
        $this->caPem = Storage::disk($this->rootCADriver)->get('myCA.pem');
    }

    public function signCsrRequest(Request $request){
        $request->validate([
            'csr' => 'required'
        ]);
        $csr = "";
        if(!is_null($request->file('csr'))){
            $csr = file_get_contents($request->file('csr'));
        } else {
            $csr = $request->input('csr');
        }
        // Test file
        // $csrFile = Storage::disk($this->certificateDriver)->get('dummy.csr');
        // $usercert = openssl_csr_sign($csrFile, $this->caPem, $caPrivateKey, 255);
        $caPrivateKey = openssl_get_privatekey($this->caKey, "Kargo123");
        $usercert = openssl_csr_sign($csr, $this->caPem, $caPrivateKey, 255);
        openssl_x509_export($usercert, $certout);
        $csrCommonName = openssl_csr_get_subject($csr)["CN"];
        $_isCreated = Storage::disk($this->certificateDriver)->put($csrCommonName . '.crt', $certout);
        $crtFilePath = Storage::disk($this->certificateDriver)->path($csrCommonName . '.crt');
        // $headers = [
        //     'Content-Disposition' => 'attachment; filename="certificate.crt"'
        // ];

        // return response($certout, 200, $headers);
        return response()->download($crtFilePath, $csrCommonName . '.crt' );
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
