<?php

namespace App\Http\Controllers;

use App\Models\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'uploadable_type' => 'required|string',
            'uploadable_id' => 'required|integer',
        ]);

        $typeMap = [
            'saman' => \App\Models\SamanRecord::class,
            'service' => \App\Models\ServiceRecord::class,
            'fuel' => \App\Models\FuelRecord::class,
        ];

        $type = $typeMap[$request->input('uploadable_type')] ?? null;
        if (!$type) {
            return back()->with('error', 'Jenis rekod tidak sah.');
        }

        $model = $type::findOrFail($request->input('uploadable_id'));
        $file = $request->file('file');

        $folder = 'uploads/' . $request->input('uploadable_type') . '/' . $request->input('uploadable_id');
        $path = $file->store($folder, 'public');

        $model->files()->create([
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => auth()->id(),
        ]);

        return back()->with('success', 'Fail dimuat naik.');
    }

    public function destroy(FileUpload $file)
    {
        Storage::disk('public')->delete($file->file_path);
        $file->delete();
        return back()->with('success', 'Fail dipadam.');
    }
}
