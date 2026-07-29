<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Models\Service;
use App\Models\Subservice;
use App\Models\Technology;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Image;
use Toastr;

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

        return view($this->view.'.index', $data);
    }

    public function create()
    {
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = $this->path;

        $data['allTechnologies'] = Technology::all();
        $data['allPortfolios'] = Portfolio::all();
        $data['services'] = Service::with('technologies')->orderBy('id', 'asc')->get();

        return view($this->view.'.create', $data);
    }

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
            $fileNameToStore = $filename.'_'.time().'.webp';

            $path = public_path('uploads/'.$this->path.'/');
            if (! File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            // Resize and convert to WebP
            Image::make($request->file('image')->getRealPath())
                ->fit(800, 500, function ($constraint) {
                    $constraint->upsize();
                })
                ->encode('webp', 90)
                ->save($path.$fileNameToStore);
        } else {
            $fileNameToStore = 'noimage.webp';
        }

        /**
         * ==========================
         * 🔹 HANDLE HTML CONTENT IMAGES
         * ==========================
         */
        $content = $request->input('description');
        $dom = new \DomDocument;
        libxml_use_internal_errors(true);
        $dom->encoding = 'utf-8';
        $dom->loadHtml(utf8_decode($content), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $images = $dom->getElementsByTagName('img');

        foreach ($images as $img) {
            $src = $img->getAttribute('src');

            if (preg_match('/data:image/', $src)) {
                preg_match('/data:image\/(?<mime>.*?)\;/', $src, $groups);
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

                $img->setAttribute('src', asset($filepath));
            }
        }

        /**
         * ==========================
         * 🔹 CREATE NEW SUBSERVICE
         * ==========================
         */
        $subservice = new Subservice;
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
        $subservice->sub_service_icon = $request->sub_service_icon;
        $subservice->description = $dom->saveHTML();
        $subservice->image_path = $fileNameToStore;
        /**
         * ==========================
         * 🔹 SECTION HEADINGS (title/subtitle shown on the public page)
         * ==========================
         */
        $subservice->client_voices_section_title = $request->client_voices_section_title;
        $subservice->industries_section_title = $request->industries_section_title;
        $subservice->guarantee_section_title = $request->guarantee_section_title;
        $subservice->guarantee_section_subtitle = $request->guarantee_section_subtitle;
        $subservice->deliverables_section_title = $request->deliverables_section_title;
        $subservice->deliverables_section_subtitle = $request->deliverables_section_subtitle;
        $subservice->why_msn_softtech_section_title = $request->why_msn_softtech_section_title;
        $subservice->stack_section_title = $request->stack_section_title;
        $subservice->core_features_section_title = $request->core_features_section_title;
        $subservice->core_features_section_subtitle = $request->core_features_section_subtitle;
        $subservice->how_we_work_section_title = $request->how_we_work_section_title;
        $subservice->how_we_work_section_subtitle = $request->how_we_work_section_subtitle;
        $subservice->whats_included_section_title = $request->whats_included_section_title;
        $subservice->whats_included_section_subtitle = $request->whats_included_section_subtitle;
        $subservice->who_is_this_for_section_title = $request->who_is_this_for_section_title;
        $subservice->who_is_this_for_section_subtitle = $request->who_is_this_for_section_subtitle;
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
                    $bannerImageName = $filename.'_'.time().'.webp';

                    $path = public_path('uploads/banner/');
                    if (! File::exists($path)) {
                        File::makeDirectory($path, 0777, true, true);
                    }

                    Image::make($file->getRealPath())
                        ->encode('webp', 90)
                        ->save($path.$bannerImageName);
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
            'features_steps' => 'core_features',      // ✅ fixed (was 'features')
            'process_steps' => 'deliverables',        // ✅ fixed (was 'process')
            'why_we_steps' => 'who_is_this_for',      // ✅ fixed (was 'why_we')
            'industries_steps' => 'hero_badges',          // ✅ fixed (was 'industry')
            'achievements_steps' => 'achievements',         // ✅ fixed (was 'achievement')
            'success_stories_steps' => 'whats_included',       // ✅ fixed (was 'story')
            'clients_say_steps' => 'client_voices',        // ✅ fixed (was 'client')
            'how_we_work' => 'how_we_work',          // ✅ same
            'faq_steps' => 'faqs',                 // ✅ fixed (was 'faq')
            'our_promise' => 'industries',           // ✅ fixed (was 'item')
            'cta_steps' => 'call_to_action',       // ✅ fixed (was 'cta')
            'guarantee_steps' => 'guarantee',            // ✅ same
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
        $data['services'] = Service::with('technologies')->orderBy('id', 'asc')->get();
        $data['allTechnologies'] = Technology::all();
        $data['allPortfolios'] = Portfolio::all();

        return view('admin.subservices.edit', $data);
    }

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
            'technologies' => 'nullable|array',
            'technologies.*' => 'exists:technologies,id',
            'portfolios' => 'nullable|array',
            'portfolios.*' => 'exists:portfolios,id',
        ]);

        // image upload, fit and store inside public folder
        if ($request->hasFile('image')) {

            $file_path = public_path('uploads/'.$this->path.'/'.$subservice->image_path);
            if (File::isFile($file_path)) {
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

        $dom = new \DomDocument;
        libxml_use_internal_errors(true);
        $dom->encoding = 'utf-8';
        $dom->loadHtml(utf8_decode($content), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $images = $dom->getElementsByTagName('img');
        foreach ($images as $img) {
            $src = $img->getAttribute('src');

            if (preg_match('/data:image/', $src)) {
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
        $subservice->sub_service_icon = $request->sub_service_icon;
        $subservice->description = $dom->saveHTML();
        $subservice->image_path = $fileNameToStore;
        /**
         * ==========================
         * 🔹 SECTION HEADINGS (title/subtitle shown on the public page)
         * ==========================
         */
        $subservice->client_voices_section_title = $request->client_voices_section_title;
        $subservice->industries_section_title = $request->industries_section_title;
        $subservice->guarantee_section_title = $request->guarantee_section_title;
        $subservice->guarantee_section_subtitle = $request->guarantee_section_subtitle;
        $subservice->deliverables_section_title = $request->deliverables_section_title;
        $subservice->deliverables_section_subtitle = $request->deliverables_section_subtitle;
        $subservice->why_msn_softtech_section_title = $request->why_msn_softtech_section_title;
        $subservice->stack_section_title = $request->stack_section_title;
        $subservice->core_features_section_title = $request->core_features_section_title;
        $subservice->core_features_section_subtitle = $request->core_features_section_subtitle;
        $subservice->how_we_work_section_title = $request->how_we_work_section_title;
        $subservice->how_we_work_section_subtitle = $request->how_we_work_section_subtitle;
        $subservice->whats_included_section_title = $request->whats_included_section_title;
        $subservice->whats_included_section_subtitle = $request->whats_included_section_subtitle;
        $subservice->who_is_this_for_section_title = $request->who_is_this_for_section_title;
        $subservice->who_is_this_for_section_subtitle = $request->who_is_this_for_section_subtitle;
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
                    $bannerImageName = $filename.'_'.time().'.webp';

                    $path = public_path('uploads/banner/');
                    if (! File::exists($path)) {
                        File::makeDirectory($path, 0777, true, true);
                    }

                    // Delete old image if exists
                    if (! empty($oldBannerSteps[$index]['banner_image'])) {
                        $oldPath = $path.$oldBannerSteps[$index]['banner_image'];
                        if (File::exists($oldPath)) {
                            File::delete($oldPath);
                        }
                    }

                    // Save new image
                    Image::make($file->getRealPath())
                        ->encode('webp', 90)
                        ->save($path.$bannerImageName);
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
        if ($request->has('core_features')) {
            foreach ($request->core_features as $feature) {
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
         * 🔹 DELIVERABLES (WORK PROCESS) SECTION
         * ==========================
         */
        $processSteps = [];
        if ($request->has('deliverables')) {
            foreach ($request->deliverables as $step) {
                $processSteps[] = [
                    'title' => $step['title'] ?? '',
                    'bottom_text' => $step['bottom_text'] ?? '',
                ];
            }
        }
        $subservice->process_steps = json_encode($processSteps);

        /**
         * ==========================
         * 🔹 WHO IS THIS FOR SECTION
         * ==========================
         */
        $whyWeSteps = [];
        if ($request->has('who_is_this_for')) {
            foreach ($request->who_is_this_for as $why) {
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
         * 🔹 HERO BADGES SECTION
         * ==========================
         */
        $industriesSteps = [];
        if ($request->has('hero_badges')) {
            foreach ($request->hero_badges as $industry) {
                $industriesSteps[] = [
                    'icon_class' => $industry['icon_class'] ?? '',
                    'title' => $industry['title'] ?? '',
                    'description' => $industry['description'] ?? '',
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
        if ($request->has('achievements')) {
            foreach ($request->achievements as $achievement) {
                $achievementsSteps[] = [
                    'count_number' => $achievement['count_number'] ?? '',
                    'title' => $achievement['title'] ?? '',
                ];
            }
        }
        $subservice->achievements_steps = json_encode($achievementsSteps);

        /**
         * ==========================
         * 🔹 WHAT'S INCLUDED (SUCCESS STORIES) SECTION
         * ==========================
         */
        $successStoriesSteps = [];
        if ($request->has('whats_included')) {
            foreach ($request->whats_included as $story) {
                $successStoriesSteps[] = [
                    'title' => $story['title'] ?? '',
                    'icon' => $story['icon'] ?? '',
                    'bottom_text' => $story['bottom_text'] ?? '',
                ];
            }
        }
        $subservice->success_stories_steps = json_encode($successStoriesSteps);

        /**
         * ==========================
         * 🔹 CLIENT VOICES SECTION
         * ==========================
         */
        $clientsSaySteps = [];
        if ($request->has('client_voices')) {
            foreach ($request->client_voices as $client) {
                $clientsSaySteps[] = [
                    'title' => $client['title'] ?? '',
                    'meassage' => $client['meassage'] ?? '',
                    'designation' => $client['designation'] ?? '',
                    'rating' => $client['rating'] ?? '',
                ];
            }
        }
        $subservice->clients_say_steps = json_encode($clientsSaySteps);

        /**
         * ==========================
         * 🔹 HOW WE WORK SECTION
         * ==========================
         */
        $howWeWork = [];
        if ($request->has('how_we_work')) {
            foreach ($request->how_we_work as $work) {
                $howWeWork[] = [
                    'title' => $work['title'] ?? '',
                    'designation' => $work['designation'] ?? '',
                    'meassage' => $work['meassage'] ?? '',
                    'icon' => $work['icon'] ?? '',
                ];
            }
        }
        $subservice->how_we_work = json_encode($howWeWork);

        /**
         * ==========================
         * 🔹 OUR GUARANTEE SECTION (prefix অপরিবর্তিত)
         * ==========================
         */
        $guaranteeSteps = [];
        if ($request->has('guarantee')) {
            foreach ($request->guarantee as $guarantee) {
                $guaranteeSteps[] = [
                    'icon' => $guarantee['icon'] ?? '',
                    'title' => $guarantee['title'] ?? '',
                    'description' => $guarantee['description'] ?? '',
                ];
            }
        }
        $subservice->guarantee_steps = json_encode($guaranteeSteps);

        /**
         * ==========================
         * 🔹 FAQ SECTION
         * ==========================
         */
        $faqSteps = [];
        if ($request->has('faqs')) {
            foreach ($request->faqs as $faq) {
                $faqSteps[] = [
                    'question' => $faq['question'] ?? '',
                    'answer' => $faq['answer'] ?? '',
                ];
            }
        }
        $subservice->faq_steps = json_encode($faqSteps);

        /**
         * ==========================
         * 🔹 INDUSTRIES (OUR PROMISE) SECTION
         * ==========================
         */
        $promiseSteps = [];
        if ($request->has('industries')) {
            foreach ($request->industries as $promise) {
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
        if ($request->has('call_to_action')) {
            foreach ($request->call_to_action as $cta) {
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
        $image_path = public_path('uploads/'.$this->path.'/'.$subservice->image_path);
        if (File::isFile($image_path)) {
            File::delete($image_path);
        }

        $subservice->delete();

        Toastr::success(__('dashboard.deleted_successfully'), __('dashboard.success'));

        return redirect()->back();
    }
}
