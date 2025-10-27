<?php

namespace App\Http\Controllers\Admin;

use File;
use Image;
use Toastr;
use App\Models\Service;
use App\Models\Technology;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class TechnologyController extends Controller
{
    public function __construct()
    {
        // Module Data
        // $this->title = trans_choice('dashboard.technology', 1);
        // $this->route = 'admin.service';
        // $this->view = 'admin.service';
        // $this->path = 'service';
        // Module Data
        $this->title = trans_choice('dashboard.technology', 1);
        $this->route = 'admin.technology';
        $this->view = 'admin.technology';
        $this->path = 'technology';
    }
    private function removeBackground($file, $path, $filename)
    {
        $response = Http::withHeaders([
            'X-Api-Key' => env('REMOVEBG_API_KEY'),
        ])->attach(
                'image_file',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            )->post('https://api.remove.bg/v1.0/removebg', [
                    'size' => 'auto',
                ]);

        if ($response->successful()) {
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            $fileNameToStore = $filename . '_' . time() . '.png';
            file_put_contents($path . $fileNameToStore, $response->body());

            return $fileNameToStore;
        }

        return null;
    }

    public function index()
    {
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = $this->path;

        $data['rows'] = Technology::with('service')->orderBy('id', 'asc')->get();

        return view($this->view . '.index', $data);
    }


    public function create()
    {
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = $this->path;

        $data['services'] = Service::orderBy('id', 'asc')->get();

        return view($this->view . '.create', $data);
    }


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
            'logo' => 'nullable|image',
        ]);

        // 🟢 Process Main Image
        $fileNameToStore = null;
        if ($request->hasFile('image')) {
            $filename = pathinfo($request->file('image')->getClientOriginalName(), PATHINFO_FILENAME);
            $removeBg = $request->input('remove_bg_image') === 'yes';
            $fileNameToStore = $this->processImage($request->file('image'), $this->path, $filename, $removeBg);
        } else {
            $fileNameToStore = 'noimage.jpg'; // Default
        }

        // 🟢 Process Logo
        $logoFileNameToStore = null;
        if ($request->hasFile('logo')) {
            $logoFilename = pathinfo($request->file('logo')->getClientOriginalName(), PATHINFO_FILENAME);
            $removeBgLogo = $request->input('remove_bg_logo') === 'yes';
            $logoFileNameToStore = $this->processImage($request->file('logo'), $this->path, $logoFilename, $removeBgLogo);
        }

        // 🟢 Process Description Images (from WYSIWYG editor)
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
                    ->resize(780, 400, function ($constraint) {
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

        // 🟢 Insert Data
        $service = new Technology;
        $service->title = $request->title;
        $service->service_id = $request->service_id;
        $service->keywords = $request->keywords;
        $service->price = $request->price;
        $service->toggle_title = $request->toggle_title;
        $service->toggle_sub_title = $request->toggle_sub_title;
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

        // 🟢 Tech Steps
        $techSteps = [];
        foreach ($request->tech as $index => $process) {
            $techSteps[] = [
                'tech_title' => $process['tech_title'],
                'tech_description' => $process['tech_description'],
            ];
        }
        $service->tech_steps = json_encode($techSteps);

        // 🟢 Expertise Steps
        $expertiseSteps = [];
        foreach ($request->expertise as $index => $process) {
            $expertiseImageName = null;

            if ($request->hasFile("expertise.$index.expertise_image")) {
                $file = $request->file("expertise.$index.expertise_image");
                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $removeBgExp = ($process['remove_bg'] ?? 'no') === 'yes';
                $expertiseImageName = $this->processImage($file, $this->path, $filename, $removeBgExp);
            }

            $expertiseSteps[] = [
                'expertise_url' => $process['expertise_url'] ?? null,
                'expertise_image' => $expertiseImageName,
            ];
        }
        $service->expertise_steps = json_encode($expertiseSteps);

        $service->save();

        Toastr::success(__('dashboard.created_successfully'), __('dashboard.success'));
        return redirect()->route('admin.technologies.index');
    }

    /**
     * 🟢 Image Processor Helper (Normal Upload + Background Remove Option)
     */
    private function processImage($file, $path, $filename, $removeBg = false)
    {
        $fileNameToStore = $filename . '_' . time() . '.webp';
        $fullPath = public_path('uploads/' . $path . '/');

        if (!File::exists($fullPath)) {
            File::makeDirectory($fullPath, 0777, true, true);
        }

        if ($removeBg) {
            // remove.bg API
            $response = Http::withHeaders([
                'X-Api-Key' => env('REMOVEBG_API_KEY'),
            ])->attach(
                    'image_file',
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                )->post('https://api.remove.bg/v1.0/removebg', [
                        'size' => 'auto',
                    ]);

            if ($response->successful()) {
                $fileNameToStore = $filename . '_' . time() . '.png'; // keep transparency
                file_put_contents($fullPath . $fileNameToStore, $response->body());
                return $fileNameToStore;
            }
        }

        // fallback: Normal resize + webp
        Image::make($file->getRealPath())
            ->resize(780, 400, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode('webp', 90)
            ->save($fullPath . $fileNameToStore);

        return $fileNameToStore;
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
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = $this->path;

        $data['subservice'] = $technology;
        $data['services'] = Service::orderBy('id', 'asc')->get();
        return view($this->view . '.edit', $data);
    }


    public function update(Request $request, Technology $technology)
    {
        // Field Validation
        $request->validate([
            'title' => 'required|max:191|unique:services,title,' . $technology->id,
            'short_title' => 'required|max:30|unique:services,short_title,' . $technology->id,
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
            'logo' => 'nullable|image',
        ]);

        $path = public_path('uploads/' . $this->path . '/');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        // ----- CTA Image -----
        if ($request->hasFile('image')) {
            if (!empty($technology->image_path) && File::exists($path . $technology->image_path)) {
                File::delete($path . $technology->image_path);
            }

            $file = $request->file('image');
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            $fileNameToStore = $request->input('image_remove_bg') === 'yes'
                ? $this->removeBackground($file, $path, $filename)
                : null;

            if (!$fileNameToStore) {
                $fileNameToStore = $filename . '_' . time() . '.webp';
                Image::make($file->getRealPath())
                    ->fit(780, 400, fn($constraint) => $constraint->upsize())
                    ->encode('webp', 90)
                    ->save($path . $fileNameToStore);
            }
        } else {
            $fileNameToStore = $technology->image_path;
        }

        // ----- Logo -----
        if ($request->hasFile('logo')) {
            if (!empty($technology->logo_path) && File::exists($path . $technology->logo_path)) {
                File::delete($path . $technology->logo_path);
            }

            $file = $request->file('logo');
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            $logoFileNameToStore = $request->input('logo_remove_bg') === 'yes'
                ? $this->removeBackground($file, $path, $filename)
                : null;

            if (!$logoFileNameToStore) {
                $logoFileNameToStore = $filename . '_' . time() . '.webp';
                Image::make($file->getRealPath())
                    ->resize(200, null, fn($constraint) => $constraint->aspectRatio()->upsize())
                    ->encode('webp', 90)
                    ->save($path . $logoFileNameToStore);
            }
        } else {
            $logoFileNameToStore = $technology->logo_path;
        }

        // ----- Description Images (CKEditor) -----
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
                $mediaPath = public_path('uploads/media/');
                if (!File::exists($mediaPath))
                    File::makeDirectory($mediaPath, 0777, true, true);

                $filepath = "/uploads/media/$filename.webp";
                Image::make($src)
                    ->resize(780, 400, fn($constraint) => $constraint->aspectRatio()->upsize())
                    ->encode('webp', 90)
                    ->save(public_path($filepath));

                $img->setAttribute('src', asset($filepath));
            }
        }

        // ----- Update Technology -----
        $technology->update([
            'title' => $request->title,
            'service_id' => $request->service_id,
            'keywords' => $request->keywords,
            'price' => $request->price,
            'toggle_title' => $request->toggle_title,
            'toggle_sub_title' => $request->toggle_sub_title,
            'starting_price' => $request->starting_price,
            'priceCurrency' => $request->priceCurrency,
            'average_rating' => $request->average_rating,
            'review_count' => $request->review_count,
            'short_title' => $request->short_title,
            'meta_title' => $request->meta_title,
            'slug' => Str::slug(strtolower($request->slug), '-'),
            'short_desc' => $request->short_desc,
            'description' => $dom->saveHTML(),
            'image_path' => $fileNameToStore,
            'logo_path' => $logoFileNameToStore,
            'status' => $request->status,
            'manu' => $request->manu,
        ]);

        // ----- Tech Steps -----
        $techSteps = [];
        foreach ($request->tech as $process) {
            $techSteps[] = [
                'tech_title' => $process['tech_title'],
                'tech_description' => $process['tech_description'],
            ];
        }
        $technology->tech_steps = json_encode($techSteps);

        // ----- Expertise Steps -----
        $oldExpertiseSteps = json_decode($technology->expertise_steps, true) ?? [];
        $expertiseSteps = [];

        foreach ($request->expertise as $index => $process) {
            $expertiseImageName = $oldExpertiseSteps[$index]['expertise_image'] ?? null;

            if ($request->hasFile("expertise.$index.expertise_image")) {

                if (!empty($expertiseImageName) && File::exists($path . $expertiseImageName)) {
                    File::delete($path . $expertiseImageName);
                }

                $file = $request->file("expertise.$index.expertise_image");
                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                $expertiseImageName = ($process['remove_bg'] ?? 'no') === 'yes'
                    ? $this->removeBackground($file, $path, $filename)
                    : null;

                if (!$expertiseImageName) {
                    $expertiseImageName = $filename . '_' . time() . '.webp';

                    $image = Image::make($file->getRealPath());

                    if ($image) {
                        $image->resize(756, 419, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        })->encode('webp', 90)
                            ->save($path . $expertiseImageName);
                    } else {
                        // Optional: fallback or log error
                        throw new \Exception('Failed to create image from uploaded file.');
                    }
                }
            }


            $expertiseSteps[] = [
                'expertise_url' => $process['expertise_url'],
                'expertise_image' => $expertiseImageName,
            ];
        }

        $technology->expertise_steps = json_encode($expertiseSteps);
        $technology->save();

        Toastr::success(__('dashboard.updated_successfully'), __('dashboard.success'));
        return redirect()->route('admin.technologies.index');
    }


    public function destroy(Technology $technology)
    {
        // Delete Data
        $image_path = public_path('uploads/' . $this->path . '/' . $technology->image_path);
        if (File::isFile($image_path)) {
            File::delete($image_path);
        }

        $technology->delete();

        Toastr::success(__('dashboard.deleted_successfully'), __('dashboard.success'));

        return redirect()->back();
    }
}
