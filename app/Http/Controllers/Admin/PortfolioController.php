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
        // Field Validation
        $request->validate([
            'title' => 'required|max:191|unique:portfolios,title',
            'categories' => 'required',
            'description' => 'required',
            'image' => 'required|image',
            'video_id' => 'nullable|max:100',
            'technologies' => 'nullable|array',
            'technologies.*' => 'exists:technologies,id',
        ]);


        // image upload, fit and store inside public folder 
        if ($request->hasFile('image')) {
            //Upload New Image
            $filenameWithExt = $request->file('image')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('image')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            //Crete Folder Location
            $path = public_path('uploads/' . $this->path . '/');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            //Resize And Crop as Fit image here (800 width, 500 height)
            $thumbnailpath = $path . $fileNameToStore;
            $img = Image::make($request->file('image')->getRealPath())->fit(800, 500, function ($constraint) {
                $constraint->upsize();
            })->save($thumbnailpath);
        } else {
            $fileNameToStore = 'noimage.jpg'; // if no image selected this will be the default image
        }


        // Get content with media file
        $content = $request->input('description');

        $dom = new \DomDocument();
        libxml_use_internal_errors(true);
        $dom->encoding = 'utf-8';
        $dom->loadHtml(utf8_decode($content), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $images = $dom->getElementsByTagName('img');
        // foreach <img> in the submited content
        foreach ($images as $img) {
            $src = $img->getAttribute('src');

            // if the img source is 'data-url'
            if (preg_match('/data:image/', $src)) {
                // get the mimetype
                preg_match('/data:image\/(?<mime>.*?)\;/', $src, $groups);
                $mimetype = $groups['mime'];
                // Generating a random filename
                $filename = uniqid() . '_' . time();

                //Crete Folder Location
                $path = public_path('uploads/media/');
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0777, true, true);
                }

                $filepath = "/uploads/media/$filename.$mimetype";
                // @see http://image.intervention.io/api/
                $image = Image::make($src)
                    // resize if required
                    //->resize(500, null) 
                    ->resize(800, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->encode($mimetype, 100)  // encode file to the specified mimetype
                    ->save(public_path($filepath));
                $new_src = asset($filepath);
                $img->removeAttribute('src');
                $img->setAttribute('src', $new_src);
            } // <!--endif
        } // <!-


        // Insert Data
        $portfolio = new Portfolio;
        $portfolio->title = $request->title;
        $portfolio->slug = Str::slug($request->title, '-');
        $portfolio->description = $request->description;
        // $portfolio->description = $dom->saveHTML();
        $portfolio->image_path = $fileNameToStore;
        $portfolio->video_id = $request->video_id;
        $portfolio->sub_title = $request->sub_title;
        $portfolio->client = $request->client;
        $portfolio->date = $request->date;
        $portfolio->link = $request->link;
        $portfolio->link2 = $request->link2;
        $portfolio->link3 = $request->link3;

        $screenshotSteps = [];

        // Decode old banner steps (so we can access previous image paths)
        $oldBannerSteps = json_decode($portfolio->screenshot ?? '[]', true);

        if ($request->has('screenshot')) {
            foreach ($request->screenshot as $index => $banner) {
                $bannerImageName = $banner['screenshot_image_old'] ?? ($oldBannerSteps[$index]['screenshot_image'] ?? null);

                // Check if new file uploaded
                if ($request->hasFile("screenshot.$index.screenshot_image")) {
                    $file = $request->file("screenshot.$index.screenshot_image");
                    $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $bannerImageName = $filename . '_' . time() . '.webp';

                    $path = public_path('uploads/screenshot/');
                    if (!File::exists($path)) {
                        File::makeDirectory($path, 0777, true, true);
                    }

                    // Delete old image if exists
                    if (!empty($oldBannerSteps[$index]['screenshot_image'])) {
                        $oldPath = $path . $oldBannerSteps[$index]['screenshot_image'];
                        if (File::exists($oldPath)) {
                            File::delete($oldPath);
                        }
                    }

                    // Save new image
                    Image::make($file->getRealPath())
                        ->encode('webp', 90)
                        ->save($path . $bannerImageName);
                }

                $screenshotSteps[] = [
                    'screenshot_image' => $bannerImageName ?? '',
                ];
            }
        }

        // Store as JSON or let Eloquent cast handle it
        $portfolio->screenshot = json_encode($screenshotSteps);
        $portfolio->technologies()->sync($request->technologies ?? []);
        $portfolio->save();

        // Attach
        $portfolio->categories()->attach($request->categories);


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
        // Field Validation
        $request->validate([
            'title' => 'required|max:191|unique:portfolios,title,' . $portfolio->id,
            'categories' => 'required',
            'description' => 'required',
            'image' => 'nullable|image',
            'video_id' => 'nullable|max:100',
            'technologies' => 'nullable|array',
            'technologies.*' => 'exists:technologies,id',
        ]);


        // image upload, fit and store inside public folder 
        if ($request->hasFile('image')) {

            $file_path = public_path('uploads/' . $this->path . '/' . $portfolio->image_path);
            if (File::isFile($file_path)) {
                File::delete($file_path);
            }

            //Upload New Image
            $filenameWithExt = $request->file('image')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('image')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            //Crete Folder Location
            $path = public_path('uploads/' . $this->path . '/');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            //Resize And Crop as Fit image here (800 width, 500 height)
            $thumbnailpath = $path . $fileNameToStore;
            $img = Image::make($request->file('image')->getRealPath())->fit(800, 500, function ($constraint) {
                $constraint->upsize();
            })->save($thumbnailpath);
        } else {

            $fileNameToStore = $portfolio->image_path;
        }


        // Get content with media file
        $content = $request->input('description');

        $dom = new \DomDocument();
        libxml_use_internal_errors(true);
        $dom->encoding = 'utf-8';
        $dom->loadHtml(utf8_decode($content), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $images = $dom->getElementsByTagName('img');
        // foreach <img> in the submited content
        foreach ($images as $img) {
            $src = $img->getAttribute('src');

            // if the img source is 'data-url'
            if (preg_match('/data:image/', $src)) {
                // get the mimetype
                preg_match('/data:image\/(?<mime>.*?)\;/', $src, $groups);
                $mimetype = $groups['mime'];
                // Generating a random filename
                $filename = uniqid() . '_' . time();

                //Crete Folder Location
                $path = public_path('uploads/media/');
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0777, true, true);
                }

                $filepath = "/uploads/media/$filename.$mimetype";
                // @see http://image.intervention.io/api/
                $image = Image::make($src)
                    // resize if required
                    //->resize(500, null) 
                    ->resize(800, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->encode($mimetype, 100)  // encode file to the specified mimetype
                    ->save(public_path($filepath));
                $new_src = asset($filepath);
                $img->removeAttribute('src');
                $img->setAttribute('src', $new_src);
            } // <!--endif
        } // <!-


        // Update Data
        $portfolio->title = $request->title;
        $portfolio->slug = Str::slug($request->title, '-');
        $portfolio->description = $request->description;
        // $portfolio->description = $dom->saveHTML();
        $portfolio->image_path = $fileNameToStore;
        $portfolio->sub_title = $request->sub_title;
        $portfolio->client = $request->client;
        $portfolio->date = $request->date;
        $portfolio->video_id = $request->video_id;
        $portfolio->link = $request->link;
        $portfolio->link2 = $request->link2;
        $portfolio->link3 = $request->link3;
        $portfolio->status = $request->status;


        $screenshotSteps = [];

        // Decode old banner steps (so we can access previous image paths)
        $oldBannerSteps = json_decode($portfolio->screenshot ?? '[]', true);

        if ($request->has('screenshot')) {
            foreach ($request->screenshot as $index => $banner) {
                $bannerImageName = $banner['screenshot_image_old'] ?? ($oldBannerSteps[$index]['screenshot_image'] ?? null);

                // Check if new file uploaded
                if ($request->hasFile("screenshot.$index.screenshot_image")) {
                    $file = $request->file("screenshot.$index.screenshot_image");
                    $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $bannerImageName = $filename . '_' . time() . '.webp';

                    $path = public_path('uploads/screenshot/');
                    if (!File::exists($path)) {
                        File::makeDirectory($path, 0777, true, true);
                    }

                    // Delete old image if exists
                    if (!empty($oldBannerSteps[$index]['screenshot_image'])) {
                        $oldPath = $path . $oldBannerSteps[$index]['screenshot_image'];
                        if (File::exists($oldPath)) {
                            File::delete($oldPath);
                        }
                    }

                    // Save new image
                    Image::make($file->getRealPath())
                        ->encode('webp', 90)
                        ->save($path . $bannerImageName);
                }

                $screenshotSteps[] = [
                    'screenshot_image' => $bannerImageName ?? '',
                ];
            }
        }

        // Store as JSON or let Eloquent cast handle it
        $portfolio->screenshot = json_encode($screenshotSteps);
        $portfolio->technologies()->sync($request->technologies ?? []);
        $portfolio->save();

        // Attach Update
        $portfolio->categories()->sync($request->categories);


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
        // Delete Data
        $image_path = public_path('uploads/' . $this->path . '/' . $portfolio->image_path);
        if (File::isFile($image_path)) {
            File::delete($image_path);
        }

        // Detach
        $portfolio->categories()->detach();
        $portfolio->delete();

        Toastr::success(__('dashboard.deleted_successfully'), __('dashboard.success'));

        return redirect()->back();
    }
}
