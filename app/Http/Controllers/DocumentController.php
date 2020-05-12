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
        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"'
        ];
        return response()->file($documentpath, $headers);
    }

    public function getSignedDocument(Request $request, String $filename)
    {
        $isExist = Storage::disk($this->signedDocumentsDriver)->exists($filename);
        if(!$isExist){
           return abort(404);
        }
        $documentpath = Storage::disk($this->signedDocumentsDriver)->path($filename);
        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"'
        ];
        return response()->file($documentpath, $headers);
    }

    public function getSignedDocuments(Request $request)
    {
        $files = Storage::disk($this->signedDocumentsDriver)->files();
        $filesResponse = [];
        foreach ($files as $file) {
            array_push($filesResponse, [
                'name' => $file
            ]);
        }
        return response()->json($filesResponse, 200);;
    }
    
    public function getAllDocumentsName(Request $request)
    {
        $files = Storage::disk($this->documentsDriver)->files();
        $fileResponses = [];
        $id = 1;
        $count = count($files);
        foreach ($files as $file) {
            array_push($fileResponses, [
                'id' => $id,
                'title' => $file
            ]);
            $id++;
        }
        return response()->json([
            "data" => $fileResponses,
            "count" => $count
        ], 200);
    }

    public function saveDocument(Request $request)
    {
        $request->validate([
            'document' => 'required'
        ]);
        $document = $request->file('document');
        $documentName = $document->getClientOriginalName();
        $success = Storage::disk($this->documentsDriver)->put($documentName, file_get_contents($document));
        $response = [
            'document_name' => $documentName,
            'created_at' => Carbon::now()->toDateTimeString()
        ];
        return response()->json($response, 200);;
    }

    public function saveSignedDocument(Request $request)
    {
        $request->validate([
            'document' => 'required'
        ]);
        $document = $request->file('document');
        $documentName = $document->getClientOriginalName();
        $success = Storage::disk($this->signedDocumentsDriver)->put($documentName, file_get_contents($document));
        $response = json_encode([
            'document_name' => $documentName,
            'created_at' => Carbon::now()->toDateTimeString()
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
