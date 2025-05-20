<?php

namespace App\Http\Controllers\Admin;

use File;
use Image;
use Toastr;
use App\Models\WorkModel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WorkModelController extends Controller
{
    public function __construct()
    {
        // Module Data
        $this->title = trans_choice('dashboard.work_model', 1);
        $this->route = 'admin.work-model';
        $this->view = 'admin.work-model';
        $this->path = 'work-model';
    }

    public function index()
    {
        //
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = $this->path;

        $data['rows'] = WorkModel::orderBy('id', 'desc')->get();

        return view($this->view.'.index', $data);
    }

    public function create()
    {
        //
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['services'] = WorkModel::all();

        // $data['categories'] = ArticleCategory::where('status', '1')->get();

        return view($this->view.'.create', $data);
    }

    public function store(Request $request)
{
   
        // Field Validation
        $request->validate([
            'title' => 'required|max:100|unique:work_models,title',
            'meta_description' => 'required|max:160',
            'meta_title' => 'required|max:60',
            'keywords' => 'required',
            'description' => 'required',
            'image' => 'nullable|image',
        ]);

        // Remove duplicate keywords but keep multi-word keywords intact
        $keywords = array_unique(array_map('trim', explode(',', $request->keywords)));

        // Check for existing keywords in other articles
        $existingKeywords = WorkModel::whereRaw("FIND_IN_SET(keywords, ?) > 0", [implode(',', $keywords)])->exists();
        if ($existingKeywords) {
            return back()->withErrors(['keywords' => 'Some keywords already exist. Please use unique tags.']);
        }


        // Image upload, fit and store inside public folder 
        if ($request->hasFile('image')) {
            // Upload New Image
            $filenameWithExt = $request->file('image')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME); 
            $extension = $request->file('image')->getClientOriginalExtension();
            $fileNameToStore = $filename.'_'.time().'.webp'; // Save as WebP

            // Create Folder Location
            $path = public_path('uploads/'.$this->path.'/');
            if (! File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            // Resize and Convert to WebP (500 width, 280 height)
            $thumbnailpath = $path.$fileNameToStore;
            $img = Image::make($request->file('image')->getRealPath())
                        ->fit(500, 280, function ($constraint) { 
                            $constraint->upsize(); 
                        })
                        ->encode('webp', 90) // Convert to WebP format with 90 quality
                        ->save($thumbnailpath);
        } else {
            $fileNameToStore = 'noimage.jpg'; // if no image selected, this will be the default image
        }

        // Get content with media file
        // $content = $request->input('description');
        
        // $dom = new \DomDocument();
        // libxml_use_internal_errors(true);
        // $dom->encoding = 'utf-8';
        // $dom->loadHtml(utf8_decode($content), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);    
        // $images = $dom->getElementsByTagName('img');

        // foreach <img> in the submitted content
        // foreach ($images as $img) {
        //     $src = $img->getAttribute('src');
            
        //     // if the img source is 'data-url'
        //     if (preg_match('/data:image/', $src)) {                
        //         // get the mimetype
        //         preg_match('/data:image\/(?<mime>.*?)\;/', $src, $groups);
        //         $mimetype = $groups['mime'];                
        //         // Generating a random filename
        //         $filename = uniqid().'_'.time();

        //         // Create Folder Location
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
        //             ->encode('webp', 90)  // Convert to WebP format
        //             ->save(public_path($filepath));                

        //         $new_src = asset($filepath);
        //         $img->removeAttribute('src');
        //         $img->setAttribute('src', $new_src);
        //     } 
        // } 

        // $cleanedContent = $this->cleanContent($request->service_desc);
        // Insert Data
        $workmodel = new WorkModel;
        $workmodel->title = $request->title;
        $workmodel->slug = Str::slug($request->title, '-');
        // $workmodel->short_title = $request->short_title;
        $workmodel->meta_title = $request->meta_title;
        $workmodel->meta_description = $request->meta_description;
        $workmodel->keywords = implode(',', $keywords); // Save as comma-separated values
        // $workmodel->category_id = $request->category;
        // $workmodel->service_id = $request->service_id;
        // $workmodel->placeholder = $request->placeholder;
        // $workmodel->service_title = $request->service_title;
        $workmodel->short_description = $request->short_description;
        // $workmodel->description = $dom->saveHTML();
        $workmodel->description = $request->description;
        $workmodel->image = $fileNameToStore;
        // $workmodel->video_id = $request->video_id;
        $workmodel->save();

        Toastr::success(__('dashboard.created_successfully'), __('dashboard.success'));

        return redirect()->route($this->route.'.index');
    }


    public function show(WorkModel $work_model)
    {
        //
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = $this->path;

        $data['row'] = $work_model;

        return view($this->view.'.show', $data);
    }

    public function edit(WorkModel $work_model)
    {
        //
        $data['title'] = $this->title;
        $data['route'] = $this->route;
        $data['view'] = $this->view;
        $data['path'] = $this->path;

        $data['row'] = $work_model;
        $data['services'] = WorkModel::all();
        // $data['categories'] = ArticleCategory::where('status', '1')->get();

        return view($this->view.'.edit', $data);
    }


    private function cleanContent($content) {
        // Remove empty tags
        return preg_replace('/<(\w+)[^>]*>(&nbsp;|\s)*<\/\1>/i', '', $content);
    }
    
    // Example usage:
    
    public function update(Request $request, WorkModel $work_model)
    {
        // Field Validation
        $request->validate([
            'title' => 'required|max:100|unique:work_models,title,'.$work_model->id,
            // 'short_title' => 'required|max:50',
            'meta_description' => 'required|max:160',
            'meta_title' => 'required|max:60',
            'keywords' => 'required',
            // 'service_id' => 'nullable|exists:services,id',
            // 'category' => 'required',
            'description' => 'required',
            // 'meta_desc' => 'required',
            'image' => 'nullable|image',
            // 'video_id' => 'nullable|max:100',
        ]);
    

        $keywords = array_unique(array_map('trim', explode(',', $request->keywords)));

        // Check for existing keywords in other articles (excluding the current article)
        $existingKeywords = WorkModel::where('id', '!=', $work_model->id)
                                   ->whereRaw("FIND_IN_SET(keywords, ?) > 0", [implode(',', $keywords)])
                                   ->exists();
    
        if ($existingKeywords) {
            return back()->withErrors(['keywords' => 'Some keywords already exist. Please use unique tags.']);
        }

        // Image upload, fit and store inside public folder 
        if ($request->hasFile('image')) {
    
            // Delete the old image
            $file_path = public_path('uploads/'.$this->path.'/'.$work_model->image_path);
            if (File::isFile($file_path)) {
                File::delete($file_path);
            }
    
            // Upload New Image
            $filenameWithExt = $request->file('image')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('image')->getClientOriginalExtension();
            $fileNameToStore = $filename.'_'.time().'.webp'; // Save as WebP
    
            // Create Folder Location
            $path = public_path('uploads/'.$this->path.'/');
            if (! File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }
    
            // Resize and Convert to WebP (500 width, 280 height)
            $thumbnailpath = $path.$fileNameToStore;
            $img = Image::make($request->file('image')->getRealPath())
                        ->fit(500, 280, function ($constraint) { 
                            $constraint->upsize(); 
                        })
                        ->encode('webp', 90) // Convert to WebP format with 90 quality
                        ->save($thumbnailpath);
        } else {
            // If no image is uploaded, retain the old image path
            $fileNameToStore = $work_model->image_path; 
        }
    
        // Get content with media file
        // $content = $request->input('description');
        
        // $dom = new \DomDocument();
        // libxml_use_internal_errors(true);
        // $dom->encoding = 'utf-8';
        // $dom->loadHtml(utf8_decode($content), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);    
        // $images = $dom->getElementsByTagName('img');
    
        // foreach <img> in the submitted content
        // foreach ($images as $img) {
        //     $src = $img->getAttribute('src');
            
        //     // if the img source is 'data-url'
        //     if (preg_match('/data:image/', $src)) {                
        //         // get the mimetype
        //         preg_match('/data:image\/(?<mime>.*?)\;/', $src, $groups);
        //         $mimetype = $groups['mime'];                
        //         // Generating a random filename
        //         $filename = uniqid().'_'.time();
    
        //         // Create Folder Location
        //         $path = public_path('uploads/media/');
        //         if (! File::exists($path)) {
        //             File::makeDirectory($path, 0777, true, true);
        //         }
    
        //         $filepath = "/uploads/media/$filename.$mimetype";    
        //         // @see http://image.intervention.io/api/
        //         $image = Image::make($src)
        //             ->resize(800, null, function ($constraint) {
        //                 $constraint->aspectRatio();
        //                 $constraint->upsize();
        //             })
        //             ->encode('webp', 90)  // Convert to WebP format
        //             ->save(public_path($filepath));                
    
        //         $new_src = asset($filepath);
        //         $img->removeAttribute('src');
        //         $img->setAttribute('src', $new_src);
        //     } // <!--endif
        // } // <!--foreach
        // $cleanedContent = $this->cleanContent($request->service_desc);
        // Update Data
        $work_model->title = $request->title;
        // $work_model->short_title = $request->short_title;
        // $work_model->service_id = $request->service_id;
        // $work_model->placeholder = $request->placeholder;
        $work_model->keywords = implode(',', $keywords); // Save as comma-separated values
        $work_model->slug = Str::slug($request->title, '-');
        // $work_model->category_id = $request->category;
        $work_model->short_description = $request->short_description;
        $work_model->description = $request->description;
        $work_model->image = $fileNameToStore;
        // $work_model->video_id = $request->video_id;
        $work_model->meta_description = $request->meta_description;
        // $work_model->meta_desc = $request->meta_desc;
        $work_model->meta_title = $request->meta_title;
        // $work_model->service_desc = trim($cleanedContent);
        // $work_model->service_title = $request->service_title;
        $work_model->status = $request->status;
        $work_model->save();
    
        Toastr::success(__('dashboard.updated_successfully'), __('dashboard.success'));
    
        return redirect()->back();
    }
    

    public function destroy(WorkModel $work_model)
    {
        // Delete Data
        $image_path = public_path('uploads/'.$this->path.'/'.$work_model->image);
        if(File::isFile($image_path)){
            File::delete($image_path);
        }

        $work_model->delete();

        Toastr::success(__('dashboard.deleted_successfully'), __('dashboard.success'));

        return redirect()->back();
    }
}