<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FaqCategory;
use App\Models\Client;
use App\Models\Faq;

class FaqsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Faq Categories
        $data['faq_categories'] = FaqCategory::where('status', '1')
                    ->orderBy('id', 'asc')
                    ->get();

        $data['current_category'] = $current_category = FaqCategory::where('status', 1)
                    ->orderBy('id', 'asc')
                    ->firstOrFail();

        // Faqs                                
        $data['faqs'] = Faq::where('category_id', $current_category->id)
                    ->where('status', '1')
                    ->orderBy('id', 'asc')
                    ->get();

        // Clients
        $data['clients'] = Client::where('status', '1')
                    ->orderBy('id', 'desc')
                    ->get();

        return view('web.faqs', $data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function category($slug)
    {
        // Faq Categories
        $data['faq_categories'] = FaqCategory::where('status', '1')
                    ->orderBy('id', 'asc')
                    ->get();

        $data['current_category'] = $current_category = FaqCategory::where('slug', $slug)->firstOrFail();

        // Faqs                                
        $data['faqs'] = Faq::where('category_id', $current_category->id)
        			->where('status', '1')
                    ->orderBy('id', 'asc')
                    ->get();

        // Clients
        $data['clients'] = Client::where('status', '1')
                    ->orderBy('id', 'desc')
                    ->get();

        return view('web.faqs', $data);
    }
}
