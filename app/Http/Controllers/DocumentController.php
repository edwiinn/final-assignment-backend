<?php

namespace App\Http\Controllers;

use App\Document;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;

class DocumentController extends Controller
{

    private $documentsDriver = 'documents';
    private $signedDocumentsDriver = 'signed-documents';

    public function getDocument(Request $request, String $documentId)
    {
        $document = Document::find($documentId);
        $filepath = $document->filepath;
        $filename = $document->name;
        $isExist = Storage::disk($this->documentsDriver)->exists($filepath);
        if(!$isExist){
           return abort(404);
        }
        $documentpath = Storage::disk($this->documentsDriver)->path($filepath);
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

    public function getSignedDocuments()
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
    
    public function getAllDocumentsName()
    {
        $documents = Document::get();
        $documentRespone = [];
        foreach ($documents as $document) {
            array_push($documentRespone, [
                'id' => $document->id,
                'title' => $document->name,
                'is_signed' => $document->signed
            ]);
        }
        return response()->json([
            "data" => $documentRespone,
        ], 200);
    }

    public function isStringInFile($file,$string){

        $handle = fopen($file, 'r');
        $valid = false; // init as false
        while (($buffer = fgets($handle)) !== false) {
            if (strpos($buffer, $string) !== false) {
                $valid = TRUE;
                break; // Once you find the string, you should break out the loop.
            }      
        }
        fclose($handle);

        return $valid;
    }

    public function saveDocument(Request $request)
    {
        try {
            $request->validate([
                'document' => 'required'
            ]);
            $document = $request->file('document');
            $documentName = $document->getClientOriginalName();
            $pathName = Uuid::uuid1()->toString() . "." . $document->getClientOriginalExtension(); 
            $success = Storage::disk($this->documentsDriver)->put($pathName, file_get_contents($document));
            $documentCreated = Document::create([
                'name' => $documentName,
                'filepath' => $pathName
            ]);
            return response()->json($documentCreated, 200);;
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function saveSignedDocument(Request $request)
    {
        $request->validate([
            'document' => 'required'
        ]);
        $document = $request->file('document');
        $documentName = $document->getClientOriginalName();
        $pathName = Uuid::uuid1()->toString() . "." . $document->getClientOriginalExtension(); 
        $success = Storage::disk($this->documentsDriver)->put($pathName, file_get_contents($document));
        $documentCreated = Document::create([
            'name' => $documentName,
            'filepath' => $pathName,
            'signed' => true
        ]);
        return response()->json($documentCreated, 200);;
    }

    public function verifyDocument(Request $request)
    {
        $request->validate([
            'document' => 'required'
        ]);
    }
}
