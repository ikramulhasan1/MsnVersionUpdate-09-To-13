<?php

namespace App\Http\Controllers\Admin;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Subservice;
use Illuminate\Support\Str;
use Toastr;
use Image;
use File;

class SubserviceController extends Controller
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
        $data['rows'] = Subservice::with('service')->orderBy('id', 'asc')->get();
        return view('admin.subservices.index',$data);
    }


    public function create()
    {
        $services = Service::orderBy('id', 'asc')->get();
        return view('admin.subservices.create', compact('services'));
    }

    
    // public function store(Request $request)
    // {
    //     // Field Validation
    //     $request->validate([
    //     'title' => 'required|max:191|unique:subservices,title',
    //     'short_desc' => 'required',
    //     'description' => 'required',
    //     'image' => 'required|image',
    // ]);


    // // image upload, fit and store inside public folder 
    // if($request->hasFile('image')){
    //     //Upload New Image
    //     $filenameWithExt = $request->file('image')->getClientOriginalName();
    //     $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME); 
    //     $extension = $request->file('image')->getClientOriginalExtension();
    //     $fileNameToStore = $filename.'_'.time().'.'.$extension;

    //     //Crete Folder Location
    //     $path = public_path('uploads/'.$this->path.'/');
    //     if (! File::exists($path)) {
    //         File::makeDirectory($path, 0777, true, true);
    //     }

    //     //Resize And Crop as Fit image here (800 width, 500 height)
    //     $thumbnailpath = $path.$fileNameToStore;
    //     $img = Image::make($request->file('image')->getRealPath())->fit(800, 500, function ($constraint) { $constraint->upsize(); })->save($thumbnailpath);
    // }
    // else{
    //     $fileNameToStore = 'noimage.jpg'; // if no image selected this will be the default image
    // }


    // // Get content with media file
    // $content=$request->input('description');
    
    // $dom = new \DomDocument();
    // libxml_use_internal_errors(true);
    // $dom->encoding = 'utf-8';
    // $dom->loadHtml(utf8_decode($content), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);    
    // $images = $dom->getElementsByTagName('img');
    // // foreach <img> in the submited content
    // foreach($images as $img){
    //     $src = $img->getAttribute('src');
        
    //     // if the img source is 'data-url'
    //     if(preg_match('/data:image/', $src)){                
    //         // get the mimetype
    //         preg_match('/data:image\/(?<mime>.*?)\;/', $src, $groups);
    //         $mimetype = $groups['mime'];                
    //         // Generating a random filename
    //         $filename = uniqid().'_'.time();

    //         //Crete Folder Location
    //         $path = public_path('uploads/media/');
    //         if (! File::exists($path)) {
    //             File::makeDirectory($path, 0777, true, true);
    //         }

    //         $filepath = "/uploads/media/$filename.$mimetype";    
    //         // @see http://image.intervention.io/api/
    //         $image = Image::make($src)
    //             // resize if required
    //             //->resize(500, null) 
    //             ->resize(800, null, function ($constraint) {
    //                 $constraint->aspectRatio();
    //                 $constraint->upsize();
    //             })
    //             ->encode($mimetype, 100)  // encode file to the specified mimetype
    //             ->save(public_path($filepath));                
    //         $new_src = asset($filepath);
    //         $img->removeAttribute('src');
    //         $img->setAttribute('src', $new_src);
    //     } // <!--endif
    // } // <!-


    // // Insert Data
    // $service = new Subservice;
    // $service->title = $request->title;
    // $service->service_id = $request->service_id;
    // $service->slug = Str::slug($request->title, '-');
    // $service->short_desc = $request->short_desc;
    // $service->description = $dom->saveHTML();
    // $service->image_path = $fileNameToStore;
    // $service->save();


    // Toastr::success(__('dashboard.created_successfully'), __('dashboard.success'));

    // return redirect()->route('admin.subservices.index');
    // }

    public function store(Request $request)
{
    // Field Validation
    $request->validate([
        'title' => 'required|max:191|unique:services,title',
        'short_title' => 'required|max:30|unique:services,short_title',
        'meta_title' => 'required|max:70',
        'keywords' => 'required',
        'price' => 'required',
        'starting_price' => 'required',
        'priceCurrency' => 'required',
        'average_rating' => 'required',
        'review_count' => 'required',
        'short_desc' => 'required',
        'description' => 'required',
        'image' => 'required|image',
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
    $service = new Subservice;
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
    $service->manu = $request->manu;
    $service->save();

    Toastr::success(__('dashboard.created_successfully'), __('dashboard.success'));
    return redirect()->route('admin.subservices.index');
}

    public function show(Subservice $subservice)
    {
        //
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = $this->path;

        $data['row'] = $subservice;

        return view('admin.subservices.show', $data);
    }

    
    public function edit(Subservice $subservice)
    {
        $data['path'] = $this->path;
        $data['subservice'] = $subservice;
        $data['services'] = Service::orderBy('id', 'asc')->get();
        return view('admin.subservices.edit',$data);
    }

    
    // public function update(Request $request, Subservice $subservice)
    // {
    //     // Field Validation
    //     $request->validate([
    //         'title' => 'required|max:191|unique:subservices,title,'.$subservice->id,
    //         'short_desc' => 'required',
    //         'description' => 'required',
    //         'image' => 'nullable|image',
    //     ]);


    //     // image upload, fit and store inside public folder 
    //     if($request->hasFile('image')){

    //         $file_path = public_path('uploads/'.$this->path.'/'.$subservice->image_path);
    //         if(File::isFile($file_path)){
    //             File::delete($file_path);
    //         }

    //         //Upload New Image
    //         $filenameWithExt = $request->file('image')->getClientOriginalName();
    //         $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME); 
    //         $extension = $request->file('image')->getClientOriginalExtension();
    //         $fileNameToStore = $filename.'_'.time().'.'.$extension;

    //         //Crete Folder Location
    //         $path = public_path('uploads/'.$this->path.'/');
    //         if (! File::exists($path)) {
    //             File::makeDirectory($path, 0777, true, true);
    //         }

    //         //Resize And Crop as Fit image here (800 width, 500 height)
    //         $thumbnailpath = $path.$fileNameToStore;
    //         $img = Image::make($request->file('image')->getRealPath())->fit(800, 500, function ($constraint) { $constraint->upsize(); })->save($thumbnailpath);
    //     }
    //     else{

    //         $fileNameToStore = $subservice->image_path; 
    //     }


    //     // Get content with media file
    //     $content=$request->input('description');
        
    //     $dom = new \DomDocument();
    //     libxml_use_internal_errors(true);
    //     $dom->encoding = 'utf-8';
    //     $dom->loadHtml(utf8_decode($content), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);    
    //     $images = $dom->getElementsByTagName('img');
    //    // foreach <img> in the submited content
    //     foreach($images as $img){
    //         $src = $img->getAttribute('src');
            
    //         // if the img source is 'data-url'
    //         if(preg_match('/data:image/', $src)){                
    //             // get the mimetype
    //             preg_match('/data:image\/(?<mime>.*?)\;/', $src, $groups);
    //             $mimetype = $groups['mime'];                
    //             // Generating a random filename
    //             $filename = uniqid().'_'.time();

    //             //Crete Folder Location
    //             $path = public_path('uploads/media/');
    //             if (! File::exists($path)) {
    //                 File::makeDirectory($path, 0777, true, true);
    //             }

    //             $filepath = "/uploads/media/$filename.$mimetype";    
    //             // @see http://image.intervention.io/api/
    //             $image = Image::make($src)
    //               // resize if required
    //               //->resize(500, null) 
    //               ->resize(800, null, function ($constraint) {
    //                     $constraint->aspectRatio();
    //                     $constraint->upsize();
    //                 })
    //               ->encode($mimetype, 100)  // encode file to the specified mimetype
    //               ->save(public_path($filepath));                
    //             $new_src = asset($filepath);
    //             $img->removeAttribute('src');
    //             $img->setAttribute('src', $new_src);
    //         } // <!--endif
    //     } // <!-


    //     // Update Data
    //     $subservice->title = $request->title;
    //     $subservice->slug = Str::slug($request->title, '-');
    //     $subservice->short_desc = $request->short_desc;
    //     $subservice->description = $dom->saveHTML();
    //     $subservice->image_path = $fileNameToStore;
    //     $subservice->status = $request->status;
    //     $subservice->save();


    //     Toastr::success(__('dashboard.updated_successfully'), __('dashboard.success'));

    //     return redirect()->route('admin.subservices.index');
    // }

    public function update(Request $request, Subservice $subservice)
{
    // Field Validation
    $request->validate([
        'title' => 'required|max:191|unique:subservices,title,'.$subservice->id,
        'short_title' => 'required|max:30|unique:services,short_title,'.$subservice->id,
        'meta_title' => 'required|max:70',
        'keywords' => 'required',
        'price' => 'required',
        'starting_price' => 'required',
        'priceCurrency' => 'required',
        'average_rating' => 'required',
        'review_count' => 'required',
        'short_desc' => 'required',
        'description' => 'required',
        'image' => 'nullable|image',
    ]);

    // image upload, fit and store inside public folder 
    if($request->hasFile('image')){

        $file_path = public_path('uploads/'.$this->path.'/'.$subservice->image_path);
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
        $fileNameToStore = $subservice->image_path; 
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
    $subservice->title = $request->title;
    $subservice->service_id = $request->service_id;
    $subservice->keywords = $request->keywords;
    $subservice->price = $request->price;
    $subservice->starting_price = $request->starting_price;
    $subservice->priceCurrency = $request->priceCurrency;
    $subservice->average_rating = $request->average_rating;
    $subservice->review_count = $request->review_count;
    $subservice->short_title = $request->short_title;
    $subservice->meta_title = $request->meta_title;
    $subservice->slug = Str::slug(strtolower($request->slug), '-');
    $subservice->short_desc = $request->short_desc;
    $subservice->description = $dom->saveHTML();
    $subservice->image_path = $fileNameToStore;
    $subservice->status = $request->status;
    $subservice->manu = $request->manu;
    $subservice->save();

    Toastr::success(__('dashboard.updated_successfully'), __('dashboard.success'));

    return redirect()->route('admin.subservices.index');
}

    public function destroy(Subservice $subservice)
    {
        // Delete Data
        $image_path = public_path('uploads/'.$this->path.'/'.$subservice->image_path);
        if(File::isFile($image_path)){
            File::delete($image_path);
        }

        $subservice->delete();

        Toastr::success(__('dashboard.deleted_successfully'), __('dashboard.success'));

        return redirect()->back();
    }
}
