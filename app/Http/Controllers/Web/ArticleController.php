<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use App\Models\Article;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Articles                                
        $data['articles'] = Article::where('status', '1')
                            ->orderBy('id', 'desc')
                            ->paginate(5);

        // Article Category
        $data['article_categories'] = ArticleCategory::where('status', '1')
                            ->orderBy('id', 'asc')
                            ->get();

        return view('web.article-category', $data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function category($slug)
    {
        // Article Category
        $data['article_categories'] = ArticleCategory::where('status', '1')
                            ->orderBy('id', 'asc')
                            ->get();

        $data['current_category'] = $current_category = ArticleCategory::where('slug', $slug)
                            ->where('status', '1')
                            ->firstOrFail();

        // Articles                                
        $data['articles'] = Article::where('category_id', $current_category->id)
                            ->where('status', '1')
                            ->orderBy('id', 'desc')
                            ->paginate(5);

        return view('web.article-category', $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $data['search'] = $search = strip_tags($request->search);

        // Articles                                
        $data['articles'] = Article::where(function($query) use ($search){
                                $query->where('title', 'LIKE', '%'.$search.'%' );
                                $query->orWhere('description', 'LIKE', '%'.$search.'%' );
                            })
                            ->where('status', '1')
                            ->orderBy('id', 'desc')
                            ->paginate(5);

        // Article Category
        $data['article_categories'] = ArticleCategory::where('status', '1')
                            ->orderBy('id', 'asc')
                            ->get();

        return view('web.article-category', $data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        // Article with Service Relationship (Eager Loading for Performance)                                
        $data['article'] = Article::with('service')
                            ->where('slug', $slug)
                            ->where('status', '1')
                            ->firstOrFail();
    
        // Article Categories
        $data['article_categories'] = ArticleCategory::where('status', '1')
                            ->orderBy('id', 'asc')
                            ->get();
    
        // Service Package HTML with Modern Design
        $packageHtml = '';
    
        if (!empty($data['article']->service)) {
            $packageHtml = "<div class='service-package' style='
    background: #2c3e50; /* Solid background for sleek look */
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: 18px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    padding: 25px; /* Reduced padding for a more compact look */
    margin: 20px 0; /* Reduced margin for a tighter design */
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
'>
    <h3 style='
        font-size: 24px; /* Slightly smaller font size for a more compact design */
        margin-bottom: 12px; /* Reduced margin for compactness */
        text-transform: uppercase;
        color: rgb(255, 255, 255);
        letter-spacing: 1px;
        font-weight: 600;
    '>" . htmlspecialchars($data['article']->service->title) . "</h3>

    <p style='
        font-size: 16px; /* Slightly smaller font size for the description */
        line-height: 1.6;
        text-align: left;
        margin-bottom: 15px; /* Reduced margin for compact look */
        color: rgb(255, 255, 255);
    '>" . htmlspecialchars($data['article']->service->short_desc) . "</p>

    <a href='" . htmlspecialchars($data['article']->service->link) . "' style='
        display: inline-block;
        margin-top: 10px;
        padding: 10px 30px; /* Slightly reduced padding */
        background-color: #1E5C63; /* Solid background without gradient */
        color: #ffffff;
        border-radius: 30px;
        text-decoration: none;
        font-weight: bold;
        box-shadow: 0 6px 20px rgba(30, 92, 99, 0.4);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    ' 
        onmouseover='this.style.transform=\"scale(1.05)\"; this.style.boxShadow=\"0 8px 35px rgba(30, 92, 99, 0.6)\";'
        onmouseout='this.style.transform=\"scale(1)\"; this.style.boxShadow=\"0 6px 20px rgba(30, 92, 99, 0.4)\";'
    >Discover Now</a>

</div>";

        }
            
        // Dynamic Placeholder Logic
        $placeholder = $data['article']->placeholder ?? 'serviceshow';
    
        // Improved Regex for Better Placeholder Handling
        $data['article']->description = preg_replace(
            '/(' . preg_quote($placeholder, '/') . ')(?!<\/span>)/i',
            $placeholder . " " . $packageHtml,
            $data['article']->description
        );
    
        // Return the view
        return view('web.article-single', $data);
    }
}    
