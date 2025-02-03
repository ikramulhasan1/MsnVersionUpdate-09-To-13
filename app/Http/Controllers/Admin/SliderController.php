<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Slider;
use Toastr;
use Image;
use File;

class SliderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Module Data
        $this->title = trans_choice('dashboard.slider', 1);
        $this->route = 'admin.slider';
        $this->view = 'admin.slider';
        $this->path = 'slider';
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
        
        $data['rows'] = Slider::orderBy('id', 'desc')->get();

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
            'title' => 'required|max:191|unique:sliders,title',
            'image' => 'required|image',
        ]);


        // image upload, fit and store inside public folder 
        if($request->hasFile('image')){
            //Upload New Image
            $filenameWithExt = $request->file('image')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME); 
            $extension = $request->file('image')->getClientOriginalExtension();
            $fileNameToStore = $filename.'_'.time().'.'.$extension;

            //Crete Folder Location
            $path = public_path('uploads/'.$this->path.'/');
            if (! File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            //Resize And Crop as Fit image here (1920 width, 800 height)
            $thumbnailpath = $path.$fileNameToStore;
            $img = Image::make($request->file('image')->getRealPath())->fit(1920, 800, function ($constraint) { $constraint->upsize(); })->save($thumbnailpath);
        }
        else{
            $fileNameToStore = 'noimage.jpg'; // if no image selected this will be the default image
        }


        // Insert Data
        $slider = new Slider;
        $slider->title = $request->title;
        $slider->slug = Str::slug($request->title, '-');
        $slider->description = $request->description;
        $slider->image_path = $fileNameToStore;
        $slider->link = $request->link;
        $slider->save();


        Toastr::success(__('dashboard.created_successfully'), __('dashboard.success'));

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Slider $slider)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Slider $slider)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Slider $slider)
    {
        // Field Validation
        $request->validate([
            'title' => 'required|max:191|unique:sliders,title,'.$slider->id,
            'image' => 'nullable|image',
        ]);


        // image upload, fit and store inside public folder 
        if($request->hasFile('image')){

            $file_path = public_path('uploads/'.$this->path.'/'.$slider->image_path);
            if(File::isFile($file_path)){
                File::delete($file_path);
            }

            //Upload New Image
            $filenameWithExt = $request->file('image')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME); 
            $extension = $request->file('image')->getClientOriginalExtension();
            $fileNameToStore = $filename.'_'.time().'.'.$extension;

            //Crete Folder Location
            $path = public_path('uploads/'.$this->path.'/');
            if (! File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            //Resize And Crop as Fit image here (1920 width, 800 height)
            $thumbnailpath = $path.$fileNameToStore;
            $img = Image::make($request->file('image')->getRealPath())->fit(1920, 800, function ($constraint) { $constraint->upsize(); })->save($thumbnailpath);
        }
        else{

            $fileNameToStore = $slider->image_path; 
        }


        // Update Data
        $slider->title = $request->title;
        $slider->slug = Str::slug($request->title, '-');
        $slider->description = $request->description;
        $slider->image_path = $fileNameToStore;
        $slider->link = $request->link;
        $slider->status = $request->status;
        $slider->save();


        Toastr::success(__('dashboard.updated_successfully'), __('dashboard.success'));

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Slider $slider)
    {
        // Delete Data
        $image_path = public_path('uploads/'.$this->path.'/'.$slider->image_path);
        if(File::isFile($image_path)){
            File::delete($image_path);
        }

        $slider->delete();

        Toastr::success(__('dashboard.deleted_successfully'), __('dashboard.success'));

        return redirect()->back();
    }
}
