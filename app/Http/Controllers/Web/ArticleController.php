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
        // Article                                
        $data['article'] = Article::with('service') // Added eager loading
                            ->where('slug', $slug)
                            ->where('status', '1')
                            ->firstOrFail();

        // Article Category
        $data['article_categories'] = ArticleCategory::where('status', '1')
                            ->orderBy('id', 'asc')
                            ->get();

        // Service Package HTML
        $packageHtml = '';
        
            $packageHtml = '<div class="service-package" style="
                                    background-color:rgb(0, 10, 51);
                                    color: #ffffff;
                                    padding: 20px;
                                    border-radius: 12px;
                                    border: 3px solid #1D2854;
                                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
                                    margin: 20px 0;
                                    text-align: center;">
                                    <h3 style="font-size: 24px; margin-bottom: 10px;">' . $data['article']->service->title . '</h3>
                                    <p style="font-size: 16px; line-height: 1.6;">' . $data['article']->service->short_desc . '</p>
                                    <a href="' . $data['article']->service->link . '" style="
                                        display: inline-block;
                                        margin-top: 10px;
                                        padding: 10px 20px;
                                        background:rgb(255, 7, 7);
                                        color: #ffffff;
                                        border-radius: 8px;
                                        text-decoration: none;
                                        font-weight: bold;">Visit</a>
                                </div>';
        

        // Dynamic Placeholder Logic
        $placeholder = $data['article']->placeholder ?? 'serviceshow';
        $data['article']->description = preg_replace(
            '/\b' . preg_quote($placeholder, '/') . '\b/i',
            $placeholder . " " . $packageHtml,
            $data['article']->description
        );

        return view('web.article-single', $data);
    }

    
}
