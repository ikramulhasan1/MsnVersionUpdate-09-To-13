<?php

namespace App\Http\Controllers\Admin;

use App\Models\Service;
use App\Models\Technology;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Toastr;
use Image;
use File;

class TechnologyController extends Controller
{
    public function __construct()
    {
        // Module Data
        $this->title = trans_choice('dashboard.service', 1);
        $this->route = 'admin.service';
        $this->view = 'admin.service';
        $this->path = 'service';
    }
    public function index()
    {
        $data['path'] = $this->path;
        $data['rows'] = Technology::with('service')->orderBy('id', 'asc')->get();
        return view('admin.technology.index',$data);
    }


    public function create()
    {
        $services = Service::orderBy('id', 'asc')->get();
        return view('admin.technology.create', compact('services'));
    }


    public function store(Request $request)
{
    // Field Validation
    $request->validate([
        'title' => 'required|max:191|unique:services,title',
        'short_title' => 'required|max:30|unique:services,short_title',
        'meta_title' => 'required|max:60',
        'keywords' => 'required',
        'price' => 'required',
        'starting_price' => 'required',
        'priceCurrency' => 'required',
        'average_rating' => 'required',
        'review_count' => 'required',
        'short_desc' => 'required',
        'description' => 'required',
        'image' => 'required|image',
        'logo' => 'nullable|image',
    ]);

    // Image upload, fit, and convert to WebP
    if($request->hasFile('image')) {
        // Upload New Image
        $filenameWithExt = $request->file('image')->getClientOriginalName();
        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME); 
        $fileNameToStore = $filename.'_'.time().'.webp';

        // Create Folder Location
        $path = public_path('uploads/'.$this->path.'/');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        // Resize, Crop, and Convert to WebP
        $thumbnailpath = $path.$fileNameToStore;
        Image::make($request->file('image')->getRealPath())
            ->fit(800, 500, function ($constraint) { 
                $constraint->upsize(); 
            })
            ->encode('webp', 90)  // Encode to WebP format with 90% quality
            ->save($thumbnailpath);
    } else {
        $fileNameToStore = 'noimage.jpg'; // Default image
    }

    // Upload Logo Image (optional) and save in the same location
    $logoFileNameToStore = null;
    if($request->hasFile('logo')) {
        $logoFile = $request->file('logo');
        $logoFilename = pathinfo($logoFile->getClientOriginalName(), PATHINFO_FILENAME); 
        $logoFileNameToStore = $logoFilename.'_'.time().'.webp';

        // Use the same folder as the image for the logo
        $logoPath = public_path('uploads/'.$this->path.'/');
        if (!File::exists($logoPath)) {
            File::makeDirectory($logoPath, 0777, true, true);
        }

        // Resize and convert the logo to WebP
        Image::make($logoFile->getRealPath())
            ->resize(200, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode('webp', 90)
            ->save($logoPath.$logoFileNameToStore);
    }

    // Get content with media file
    $content = $request->input('description');

    $dom = new \DomDocument();
    libxml_use_internal_errors(true);
    $dom->encoding = 'utf-8';
    $dom->loadHtml(utf8_decode($content), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);    
    $images = $dom->getElementsByTagName('img');

    foreach($images as $img) {
        $src = $img->getAttribute('src');
        
        if(preg_match('/data:image/', $src)) {                
            preg_match('/data:image\/(?<mime>.*?)\;/', $src, $groups);
            $mimetype = $groups['mime'];
            $filename = uniqid().'_'.time();

            $path = public_path('uploads/media/');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            $filepath = "/uploads/media/$filename.webp";    
            Image::make($src)
                ->resize(800, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 90)
                ->save(public_path($filepath));
            
            $new_src = asset($filepath);
            $img->removeAttribute('src');
            $img->setAttribute('src', $new_src);
        }
    }

    // Insert Data
    $service = new Technology;
    $service->title = $request->title;
    $service->service_id = $request->service_id;
    $service->keywords = $request->keywords;
    $service->price = $request->price;
    $service->starting_price = $request->starting_price;
    $service->priceCurrency = $request->priceCurrency;
    $service->average_rating = $request->average_rating;
    $service->review_count = $request->review_count;
    $service->short_title = $request->short_title;
    $service->meta_title = $request->meta_title;
    $service->slug = Str::slug(strtolower($request->slug), '-');
    $service->short_desc = $request->short_desc;
    $service->description = $dom->saveHTML();
    $service->image_path = $fileNameToStore;
    $service->logo_path = $logoFileNameToStore;
    $service->manu = $request->manu;
    $service->save();

    Toastr::success(__('dashboard.created_successfully'), __('dashboard.success'));
    return redirect()->route('admin.technologies.index');
}

    public function show(Technology $technology)
    {
        //
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = $this->path;

        $data['row'] = $technology;

        return view('admin.technology.show', $data);
    }

    
    public function edit(Technology $technology)
    {
        $data['path'] = $this->path;
        $data['subservice'] = $technology;
        $data['services'] = Service::orderBy('id', 'asc')->get();
        return view('admin.technology.edit',$data);
    }


