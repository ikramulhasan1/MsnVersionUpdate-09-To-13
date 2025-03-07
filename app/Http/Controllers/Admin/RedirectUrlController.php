<?php

namespace App\Http\Controllers\Admin;

use Log;
use Toastr;
use App\Models\RedirectUrl;

use Illuminate\Support\Str;
// use App\Models\FaqCategory;
// use App\Models\Faq;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
            'redirect_to' => 'required|url',
        ]);

        RedirectUrl::create([
            'submitted_url' => $request->submitted_url,
            'redirect_to' => $request->redirect_to
        ]);

        return redirect()->route('admin.redirects.index')->with('success', 'Redirect added successfully!');
    
    }

    public function redirect(Request $request)
    {
      
        $submittedUrl = $request->input('url'); // Get the submitted URL
        // Debugging Log
        Log::info('Redirecting URL: ' . $submittedUrl);

        // Check if the submitted URL exists in the database
        $redirect = RedirectUrl::where('submitted_url', $submittedUrl)->first();

        // If a matching redirect is found, perform a 301 redirect
        if ($redirect) {
            return redirect()->away($redirect->redirect_to, 301); // 301 Permanent Redirect
        }

        Log::error('No redirect found for: ' . $submittedUrl);

        return redirect()->route('admin.redirects.index')->with('error', 'No redirect found for this URL.');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $row = RedirectUrl::findOrFail($id);
        return view('admin.redirect.edit', compact('row'));
    }

    
    public function update(Request $request, $id)
    {
        $request->validate([
            'submitted_url' => 'required|url|unique:redirect_urls,submitted_url,' . $id,
            'redirect_to' => 'required|url',
        ]);

        $redirect = RedirectUrl::findOrFail($id);
        $redirect->update($request->all());

        return redirect()->route('admin.redirects.index')->with('success', 'Redirect updated successfully!');
    }

    
    public function destroy($id)
    {
        $redirect = RedirectUrl::findOrFail($id);
        $redirect->delete();

        return redirect()->route('admin.redirects.index')->with('success', 'Redirect deleted successfully!');
    }
}
