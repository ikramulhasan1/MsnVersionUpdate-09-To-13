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
    background: #1E2A38; 
    border: 2px solid rgba(255, 255, 255, 0.15);
    border-radius: 20px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
    padding: 30px; 
    margin: 20px 0; 
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
'>
    <h3 style='
        font-size: 22px; 
        font-weight: 700;
        margin-bottom: 10px; 
        text-transform: uppercase;
        color: #FFD700;  /* Gold for premium feel */
        letter-spacing: 1px;
    '>" . htmlspecialchars($data['article']->service->title) . "</h3>

    <p style='
        font-size: 15px; 
        line-height: 1.7;
        text-align: left;
        margin-bottom: 18px; 
        color: #E0E0E0; 
    '>" . nl2br(htmlspecialchars($data['article']->service->short_desc)) . "</p>

    <a href='" . htmlspecialchars($data['article']->service->link) . "' style='
        display: inline-block;
        margin-top: 10px;
        padding: 12px 40px; 
        background: linear-gradient(135deg, #00893B, #00B75D); /* Enhanced Gradient */
        color: #ffffff; 
        border-radius: 30px;
        text-decoration: none;
        font-weight: bold;
        box-shadow: 0 6px 20px rgba(0, 137, 59, 0.6);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    '
    onmouseover=\"this.style.transform='scale(1.08)'; this.style.boxShadow='0 10px 30px rgba(0, 183, 93, 0.8)';\"
    onmouseout=\"this.style.transform='scale(1)'; this.style.boxShadow='0 6px 20px rgba(0, 137, 59, 0.5)';\"
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
