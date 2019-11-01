<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{

    private $documentsDriver = 'documents';
    private $signedDocumentsDriver = 'signed-documents';

    public function getDocument(Request $request, String $filename)
    {

        $isExist = Storage::disk($this->documentsDriver)->exists($filename);
        if(!$isExist){
           return abort(404);
        }
        $documentpath = Storage::disk($this->documentsDriver)->path($filename);
        return response()->download($documentpath, $filename);
    }
    
    public function getAllDocumentsName(Request $request)
    {
        $files = Storage::disk($this->documentsDriver)->files();
        $response = json_encode([
            'documentsName' => $files 
        ]);
        return $response;
    }

    public function saveDocument(Request $request)
    {
        $request->validate([
            'document' => 'required'
        ]);
        $document = $request->file('document');
        $documentName = $document->getClientOriginalName();
        $success = Storage::disk($this->signedDocumentsDriver)->put($documentName, file_get_contents($document));
        $response = json_encode([
            'documentName' => $documentName,
            'createdAt' => Carbon::now()->toDateTimeString()
        ]);
        return $response;
    }

    public function verifyDocument(Request $request)
    {
        $request->validate([
            'document' => 'required'
        ]);
    }
}