    public function update(Request $request, Technology $technology)
{
    // Field Validation
    $request->validate([
        'title' => 'required|max:191|unique:subservices,title,'.$technology->id,
        'short_title' => 'required|max:30|unique:services,short_title,'.$technology->id,
        'meta_title' => 'required|max:60',
        'keywords' => 'required',
        'price' => 'required',
        'starting_price' => 'required',
        'priceCurrency' => 'required',
        'average_rating' => 'required',
        'review_count' => 'required',
        'short_desc' => 'required',
        'description' => 'required',
        'image' => 'nullable|image',
        'logo' => 'nullable|image', // New optional logo
    ]);

    // image upload, fit and store inside public folder 
    if($request->hasFile('image')){

        $file_path = public_path('uploads/'.$this->path.'/'.$technology->image_path);
        if(File::isFile($file_path)){
            File::delete($file_path);
        }

        // Upload New Image
        $filename = pathinfo($request->file('image')->getClientOriginalName(), PATHINFO_FILENAME); 
        $fileNameToStore = $filename.'_'.time().'.webp';

        // Create Folder Location
        $path = public_path('uploads/'.$this->path.'/');
        if (! File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        // Resize and convert to WebP (800x500)
        $thumbnailpath = $path.$fileNameToStore;
        Image::make($request->file('image')->getRealPath())
            ->fit(800, 500, function ($constraint) { $constraint->upsize(); })
            ->encode('webp', 90)
            ->save($thumbnailpath);
    }
    else{
        $fileNameToStore = $technology->image_path; 
    }


     // Logo Upload (New Optional)
    if ($request->hasFile('logo')) {
        // Delete old logo if exists
        if (!empty($technology->logo_path)) {
            $oldLogoPath = public_path('uploads/' . $this->path . '/' . $technology->logo_path);
            if (File::isFile($oldLogoPath)) {
                File::delete($oldLogoPath);
            }
        }

        $logoFile = $request->file('logo');
        $logoFilename = pathinfo($logoFile->getClientOriginalName(), PATHINFO_FILENAME);
        $logoFileNameToStore = $logoFilename . '_' . time() . '.webp';

        // Use the same path as the main image for the logo
        $logoPath = public_path('uploads/' . $this->path . '/');
        if (!File::exists($logoPath)) {
            File::makeDirectory($logoPath, 0777, true, true);
        }

        Image::make($logoFile->getRealPath())
            ->resize(200, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode('webp', 90)
            ->save($logoPath . $logoFileNameToStore);
    } else {
        $logoFileNameToStore = $technology->logo_path;
    }
    // Get content with media file
    $content = $request->input('description');

    $dom = new \DomDocument();
    libxml_use_internal_errors(true);
    $dom->encoding = 'utf-8';
    $dom->loadHtml(utf8_decode($content), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);    
    $images = $dom->getElementsByTagName('img');
    foreach($images as $img){
        $src = $img->getAttribute('src');

        if(preg_match('/data:image/', $src)){                
            preg_match('/data:image\/(?<mime>.*?)\;/', $src, $groups);
            $mimetype = $groups['mime'];                
            $filename = uniqid().'_'.time();

            $path = public_path('uploads/media/');
            if (! File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            $filepath = "/uploads/media/$filename.webp";    
            Image::make($src)
                ->resize(800, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 90)
                ->save(public_path($filepath));                

            $new_src = asset($filepath);
            $img->removeAttribute('src');
            $img->setAttribute('src', $new_src);
        }
    }

    // Update Data
    $technology->title = $request->title;
    $technology->service_id = $request->service_id;
    $technology->keywords = $request->keywords;
    $technology->price = $request->price;
    $technology->starting_price = $request->starting_price;
    $technology->priceCurrency = $request->priceCurrency;
    $technology->average_rating = $request->average_rating;
    $technology->review_count = $request->review_count;
    $technology->short_title = $request->short_title;
    $technology->meta_title = $request->meta_title;
    $technology->slug = Str::slug(strtolower($request->slug), '-');
    $technology->short_desc = $request->short_desc;
    $technology->description = $dom->saveHTML();
    $technology->image_path = $fileNameToStore;
    $technology->logo_path = $logoFileNameToStore;
    $technology->status = $request->status;
    $technology->manu = $request->manu;
    $technology->save();

    Toastr::success(__('dashboard.updated_successfully'), __('dashboard.success'));

    return redirect()->route('admin.technologies.index');
}

    public function destroy(Technology $technology)
    {
        // Delete Data
        $image_path = public_path('uploads/'.$this->path.'/'.$technology->image_path);
        if(File::isFile($image_path)){
            File::delete($image_path);
        }

        $technology->delete();

        Toastr::success(__('dashboard.deleted_successfully'), __('dashboard.success'));

        return redirect()->back();
    }
}
