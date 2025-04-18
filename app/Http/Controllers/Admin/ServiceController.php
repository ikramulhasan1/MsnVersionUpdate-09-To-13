<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Service;
use Toastr;
use Image;
use File;

class ServiceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Module Data
        $this->title = trans_choice('dashboard.service', 1);
        $this->route = 'admin.service';
        $this->view = 'admin.service';
        $this->path = 'service';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = $this->path;

        $data['rows'] = Service::orderBy('id', 'asc')->get();

        return view($this->view.'.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;

        return view($this->view.'.create', $data);
    }

   
    // public function store(Request $request)
    // {
    //     // Field Validation
    //     $request->validate([
    //         'title' => 'required|max:191|unique:services,title',
    //         'short_title' => 'required|max:30|unique:services,short_title',
    //         'short_desc' => 'required',
    //         'description' => 'required',
    //         'image' => 'required|image',
    //     ]);


    //     // image upload, fit and store inside public folder 
    //     if($request->hasFile('image')){
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
    //         $fileNameToStore = 'noimage.jpg'; // if no image selected this will be the default image
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


    //     // Insert Data
    //     $service = new Service;
    //     $service->title = $request->title;
    //     $service->short_title = $request->short_title;
    //     $service->slug = Str::slug(strtolower($request->slug), '-');
    //     $service->short_desc = $request->short_desc;
    //     $service->description = $dom->saveHTML();
    //     $service->image_path = $fileNameToStore;
    //     $service->manu = $request->manu;
    //     $service->save();


    //     Toastr::success(__('dashboard.created_successfully'), __('dashboard.success'));

    //     return redirect()->route($this->route.'.index');
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
            'faqs.*.title' => 'required|string',
            'faqs.*.description' => 'required|string',
        ]);

         // Remove duplicate keywords but keep multi-word keywords intact
        $keywords = array_unique(array_map('trim', explode(',', $request->keywords)));

        // Check for existing keywords in other articles
        $existingKeywords = Service::whereRaw("FIND_IN_SET(keywords, ?) > 0", [implode(',', $keywords)])->exists();
        if ($existingKeywords) {
            return back()->withErrors(['keywords' => 'Some keywords already exist. Please use unique tags.']);
        }

        // Image upload, fit, and store inside public folder 
        if($request->hasFile('image')){
            $filenameWithExt = $request->file('image')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME); 
            $fileNameToStore = $filename.'_'.time().'.webp'; // Save as WebP

            // Create Folder Location
            $path = public_path('uploads/'.$this->path.'/');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            // Resize, Convert to WebP, and Save
            $thumbnailpath = $path.$fileNameToStore;
            Image::make($request->file('image')->getRealPath())
                ->fit(800, 500, function ($constraint) {
                    $constraint->upsize();
                })
                ->encode('webp', 90) // Encode as WebP with 90% quality
                ->save($thumbnailpath);
        } else {
            $fileNameToStore = 'noimage.jpg'; // Default image if none uploaded
        }

        // Get content with media file
        $content = $request->input('description');

        $dom = new \DomDocument();
        libxml_use_internal_errors(true);
        $dom->encoding = 'utf-8';
        $dom->loadHtml(utf8_decode($content), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);    
        $images = $dom->getElementsByTagName('img');

        // foreach <img> in the submitted content
        foreach($images as $img) {
            $src = $img->getAttribute('src');

            if (preg_match('/data:image/', $src)) {                
                preg_match('/data:image\/(?<mime>.*?)\;/', $src, $groups);
                $mimetype = 'webp'; // Force conversion to WebP

                $filename = uniqid().'_'.time();

                $path = public_path('uploads/media/');
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0777, true, true);
                }

                $filepath = "/uploads/media/$filename.$mimetype";    
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
        $service = new Service;
        $service->title = $request->title;
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


        foreach ($request->faqs as $faq) {
            $service->faqs()->create([
                'service_id' => $service->id,
                'category_id' => $request->category_id,
                'description' => $faq['description'],
                'title' => $faq['title'],
            ]);
        }
        Toastr::success(__('dashboard.created_successfully'), __('dashboard.success'));

        return redirect()->route($this->route.'.index');
    }



    public function show(Service $service)
    {
        //
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = $this->path;

        $data['row'] = $service;

        return view($this->view.'.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Service $service)
    {
        //
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = $this->path;

        $data['row'] = $service;

        return view($this->view.'.edit', $data);
    }

   
    // public function update(Request $request, Service $service)
    // {
    //     // Field Validation
    //     $request->validate([
    //         'title' => 'required|max:191|unique:services,title,'.$service->id,
    //         'short_title' => 'required|max:30|unique:services,short_title,'.$service->id,
    //         'short_desc' => 'required',
    //         'description' => 'required',
    //         'image' => 'nullable|image',
    //     ]);


    //     // image upload, fit and store inside public folder 
    //     if($request->hasFile('image')){

    //         $file_path = public_path('uploads/'.$this->path.'/'.$service->image_path);
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

    //         $fileNameToStore = $service->image_path; 
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
    //     $service->title = $request->title;
    //     $service->short_title = $request->short_title;
    //     $service->slug = Str::slug(strtolower($request->slug), '-');
    //     $service->short_desc = $request->short_desc;
    //     $service->description = $dom->saveHTML();
    //     $service->image_path = $fileNameToStore;
    //     $service->manu = $request->manu;
    //     $service->status = $request->status;
    //     $service->save();


    //     Toastr::success(__('dashboard.updated_successfully'), __('dashboard.success'));

    //     return redirect()->back();
    // }


    public function update(Request $request, Service $service)
{
    // Field Validation
    $request->validate([
        'title' => 'required|max:191|unique:services,title,'.$service->id,
        'short_title' => 'required|max:30|unique:services,short_title,'.$service->id,
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
        'faqs.*.title' => 'required|string',
        'faqs.*.description' => 'required|string',
    ]);

    $keywords = array_unique(array_map('trim', explode(',', $request->keywords)));

    // Check for existing keywords in other articles (excluding the current article)
    $existingKeywords = Service::where('id', '!=', $service->id)
                               ->whereRaw("FIND_IN_SET(keywords, ?) > 0", [implode(',', $keywords)])
                               ->exists();

    if ($existingKeywords) {
        return back()->withErrors(['keywords' => 'Some keywords already exist. Please use unique tags.']);
    }

    // Image upload, fit, and store inside public folder 
    if($request->hasFile('image')){

        $file_path = public_path('uploads/'.$this->path.'/'.$service->image_path);
        if(File::isFile($file_path)){
            File::delete($file_path);
        }

        // Upload New Image
        $filenameWithExt = $request->file('image')->getClientOriginalName();
        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME); 
        $fileNameToStore = $filename.'_'.time().'.webp'; // Save as WebP

        // Create Folder Location
        $path = public_path('uploads/'.$this->path.'/');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        // Resize, Convert to WebP, and Save
        $thumbnailpath = $path.$fileNameToStore;
        Image::make($request->file('image')->getRealPath())
            ->fit(800, 500, function ($constraint) {
                $constraint->upsize();
            })
            ->encode('webp', 90)
            ->save($thumbnailpath);
    } else {
        $fileNameToStore = $service->image_path; 
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
            $mimetype = 'webp';

            $filename = uniqid().'_'.time();

            $path = public_path('uploads/media/');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            $filepath = "/uploads/media/$filename.$mimetype";    
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
    $service->title = $request->title;
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
    $service->status = $request->status;
    $service->save();

    foreach ($request->faqs as $faq) {
        $service->faqs()->create([
            'service_id' => $service->id,
            'category_id' => $request->category_id,
            'title' => $faq['title'],
            'description' => $faq['description'],
        ]);
    }

    Toastr::success(__('dashboard.updated_successfully'), __('dashboard.success'));

    return redirect()->back();
}




    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $service)
    {
        // Delete Data
        $image_path = public_path('uploads/'.$this->path.'/'.$service->image_path);
        if(File::isFile($image_path)){
            File::delete($image_path);
        }

        $service->delete();

        Toastr::success(__('dashboard.deleted_successfully'), __('dashboard.success'));

        return redirect()->back();
    }
}
