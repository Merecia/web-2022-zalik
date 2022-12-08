<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use App\Models\File;

class UploadFileController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            "file" => "required|mimes:pdf|max:10000"
        ]);

        $fileModel = new File;

        $file = $request->file('file');

        if ($request->file()) {

            $fileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $filePath = $file->storeAs('uploads', $fileName, 'public');

            $fileModel->filename = $fileName;
            $fileModel->filepath = '/public/' . $filePath;
            $fileModel->save();

            Storage::putFileAs('/', $file, $fileName);

            return response()->json([
                'data' => [
                    'id' => File::all()->count() + 1,
                    'filename' => $fileName,
                    'file_size' => $fileSize
                ]
            ], 201);
        }
    }
}
