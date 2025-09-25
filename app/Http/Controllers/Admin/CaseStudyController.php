<?php

namespace App\Http\Controllers\Admin;

use File;
use Image;
use Toastr;
use App\Models\Service;
use App\Models\CaseStudy;
use App\Models\Technology;
use App\Models\FaqCategory;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class CaseStudyController extends Controller
{
    public function __construct()
    {
        // Module Data
        $this->title = trans_choice('dashboard.case-studies', 1);
        $this->route = 'admin.case-studies';
        $this->view = 'admin.case-study';
        $this->path = 'case-study';
    }

    public function index()
    {
        //
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = $this->path;

        $data['rows'] = CaseStudy::orderBy('id', 'asc')->get();

        return view($this->view . '.index', $data);
    }

    public function create()
    {
        //
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;

        $data['services'] = Service::where('status', 1)->get();
        $data['technologies'] = Technology::where('status', 1)->get();
        $data['faqCategories'] = FaqCategory::where('status', 1)->get();

        return view($this->view . '.create', $data);
    }


    public function store(Request $request)
    {
        // Field Validation
        $request->validate([
            'main_title' => 'required|max:191|unique:case_studies,main_title',
            'the_client' => 'required',
            'the_client_desc' => 'required',
            'industry' => 'required',
            'tech_stack' => 'required',

            'services' => 'required|array',
            'services.*' => 'exists:services,id',
            'technologies' => 'nullable|array',
            'technologies.*' => 'exists:technologies,id',

            // 'faqs.*.title' => 'required|string',
            // 'faqs.*.description' => 'required|string',
        ]);

        // Remove duplicate keywords but keep multi-word keywords intact
        $keywords = array_unique(array_map('trim', explode(',', $request->keywords)));
        $existingKeywords = CaseStudy::whereRaw("FIND_IN_SET(keywords, ?) > 0", [implode(',', $keywords)])->exists();
        if ($existingKeywords) {
            return back()->withErrors(['keywords' => 'Some keywords already exist. Please use unique tags.']);
        }
        // Image upload, fit, and store inside public folder 
        if ($request->hasFile('image')) {
            $filenameWithExt = $request->file('image')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $fileNameToStore = $filename . '_' . time() . '.webp'; // Save as WebP

            // Create Folder Location
            $path = public_path('uploads/' . $this->path . '/');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            // Resize, Convert to WebP, and Save
            $thumbnailpath = $path . $fileNameToStore;
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
        $content = $request->input('the_client_desc');

        $dom = new \DomDocument();
        libxml_use_internal_errors(true);
        $dom->encoding = 'utf-8';
        $dom->loadHtml(utf8_decode($content), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $images = $dom->getElementsByTagName('img');

        // foreach <img> in the submitted content
        foreach ($images as $img) {
            $src = $img->getAttribute('src');

            if (preg_match('/data:image/', $src)) {
                preg_match('/data:image\/(?<mime>.*?)\;/', $src, $groups);
                $mimetype = 'webp'; // Force conversion to WebP

                $filename = uniqid() . '_' . time();

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
        $CaseStudy = new CaseStudy;
        $CaseStudy->main_title = $request->main_title;
        $CaseStudy->slug = Str::slug(strtolower($request->main_title), '-');
        $CaseStudy->meta_title = $request->meta_title;
        $CaseStudy->meta_desc = $request->meta_desc;
        $CaseStudy->keywords = $request->keywords;

        $CaseStudy->the_client = $request->the_client;
        $CaseStudy->the_client_desc = $request->the_client_desc;
        $CaseStudy->industry = $request->industry;
        $CaseStudy->tech_stack = is_array($request->tech_stack)
            ? implode(',', $request->tech_stack)
            : $request->tech_stack;
        $CaseStudy->country = $request->country;

        $CaseStudy->image_path = $fileNameToStore;
        $CaseStudy->status = $request->status;

        $caseSteps = [];

        foreach ($request->case as $index => $process) {
            $processImageName = null;

            if ($request->hasFile("case.$index.case_image")) {
                $file = $request->file("case.$index.case_image");
                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $processImageName = $filename . '_' . time() . '.webp';

                $path = public_path('uploads/' . $this->path . '/');
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0777, true, true);
                }

                Image::make($file->getRealPath())
                    ->resize(756, 419, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->encode('webp', 90)
                    ->save($path . $processImageName);
            }

            $caseSteps[] = [
                'case_title' => $process['case_title'],
                'case_description' => $process['case_description'],
                'case_image' => $processImageName,
            ];
        }

        // Save array as JSON
        $CaseStudy->case_steps = json_encode($caseSteps);
        $CaseStudy->save();

        $CaseStudy->services()->attach($request->services);
        $CaseStudy->technologies()->attach($request->technologies);


        // foreach ($request->faqs as $faq) {
        //     Faq::create([
        //         'category_id' => $faq['category_id'],
        //         'type' => $request->type,
        //         'title' => $faq['title'],
        //         'description' => $faq['description'],
        //         'service_id' => $CaseStudy->id,
        //     ]);
        // }
        Toastr::success(__('dashboard.created_successfully'), __('dashboard.success'));

        return redirect()->route($this->route . '.index');
    }



    public function show(CaseStudy $casestudy)
    {
        //
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = $this->path;

        $data['row'] = $casestudy;

        return view($this->view . '.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(CaseStudy $case_study)
    {
        //
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = $this->path;
        $data['services'] = Service::where('status', 1)->get();
        $data['faqCategories'] = FaqCategory::where('status', 1)->get();
        $data['technologies'] = Technology::where('status', 1)->get();
        $data['row'] = $case_study;

        return view($this->view . '.edit', $data);
    }



    //     public function update(Request $request, CaseStudy $casestudy)
// {
//     // Field Validation
//     $request->validate([
//         'main_title' => 'required|max:191|unique:case_studies,main_title'.$casestudy->id,
//         'the_client' => 'required',
//         'the_client_desc' => 'required',
//         'industry' => 'required',
//         'tech_stack' => 'required',

    //     ]);

    //     $keywords = array_unique(array_map('trim', explode(',', $request->keywords)));

    //     // Check for existing keywords in other articles (excluding the current article)
//     $existingKeywords = Service::where('id', '!=', $casestudy->id)
//                                ->whereRaw("FIND_IN_SET(keywords, ?) > 0", [implode(',', $keywords)])
//                                ->exists();

    //     if ($existingKeywords) {
//         return back()->withErrors(['keywords' => 'Some keywords already exist. Please use unique tags.']);
//     }

    //     // Image upload, fit, and store inside public folder 
//     if($request->hasFile('image')){

    //         $file_path = public_path('uploads/'.$this->path.'/'.$casestudy->image_path);
//         if(File::isFile($file_path)){
//             File::delete($file_path);
//         }

    //         // Upload New Image
//         $filenameWithExt = $request->file('image')->getClientOriginalName();
//         $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME); 
//         $fileNameToStore = $filename.'_'.time().'.webp'; // Save as WebP

    //         // Create Folder Location
//         $path = public_path('uploads/'.$this->path.'/');
//         if (!File::exists($path)) {
//             File::makeDirectory($path, 0777, true, true);
//         }

    //         // Resize, Convert to WebP, and Save
//         $thumbnailpath = $path.$fileNameToStore;
//         Image::make($request->file('image')->getRealPath())
//             ->fit(800, 500, function ($constraint) {
//                 $constraint->upsize();
//             })
//             ->encode('webp', 90)
//             ->save($thumbnailpath);
//     } else {
//         $fileNameToStore = $casestudy->image_path; 
//     }

    //     // Get content with media file
//     $content = $request->input('description');

    //     $dom = new \DomDocument();
//     libxml_use_internal_errors(true);
//     $dom->encoding = 'utf-8';
//     $dom->loadHtml(utf8_decode($content), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);    
//     $images = $dom->getElementsByTagName('img');

    //     foreach($images as $img){
//         $src = $img->getAttribute('src');

    //         if(preg_match('/data:image/', $src)){                
//             preg_match('/data:image\/(?<mime>.*?)\;/', $src, $groups);
//             $mimetype = 'webp';

    //             $filename = uniqid().'_'.time();

    //             $path = public_path('uploads/media/');
//             if (!File::exists($path)) {
//                 File::makeDirectory($path, 0777, true, true);
//             }

    //             $filepath = "/uploads/media/$filename.$mimetype";    
//             Image::make($src)
//                 ->resize(800, null, function ($constraint) {
//                     $constraint->aspectRatio();
//                     $constraint->upsize();
//                 })
//                 ->encode('webp', 90)
//                 ->save(public_path($filepath));

    //             $new_src = asset($filepath);
//             $img->removeAttribute('src');
//             $img->setAttribute('src', $new_src);
//         }
//     }

    //     // Update Data
//     $casestudy->title = $request->title;
//     $casestudy->keywords = $request->keywords;
//     $casestudy->price = $request->price;
//     $casestudy->starting_price = $request->starting_price;
//     $casestudy->priceCurrency = $request->priceCurrency;
//     $casestudy->average_rating = $request->average_rating;
//     $casestudy->review_count = $request->review_count;
//     $casestudy->short_title = $request->short_title;
//     $casestudy->meta_title = $request->meta_title;
//     $casestudy->slug = Str::slug(strtolower($request->slug), '-');
//     $casestudy->short_desc = $request->short_desc;
//     $casestudy->description = $dom->saveHTML();
//     $casestudy->image_path = $fileNameToStore;
//     $casestudy->manu = $request->manu;
//     $casestudy->status = $request->status;
//     $casestudy->save();

    //     if ($request->has('faqs')) {
//         foreach ($request->faqs as $faq) {
//             Faq::updateOrCreate([
//                 'category_id' => $faq['category_id'],
//                 'type' => $request->type,
//                 'title' => $faq['title'],
//                 'description' => $faq['description'],
//                 'service_id' => $casestudy->id,
//             ]);
//         }
//     }
//     // 
//     if ($request->has('case')) {
//         foreach ($request->case as $index => $process) {
//             $processImageName = null;

    //             // Check if record already exists
//             $existing = Processwork::where('title', $process['title'])
//                 ->where('service_id', $casestudy->id)
//                 ->first();

    //             // Check if new image uploaded
//             if ($request->hasFile("case.$index.process_image")) {
//                 $file = $request->file("case.$index.process_image");
//                 $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
//                 $processImageName = $filename.'_'.time().'.webp';

    //                 $path = public_path('uploads/process/');
//                 if (!File::exists($path)) {
//                     File::makeDirectory($path, 0777, true, true);
//                 }

    //                 // Delete old image if exists
//                 if ($existing && $existing->image_path && File::exists($path . $existing->image_path)) {
//                     File::delete($path . $existing->image_path);
//                 }

    //                 // Save new image
//                 Image::make($file->getRealPath())
//                     ->resize(100, 100, function ($constraint) {
//                         $constraint->aspectRatio();
//                         $constraint->upsize();
//                     })
//                     ->encode('webp', 90)
//                     ->save($path . $processImageName);
//             }

    //             // Use new image or retain existing
//             $finalImagePath = $processImageName ?? ($existing->image_path ?? null);

    //             Processwork::updateOrCreate(
//                 ['title' => $process['title'], 'service_id' => $casestudy->id],
//                 ['description' => $process['description'], 'image_path' => $finalImagePath]
//             );
//         }
//     }

    //     // 
//     if ($request->has('industries')) {
//         foreach ($request->industries as $industry) {
//             Industry::updateOrCreate(
//                 ['title' =>  trim($industry['title'])], // Only use title for the match
//                 [
//                     'link' => trim($industry['link']) ?: null, // Even if empty, set it
//                     'service_id' => $casestudy->id,
//                 ]
//             );
//         }
//     }

    //     // 
//     if ($request->has('whywes')) {
//         foreach ($request->whywes as $we) {
//             Whywe::updateOrCreate(
//                 ['title' =>  trim($we['title'])], // Only use title for the match
//                 [
//                     'link' => trim($we['link']) ?: null, // Even if empty, set it
//                     'service_id' => $service->id,
//                 ]
//             );
//         }
//     }

    //     Toastr::success(__('dashboard.updated_successfully'), __('dashboard.success'));

    //     return redirect()->back();
// }

    public function update(Request $request, CaseStudy $case_study)
    {
        
        // Validation
        $request->validate([
            'main_title' => 'required|max:191|unique:case_studies,main_title,' . $case_study->id,
            'the_client' => 'required',
            'the_client_desc' => 'required',
            'industry' => 'required',
            'tech_stack' => 'required',
            'services' => 'required|array',
            'services.*' => 'exists:services,id',
            'technologies' => 'nullable|array',
            'technologies.*' => 'exists:technologies,id',
            // 'faqs.*.title' => 'required|string',
            // 'faqs.*.description' => 'required|string',
        ]);

        // Keywords check
        $keywords = array_unique(array_map('trim', explode(',', $request->keywords)));
        $existingKeywords = CaseStudy::whereRaw("FIND_IN_SET(keywords, ?) > 0", [implode(',', $keywords)])
            ->where('id', '!=', $case_study->id)
            ->exists();
        if ($existingKeywords) {
            return back()->withErrors(['keywords' => 'Some keywords already exist. Please use unique tags.']);
        }

        // Update Image if uploaded
        if ($request->hasFile('image')) {
            $filenameWithExt = $request->file('image')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $fileNameToStore = $filename . '_' . time() . '.webp';

            $path = public_path('uploads/' . $this->path . '/');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            Image::make($request->file('image')->getRealPath())
                ->fit(800, 500, function ($constraint) {
                    $constraint->upsize();
                })
                ->encode('webp', 90)
                ->save($path . $fileNameToStore);

            $case_study->image_path = $fileNameToStore;
        }

        // Process description images (like in store)
        $content = $request->input('the_client_desc');
        $dom = new \DomDocument();
        libxml_use_internal_errors(true);
        $dom->encoding = 'utf-8';
        $dom->loadHtml(utf8_decode($content), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $images = $dom->getElementsByTagName('img');

        foreach ($images as $img) {
            $src = $img->getAttribute('src');

            if (preg_match('/data:image/', $src)) {
                preg_match('/data:image\/(?<mime>.*?)\;/', $src, $groups);
                $mimetype = 'webp'; // Force conversion

                $filename = uniqid() . '_' . time();
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
        $case_study->the_client_desc = $dom->saveHTML();

        // Update Fields
        $case_study->main_title = $request->main_title;
        $case_study->slug = Str::slug(strtolower($request->main_title), '-');
        $case_study->meta_title = $request->meta_title;
        $case_study->meta_desc = $request->meta_desc;
        $case_study->keywords = $request->keywords;
        $case_study->the_client = $request->the_client;
        $case_study->industry = $request->industry;
        $case_study->tech_stack = is_array($request->tech_stack)
            ? implode(',', $request->tech_stack)
            : $request->tech_stack;
        $case_study->country = $request->country;
        $case_study->status = $request->status;

        $caseSteps = [];
        if ($request->case) {
            foreach ($request->case as $index => $process) {
                $processImageName = $process['old_case_image'] ?? null;

                if ($request->hasFile("case.$index.case_image")) {
                    $file = $request->file("case.$index.case_image");
                    $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $processImageName = $filename . '_' . time() . '.webp';

                    $path = public_path('uploads/' . $this->path . '/');
                    if (!File::exists($path)) {
                        File::makeDirectory($path, 0777, true, true);
                    }

                    Image::make($file->getRealPath())
                        ->resize(756, 419, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        })
                        ->encode('webp', 90)
                        ->save($path . $processImageName);
                }

                $caseSteps[] = [
                    'case_title'       => $process['case_title'] ?? '',
                    'case_description' => $process['case_description'] ?? '',
                    'case_image'       => $processImageName,
                ];
            }
        }

        // ✅ Directly assign array (no json_encode)
        $case_study->case_steps = json_encode($caseSteps);


        // FAQ Update (if any)
        // if ($request->faqs) {
        //     // Remove old FAQs
        //     $case_study->faqs()->delete();
        //     foreach ($request->faqs as $faq) {
        //         $case_study->faqs()->create([
        //             'category_id' => $faq['category_id'] ?? null,
        //             'title' => $faq['title'] ?? '',
        //             'description' => $faq['description'] ?? '',
        //         ]);
        //     }
        // }

        // Save main case study
        $case_study->save();

        // Clean services & technologies arrays
        $services = array_filter($request->services ?? [], fn($id) => !empty($id));
        $technologies = array_filter($request->technologies ?? [], fn($id) => !empty($id));

        // Sync Relations safely
        $case_study->services()->sync($services);
        $case_study->technologies()->sync($technologies);

        Toastr::success(__('dashboard.updated_successfully'), __('dashboard.success'));
        return redirect()->route($this->route . '.index');
    }




    public function destroy(CaseStudy $case_study)
    {
        // Delete Data
        $image_path = public_path('uploads/' . $this->path . '/' . $case_study->image_path);
        if (File::isFile($image_path)) {
            File::delete($image_path);
        }

        $case_study->delete();

        Toastr::success(__('dashboard.deleted_successfully'), __('dashboard.success'));

        return redirect()->back();
    }
}
