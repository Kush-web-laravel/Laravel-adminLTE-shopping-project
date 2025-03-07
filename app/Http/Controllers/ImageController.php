<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\Image;

class ImageController extends Controller
{
    //
    public function index()
    {
        return view('resize-image');
    }

    public function storeImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,'
        ]);

        $filePath = null;

        $file = $request->file('image');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $path = public_path('uploads/images/' . $fileName);
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);
        $image->toJpeg(80)->save($path);
        $filePath = 'uploads/images/' . $fileName;

        Image::create([
            'image' => $filePath,
        ]);

        return redirect()->back()->with('success', 'Image uploaded successfully');
    }
}
