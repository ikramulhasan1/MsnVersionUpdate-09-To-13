<?php

namespace App\Http\Controllers\Admin;

use File;
use Image;
use Toastr;
use App\Models\Service;
use App\Models\Portfolio;
use App\Models\Subservice;
use App\Models\Technology;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubserviceController extends Controller
{
    public function __construct()
    {
        // Module Data
        $this->title = trans_choice('dashboard.subservice', 2);
        $this->route = 'admin.subservices';
        $this->view = 'admin.subservices';
        $this->path = 'subservices';
    }
    public function index()
    {

        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = $this->path;

        $data['rows'] = Subservice::with('service')->orderBy('id', 'asc')->get();
        return view($this->view . '.index', $data);
    }


    public function create()
    {
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = $this->path;

        $data['allTechnologies'] = Technology::all();
        $data['allPortfolios'] = Portfolio::all();
        $data['services'] = Service::orderBy('id', 'asc')->get();
        return view($this->view . '.create', $data);
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
        // ✅ Validate all fields
        $request->validate([
            'title' => 'required|max:191|unique:subservices,title',
            'short_title' => 'required|max:30|unique:subservices,short_title',
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
            'technologies' => 'nullable|array',
            'technologies.*' => 'exists:technologies,id',
            'portfolios' => 'nullable|array',
            'portfolios.*' => 'exists:portfolios,id',
        ]);

        /**
         * ==========================
         * 🔹 IMAGE UPLOAD (Main Thumbnail)
         * ==========================
         */
        if ($request->hasFile('image')) {
            $filename = pathinfo($request->file('image')->getClientOriginalName(), PATHINFO_FILENAME);
            $fileNameToStore = $filename . '_' . time() . '.webp';

            $path = public_path('uploads/' . $this->path . '/');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            // Resize and convert to WebP
            Image::make($request->file('image')->getRealPath())
                ->fit(800, 500, function ($constraint) {
                    $constraint->upsize();
                })
                ->encode('webp', 90)
                ->save($path . $fileNameToStore);
        } else {
            $fileNameToStore = 'noimage.webp';
        }

        /**
         * ==========================
         * 🔹 HANDLE HTML CONTENT IMAGES
         * ==========================
         */
        $content = $request->input('description');
        $dom = new \DomDocument();
        libxml_use_internal_errors(true);
        $dom->encoding = 'utf-8';
        $dom->loadHtml(utf8_decode($content), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $images = $dom->getElementsByTagName('img');

        foreach ($images as $img) {
            $src = $img->getAttribute('src');

            if (preg_match('/data:image/', $src)) {
                preg_match('/data:image\/(?<mime>.*?)\;/', $src, $groups);
                $filename = uniqid() . '_' . time();
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

                $img->setAttribute('src', asset($filepath));
            }
        }

        /**
         * ==========================
         * 🔹 CREATE NEW SUBSERVICE
         * ==========================
         */
        $subservice = new Subservice();
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
        $subservice->status = $request->status ?? 1;
        $subservice->manu = $request->manu;

        /**
         * ==========================
         * 🔹 BANNER SECTION
         * ==========================
         */
        $bannerSteps = [];
        if ($request->has('banner')) {
            foreach ($request->banner as $index => $banner) {
                $bannerImageName = null;

                if ($request->hasFile("banner.$index.banner_image")) {
                    $file = $request->file("banner.$index.banner_image");
                    $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $bannerImageName = $filename . '_' . time() . '.webp';

                    $path = public_path('uploads/banner/');
                    if (!File::exists($path)) {
                        File::makeDirectory($path, 0777, true, true);
                    }

                    Image::make($file->getRealPath())
                        ->encode('webp', 90)
                        ->save($path . $bannerImageName);
                }

                $bannerSteps[] = [
                    'title' => $banner['title'] ?? '',
                    'sub_title' => $banner['sub_title'] ?? '',
                    'banner_image' => $bannerImageName ?? '',
                ];
            }
        }
        $subservice->banner_steps = json_encode($bannerSteps);

        /**
         * ==========================
         * 🔹 OTHER JSON SECTIONS
         * ==========================
         */
        $sections = [
            'features_steps' => 'features',
            'process_steps' => 'process',
            'why_we_steps' => 'why_we',
            'industries_steps' => 'industry',
            'achievements_steps' => 'achievement',
            'success_stories_steps' => 'story',
            'clients_say_steps' => 'client',
            'faq_steps' => 'faq',
            'our_promise' => 'item',
            'cta_steps' => 'cta',
        ];

        foreach ($sections as $jsonKey => $inputName) {
            $steps = [];
            if ($request->has($inputName)) {
                foreach ($request->$inputName as $data) {
                    $steps[] = $data;
                }
            }
            $subservice->$jsonKey = json_encode($steps);
        }

        // ✅ Save the Subservice first
        $subservice->save();

        /**
         * ==========================
         * 🔹 SYNC RELATIONS (Pivot)
         * ==========================
         */
        $subservice->technologies()->sync($request->technologies ?? []);
        $subservice->portfolios()->sync($request->portfolios ?? []);

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
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = $this->path;

        $data['subservice'] = $subservice;
        $data['services'] = Service::orderBy('id', 'asc')->get();
        $data['allTechnologies'] = Technology::all();
        $data['allPortfolios'] = Portfolio::all();

        return view('admin.subservices.edit', $data);
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
            'title' => 'required|max:191|unique:subservices,title,' . $subservice->id,
            'short_title' => 'required|max:30|unique:services,short_title,' . $subservice->id,
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
            'technologies' => 'nullable|array',
            'technologies.*' => 'exists:technologies,id',
            'portfolios' => 'nullable|array',
            'portfolios.*' => 'exists:portfolios,id',
        ]);

        // image upload, fit and store inside public folder 
        if ($request->hasFile('image')) {

            $file_path = public_path('uploads/' . $this->path . '/' . $subservice->image_path);
            if (File::isFile($file_path)) {
                File::delete($file_path);
            }

            // Upload New Image
            $filename = pathinfo($request->file('image')->getClientOriginalName(), PATHINFO_FILENAME);
            $fileNameToStore = $filename . '_' . time() . '.webp';

            // Create Folder Location
            $path = public_path('uploads/' . $this->path . '/');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            // Resize and convert to WebP (800x500)
            $thumbnailpath = $path . $fileNameToStore;
            Image::make($request->file('image')->getRealPath())
                ->fit(800, 500, function ($constraint) {
                    $constraint->upsize();
                })
                ->encode('webp', 90)
                ->save($thumbnailpath);
        } else {
            $fileNameToStore = $subservice->image_path;
        }

        // Get content with media file
        $content = $request->input('description');

        $dom = new \DomDocument();
        libxml_use_internal_errors(true);
        $dom->encoding = 'utf-8';
        $dom->loadHtml(utf8_decode($content), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $images = $dom->getElementsByTagName('img');
        foreach ($images as $img) {
            $src = $img->getAttribute('src');

            if (preg_match('/data:image/', $src)) {
                preg_match('/data:image\/(?<mime>.*?)\;/', $src, $groups);
                $mimetype = $groups['mime'];
                $filename = uniqid() . '_' . time();

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


        $bannerSteps = [];

        // Decode old banner steps (so we can access previous image paths)
        $oldBannerSteps = json_decode($subservice->banner_steps ?? '[]', true);

        if ($request->has('banner')) {
            foreach ($request->banner as $index => $banner) {
                $bannerImageName = $banner['banner_image_old'] ?? ($oldBannerSteps[$index]['banner_image'] ?? null);

                // Check if new file uploaded
                if ($request->hasFile("banner.$index.banner_image")) {
                    $file = $request->file("banner.$index.banner_image");
                    $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $bannerImageName = $filename . '_' . time() . '.webp';

                    $path = public_path('uploads/banner/');
                    if (!File::exists($path)) {
                        File::makeDirectory($path, 0777, true, true);
                    }

                    // Delete old image if exists
                    if (!empty($oldBannerSteps[$index]['banner_image'])) {
                        $oldPath = $path . $oldBannerSteps[$index]['banner_image'];
                        if (File::exists($oldPath)) {
                            File::delete($oldPath);
                        }
                    }

                    // Save new image
                    Image::make($file->getRealPath())
                        ->encode('webp', 90)
                        ->save($path . $bannerImageName);
                }

                $bannerSteps[] = [
                    'title' => $banner['title'] ?? '',
                    'sub_title' => $banner['sub_title'] ?? '',
                    'banner_image' => $bannerImageName ?? '',
                ];
            }
        }

        // Store as JSON or let Eloquent cast handle it
        $subservice->banner_steps = json_encode($bannerSteps);

        /**
         * ==========================
         * 🔹 CORE FEATURES SECTION
         * ==========================
         */
        $featuresSteps = [];
        if ($request->has('features')) {
            foreach ($request->features as $feature) {
                $featuresSteps[] = [
                    'icon_class' => $feature['icon_class'] ?? '',
                    'title' => $feature['title'] ?? '',
                    'bottom_text' => $feature['bottom_text'] ?? '',
                ];
            }
        }
        $subservice->features_steps = json_encode($featuresSteps);

        /**
         * ==========================
         * 🔹 WORK PROCESS SECTION
         * ==========================
         */
        $processSteps = [];
        if ($request->has('process')) {
            foreach ($request->process as $step) {
                $processSteps[] = [
                    'title' => $step['title'] ?? '',
                    'bottom_text' => $step['bottom_text'] ?? '',
                ];
            }
        }
        $subservice->process_steps = json_encode($processSteps);

        /**
         * ==========================
         * 🔹 WHY CHOOSE US SECTION
         * ==========================
         */
        $whyWeSteps = [];
        if ($request->has('why_we')) {
            foreach ($request->why_we as $why) {
                $whyWeSteps[] = [
                    'icon_class' => $why['icon_class'] ?? '',
                    'title' => $why['title'] ?? '',
                    'bottom_text' => $why['bottom_text'] ?? '',
                ];
            }
        }
        $subservice->why_we_steps = json_encode($whyWeSteps);

        /**
         * ==========================
         * 🔹 INDUSTRIES SECTION
         * ==========================
         */
        $industriesSteps = [];
        if ($request->has('industry')) {
            foreach ($request->industry as $industry) {
                $industriesSteps[] = [
                    'icon_class' => $industry['icon_class'] ?? '',
                    'title' => $industry['title'] ?? '',
                ];
            }
        }
        $subservice->industries_steps = json_encode($industriesSteps);

        /**
         * ==========================
         * 🔹 ACHIEVEMENTS SECTION
         * ==========================
         */
        $achievementsSteps = [];
        if ($request->has('achievement')) {
            foreach ($request->achievement as $achievement) {
                $achievementsSteps[] = [
                    'count_number' => $achievement['count_number'] ?? '',
                    'title' => $achievement['title'] ?? '',
                ];
            }
        }
        $subservice->achievements_steps = json_encode($achievementsSteps);

        /**
         * ==========================
         * 🔹 SUCCESS STORIES SECTION
         * ==========================
         */
        $successStoriesSteps = [];
        if ($request->has('story')) {
            foreach ($request->story as $story) {
                $successStoriesSteps[] = [
                    'title' => $story['title'] ?? '',
                    'bottom_text' => $story['bottom_text'] ?? '',
                ];
            }
        }
        $subservice->success_stories_steps = json_encode($successStoriesSteps);

        /**
         * ==========================
         * 🔹 CLIENTS SAY SECTION
         * ==========================
         */
        $clientsSaySteps = [];
        if ($request->has('client')) {
            foreach ($request->client as $client) {
                $clientsSaySteps[] = [
                    'title' => $client['title'] ?? '',
                    'meassage' => $client['meassage'] ?? '',
                ];
            }
        }
        $subservice->clients_say_steps = json_encode($clientsSaySteps);

        /**
         * ==========================
         * 🔹 FAQ SECTION
         * ==========================
         */
        $faqSteps = [];
        if ($request->has('faq')) {
            foreach ($request->faq as $faq) {
                $faqSteps[] = [
                    'question' => $faq['question'] ?? '',
                    'answer' => $faq['answer'] ?? '',
                ];
            }
        }
        $subservice->faq_steps = json_encode($faqSteps);

        /**
         * ==========================
         * 🔹 OUR PROMISE SECTION
         * ==========================
         */
        $promiseSteps = [];
        if ($request->has('item')) {
            foreach ($request->item as $promise) {
                $promiseSteps[] = [
                    'bottom_text' => $promise['bottom_text'] ?? '',
                ];
            }
        }
        $subservice->our_promise = json_encode($promiseSteps);

        /**
         * ==========================
         * 🔹 CALL TO ACTION SECTION
         * ==========================
         */
        $ctaSteps = [];
        if ($request->has('cta')) {
            foreach ($request->cta as $cta) {
                $ctaSteps[] = [
                    'bottom_text' => $cta['bottom_text'] ?? '',
                ];
            }
        }
        $subservice->cta_steps = json_encode($ctaSteps);

        // 🔹 SYNC TECHNOLOGIES
        $subservice->technologies()->sync($request->technologies ?? []);
        // 🔹 SYNC PORTFOLIOS
        $subservice->portfolios()->sync($request->portfolios ?? []);
        $subservice->save();

        Toastr::success(__('dashboard.updated_successfully'), __('dashboard.success'));

        return redirect()->route('admin.subservices.index');
    }

    public function destroy(Subservice $subservice)
    {
        // Delete Data
        $image_path = public_path('uploads/' . $this->path . '/' . $subservice->image_path);
        if (File::isFile($image_path)) {
            File::delete($image_path);
        }

        $subservice->delete();

        Toastr::success(__('dashboard.deleted_successfully'), __('dashboard.success'));

        return redirect()->back();
    }
}
