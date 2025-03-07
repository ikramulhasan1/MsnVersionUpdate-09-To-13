<?php

namespace App\Http\Controllers\Admin;

use App\Models\RedirectUrl;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Str;
// use App\Models\FaqCategory;
// use App\Models\Faq;
use Toastr;
// use Image;
// use File;



class RedirectUrlController extends Controller
{
  
    public function index()
    {
        $data['rows'] = RedirectUrl::orderBy('id', 'asc')->get();

        return view('admin.redirect.index', $data);
    }

    public function create()
    {
        
        return view('admin.redirect.create');
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'submitted_url' => 'required|url|unique:redirect_urls,submitted_url',
            'redirect_to' => 'required|url'
        ]);
    
        RedirectUrl::create($request->all());
    
        return redirect()->route('admin.redirects.index')->with('success', 'Redirect rule created successfully!');
    }

    public function redirect(Request $request)
    {
        $submittedUrl = $request->input('url'); // Get URL from request

        // Find if this URL exists in the database
        $redirect = RedirectUrl::where('submitted_url', $submittedUrl)->first();

        if ($redirect) {
            return redirect()->away($redirect->redirect_to); // Redirect to new URL
        }

        return redirect()->route('redirects.index')->with('error', 'No redirect found for this URL.');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    
    public function update(Request $request, $id)
    {
        //
    }

    
    public function destroy($id)
    {
        //
    }
}
