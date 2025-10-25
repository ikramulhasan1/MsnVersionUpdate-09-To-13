<?php

namespace App\Http\Controllers\Admin;

use File;
use Image;
use Toastr;
use App\Models\Portfolio;
use App\Models\Technology;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PortfolioCategory;
use App\Http\Controllers\Controller;

class PortfolioController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Module Data
        $this->title = trans_choice('dashboard.portfolio', 1);
        $this->route = 'admin.portfolio';
        $this->view = 'admin.portfolio';
        $this->path = 'portfolio';
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

        $data['rows'] = Portfolio::orderBy('id', 'desc')->get();

        return view($this->view . '.index', $data);
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
        $data['allTechnologies'] = Technology::all();


        $data['categories'] = PortfolioCategory::where('status', '1')->get();

        return view($this->view . '.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   public function store(Request $request)
{
    // ---------------------------
    // 1️⃣ Validation
    // ---------------------------
    $request->validate([
        'title' => 'required|max:191|unique:portfolios,title',
        'categories' => 'required',
        'description' => 'required',
        'image' => 'required|image',
        'overview_image' => 'nullable|image',
        'video_id' => 'nullable|max:100',
        'technologies' => 'nullable|array',
        'technologies.*' => 'exists:technologies,id',
    ]);

    // ---------------------------
    // 2️⃣ Upload main image
    // ---------------------------
    if ($request->hasFile('image')) {
        $filenameWithExt = $request->file('image')->getClientOriginalName();
        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
        $extension = $request->file('image')->getClientOriginalExtension();
        $fileNameToStore = $filename . '_' . time() . '.' . $extension;

        $path = public_path('uploads/' . $this->path . '/');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $thumbnailpath = $path . $fileNameToStore;
        Image::make($request->file('image')->getRealPath())
            ->fit(800, 500, function ($constraint) {
                $constraint->upsize();
            })
            ->save($thumbnailpath);
    } else {
        $fileNameToStore = 'noimage.jpg';
    }

    // ---------------------------
    // 3️⃣ Handle overview_image (single image)
    // ---------------------------
    $overviewImageName = null;
    if ($request->hasFile('overview_image')) {
        $file = $request->file('overview_image');
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $overviewImageName = $filename . '_' . time() . '.webp';

        $path = public_path('uploads/overview_image/');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        Image::make($file->getRealPath())
            ->encode('webp', 90)
            ->save($path . $overviewImageName);
    }

    // ---------------------------
    // 4️⃣ Handle screenshots (JSON)
    // ---------------------------
    $screenshotSteps = [];

    if ($request->has('screenshot')) {
        foreach ($request->screenshot as $index => $banner) {
            if ($request->hasFile("screenshot.$index.screenshot_image")) {
                $file = $request->file("screenshot.$index.screenshot_image");
                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $bannerImageName = $filename . '_' . time() . '.webp';

                $path = public_path('uploads/screenshot/');
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0777, true, true);
                }

                Image::make($file->getRealPath())
                    ->encode('webp', 90)
                    ->save($path . $bannerImageName);

                $screenshotSteps[] = ['screenshot_image' => $bannerImageName];
            }
        }
    }

    // ---------------------------
    // 5️⃣ Save Portfolio Data
    // ---------------------------
    $portfolio = new Portfolio();
    $portfolio->title = $request->title;
    $portfolio->slug = Str::slug($request->title, '-');
    $portfolio->description = $request->description;
    $portfolio->image_path = $fileNameToStore;
    $portfolio->overview_image = $overviewImageName;
    $portfolio->video_id = $request->video_id;
    $portfolio->sub_title = $request->sub_title;
    $portfolio->client = $request->client;
    $portfolio->date = $request->date;
    $portfolio->link = $request->link;
    $portfolio->link2 = $request->link2;
    $portfolio->link3 = $request->link3;
    $portfolio->screenshot = json_encode($screenshotSteps);

    $portfolio->save();

    // ---------------------------
    // 6️⃣ Attach categories & technologies
    // ---------------------------
    $portfolio->categories()->attach($request->categories);
    $portfolio->technologies()->sync($request->technologies ?? []);

    // ---------------------------
    // ✅ Success message
    // ---------------------------
    Toastr::success(__('dashboard.created_successfully'), __('dashboard.success'));
    return redirect()->route($this->route . '.index');
}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Portfolio $portfolio)
    {
        //
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = $this->path;

        $data['row'] = $portfolio;

        return view($this->view . '.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Portfolio $portfolio)
    {
        //
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = $this->path;
        $data['allTechnologies'] = Technology::all();

        $data['row'] = $portfolio;
        $data['categories'] = PortfolioCategory::where('status', '1')->get();

        return view($this->view . '.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Portfolio $portfolio)
{
    // ============================
    // Validation
    // ============================
    $request->validate([
        'title' => 'required|max:191|unique:portfolios,title,' . $portfolio->id,
        'categories' => 'required',
        'description' => 'required',
        'image' => 'nullable|image',
        'overview_image' => 'nullable|image',
        'video_id' => 'nullable|max:100',
        'technologies' => 'nullable|array',
        'technologies.*' => 'exists:technologies,id',
    ]);

    // ============================
    // Handle MAIN Image
    // ============================
    if ($request->hasFile('image')) {
        $file_path = public_path('uploads/' . $this->path . '/' . $portfolio->image_path);
        if (File::isFile($file_path)) {
            File::delete($file_path);
        }

        $file = $request->file('image');
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $fileNameToStore = $filename . '_' . time() . '.' . $extension;

        $path = public_path('uploads/' . $this->path . '/');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        Image::make($file->getRealPath())->fit(800, 500, function ($constraint) {
            $constraint->upsize();
        })->save($path . $fileNameToStore);
    } else {
        $fileNameToStore = $portfolio->image_path;
    }

    // ============================
    // Handle Description Images
    // ============================
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

            $filepath = "/uploads/media/$filename.$mimetype";
            Image::make($src)
                ->resize(800, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode($mimetype, 100)
                ->save(public_path($filepath));
            $new_src = asset($filepath);
            $img->removeAttribute('src');
            $img->setAttribute('src', $new_src);
        }
    }

    // ============================
    // Handle OVERVIEW IMAGE (single column)
    // ============================
    if ($request->hasFile('overview_image')) {
        $file = $request->file('overview_image');
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $overviewImageName = $filename . '_' . time() . '.webp';

        $path = public_path('uploads/overview_image/');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        // Delete old overview image
        if (!empty($portfolio->overview_image)) {
            $oldPath = $path . $portfolio->overview_image;
            if (File::exists($oldPath)) {
                File::delete($oldPath);
            }
        }

        // Save new overview image
        Image::make($file->getRealPath())
            ->encode('webp', 90)
            ->save($path . $overviewImageName);

        $portfolio->overview_image = $overviewImageName;
    }
    // If no new upload → keep old one

    // ============================
    // Handle SCREENSHOTS (JSON column)
    // ============================
    $screenshotSteps = [];
    $oldScreenshots = json_decode($portfolio->screenshot ?? '[]', true);

    if ($request->has('screenshot')) {
        foreach ($request->screenshot as $index => $banner) {
            $bannerImageName = $banner['screenshot_image_old'] ?? ($oldScreenshots[$index]['screenshot_image'] ?? null);

            if ($request->hasFile("screenshot.$index.screenshot_image")) {
                $file = $request->file("screenshot.$index.screenshot_image");
                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $bannerImageName = $filename . '_' . time() . '.webp';

                $path = public_path('uploads/screenshot/');
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0777, true, true);
                }

                // Delete old
                if (!empty($oldScreenshots[$index]['screenshot_image'])) {
                    $oldPath = $path . $oldScreenshots[$index]['screenshot_image'];
                    if (File::exists($oldPath)) {
                        File::delete($oldPath);
                    }
                }

                // Save new screenshot
                Image::make($file->getRealPath())
                    ->encode('webp', 90)
                    ->save($path . $bannerImageName);
            }

            $screenshotSteps[] = ['screenshot_image' => $bannerImageName ?? ''];
        }
    } else {
        $screenshotSteps = $oldScreenshots;
    }

    // ============================
    // Save Portfolio Data
    // ============================
    $portfolio->title = $request->title;
    $portfolio->slug = Str::slug($request->title, '-');
    $portfolio->description = $request->description;
    $portfolio->image_path = $fileNameToStore;
    $portfolio->sub_title = $request->sub_title;
    $portfolio->client = $request->client;
    $portfolio->date = $request->date;
    $portfolio->video_id = $request->video_id;
    $portfolio->link = $request->link;
    $portfolio->link2 = $request->link2;
    $portfolio->link3 = $request->link3;
    $portfolio->status = $request->status;
    $portfolio->screenshot = json_encode($screenshotSteps);

    $portfolio->technologies()->sync($request->technologies ?? []);
    $portfolio->categories()->sync($request->categories);
    $portfolio->save();

    Toastr::success(__('dashboard.updated_successfully'), __('dashboard.success'));
    return redirect()->back();
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Portfolio $portfolio)
{
    // ==================================================
    // Delete MAIN image
    // ==================================================
    $mainImagePath = public_path('uploads/' . $this->path . '/' . $portfolio->image_path);
    if (File::isFile($mainImagePath)) {
        File::delete($mainImagePath);
    }

    // ==================================================
    // Delete OVERVIEW image
    // ==================================================
    if (!empty($portfolio->overview_image)) {
        $overviewPath = public_path('uploads/overview_image/' . $portfolio->overview_image);
        if (File::exists($overviewPath)) {
            File::delete($overviewPath);
        }
    }

    // ==================================================
    // Delete SCREENSHOT images (stored as JSON)
    // ==================================================
    if (!empty($portfolio->screenshot)) {
        $screenshots = json_decode($portfolio->screenshot, true);
        if (is_array($screenshots)) {
            foreach ($screenshots as $item) {
                if (!empty($item['screenshot_image'])) {
                    $screenshotPath = public_path('uploads/screenshot/' . $item['screenshot_image']);
                    if (File::exists($screenshotPath)) {
                        File::delete($screenshotPath);
                    }
                }
            }
        }
    }

    // ==================================================
    // Detach relationships & Delete Portfolio
    // ==================================================
    $portfolio->categories()->detach();
    $portfolio->technologies()->detach();
    $portfolio->delete();

    // ==================================================
    // Success Notification
    // ==================================================
    Toastr::success(__('dashboard.deleted_successfully'), __('dashboard.success'));
    return redirect()->back();
}

}
