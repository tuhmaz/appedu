<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        // Check if the request contains a file
        if ($request->hasFile('file')) {
            // Get the uploaded file
            $file = $request->file('file');

            // Generate a random filename with the original extension
            $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();

            // Store the file in the 'public/images' directory and get the storage path
            $path = $file->storeAs('public/images', $filename);

            // Return the URL to the uploaded image
            return response()->json(['url' => Storage::url($path)]);
        }

        // Return an error if no file is found in the request
        return response()->json(['error' => 'No file uploaded.'], 400);
    }
}
