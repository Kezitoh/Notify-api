<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends Controller
{
    //
    private $destinationPath = 'attachments_/';
    public function uploadFile(Request $request)
    {

        if (!$request->hasFile('file')) {
            return response()->json([
                'ok' => false,
                'message' => 'No se adjuntó ningún archivo'
            ]);
        }
        
        $file = $request->file('file');

        // $date = new DateTime();
        // $name = $date->format("YmdHis") . $request->file('file')->getClientOriginalName();
        $name = $file->getClientOriginalName();

        $res = $file->move($this->destinationPath, $name);
        return response()->json([
            'ok' => true,
            'res' => $res
        ]);
    }

    public function downloadFile(Request $request)
    {

        if (!$request->has('filename')) {
            return response()->json([
                'ok' => false,
                'message' => 'No se referencia ningún archivo.'
            ]);
        }

        $filename = $request->filename;
        $path = $this->destinationPath . $filename;

        $response = new BinaryFileResponse($path, 200, [], true, 'attachment');

        return $response;
        // response()->download($this->destinationPath.$filename, $filename);
    }
}
