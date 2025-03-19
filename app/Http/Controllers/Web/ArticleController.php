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

  
    // public function show($slug)
    // {
    //     // Article with Service Relationship (Eager Loading for Performance)                                
    //     $data['article'] = Article::with('service')
    //                         ->where('slug', $slug)
    //                         ->where('status', '1')
    //                         ->firstOrFail();
    
    //     // Article Categories
    //     $data['article_categories'] = ArticleCategory::where('status', '1')
    //                         ->orderBy('id', 'asc')
    //                         ->get();
    
    //     // Service Package HTML with Modern Design
    //     $packageHtml = '';
       
    //     $page_quote = \App\Models\PageSetup::page('get-quote');
    //     $page_contact = \App\Models\PageSetup::page('contact-us');
    //     $setting = \App\Models\Setting::first();
    //     if (!empty($data['article']->service) && !empty($data['article']->service_title) && !empty($data['article']->service_desc)) {
    //         // Replace the <li> elements with the ✅ emoji
    //         $description = htmlspecialchars_decode(preg_replace('/<p(.*?)>/i', '<p$1 style="color: #ffffff !important; margin: 8px !important; font-size: 18px !important; ">', $data['article']->service_desc));
    //         $description = preg_replace('/<li>(.*?)<\/li>/i', '<p style="margin:0px; text-align:left !important; color: #ffffff !important;">✅ $1</p>', $description);
    //         $description = str_replace(['<ul>', '</ul>', '<ol>', '</ol>'], '', $description);
        
    //         // Improved Package HTML with Blade Variables Inside String
    //         $packageHtml = "<div class='service-package' style='
    //         background: #1E2A38; 
    //         border: 2px solid #59C94E !important; 
    //         border-radius: 30px !important; 
    //         box-shadow: 0 5px 5px rgba(0, 0, 0, 0.4) !important; 
    //         padding: 20px; 
    //         margin: 20px 0; 
    //         text-align: center;
    //         transition: transform 0.3s ease, box-shadow 0.3s ease;
    //         '>
    //             <h3 style='
    //                 font-size: 22px; 
    //                 font-weight: 700;
    //                 margin-bottom: 10px; 
    //                 text-transform: uppercase;
    //                 color: #FFD700 !important;  
    //                 letter-spacing: 1px;
    //             '>" . htmlspecialchars($data['article']->service_title) . "</h3>
        
    //             <div id='emoji' style='
    //                 text-align:left !important; 
    //                 font-size: 18px !important; 
    //                 line-height: 1.7;
    //                 text-align: left;
    //                 margin-bottom: 18px; 
    //                 color: #ffffff !important;
    //             '>" . $description . "</div>
        
    //             <div class='button-container' style='
    //                 display: flex; 
    //                 align-items: center; 
    //                 justify-content: space-between; 
    //                 gap: 10px; 
    //                 flex-wrap: wrap;
    //             '>
    //                 <div class='circle-container' style='
    //                     display: flex; 
    //                     gap: 10px;
    //                 '>
    //                     <a href='" . route('get-quote') . "' target='_blank' class='circle-button'>
    //                         <img src='https://cdn-icons-png.flaticon.com/128/18572/18572275.png' alt='Get A Quote'>
    //                     </a>
        
    //                     <a rel='noopener noreferrer' href='https://wa.link/vkb4au' target='_blank' class='circle-button'>
    //                         <img src='https://cdn-icons-png.flaticon.com/128/733/733585.png' alt='WhatsApp'>
    //                     </a>
        
    //                     <a href='mailto:" . $setting->email_one . "?subject=Inquiry&body=" . $article->title . "' class='circle-button'>
    //                         <img src='https://cdn-icons-png.flaticon.com/128/732/732200.png' alt='Email'>
    //                     </a>
    //                 </div>
        
    //                 <div>
    //                     <a target='_blank' href='" . url('service/' . htmlspecialchars($article->slug)) . "' style='
    //                         display: inline-block;
    //                         padding: 8px 25px;
    //                         background: linear-gradient(135deg, #00893B, #00B75D);
    //                         color: #ffffff;
    //                         border-radius: 30px;
    //                         text-decoration: none;
    //                         font-weight: bold;
    //                         box-shadow: 0 6px 50px rgba(0, 137, 59, 0.6);
    //                         transition: transform 0.3s ease, box-shadow 0.3s ease;
    //                     '
    //                     onmouseover=\"this.style.transform='scale(1.08)'; this.style.boxShadow='0 6px 10px rgba(0, 137, 59, 0.5)';\"
    //                     onmouseout=\"this.style.transform='scale(1)'; this.style.boxShadow='0 6px 10px rgba(0, 137, 59, 0.5)';\"
    //                     >Visit Now >></a>
    //                 </div>
    //             </div>
    //         </div>";
    //     }
        
    //     // Dynamic Placeholder Logic
    //     $placeholder = $data['article']->placeholder ?? 'serviceshow';
        
    //     // Improved Regex for Better Placeholder Handling
    //     $data['article']->description = preg_replace(
    //         '/(' . preg_quote($placeholder, '/') . ')(?!<\/span>)/i',
    //         $placeholder . " " . $packageHtml,
    //         $data['article']->description
    //     );
        
    //     // Return the view
    //     return view('web.article-single', $data);
    //     }

    public function show($slug)
    {
        // Article with Service Relationship (Eager Loading for Performance)                                
        $data['article'] = Article::with('service')
                            ->where('slug', $slug)
                            ->where('status', '1')
                            ->firstOrFail();
    
        // Assigning $article for cleaner usage
        $article = $data['article'];
    
        // Article Categories
        $data['article_categories'] = ArticleCategory::where('status', '1')
                            ->orderBy('id', 'asc')
                            ->get();
    
        // Service Package HTML with Modern Design
        $packageHtml = '';
    
        $page_quote = \App\Models\PageSetup::page('get-quote');
        $page_contact = \App\Models\PageSetup::page('contact-us');
        $setting = \App\Models\Setting::first();
    
        if (!empty($article->service) && !empty($article->service_title) && !empty($article->service_desc)) {
            // Replace <li> elements with the ✅ emoji
            $description = htmlspecialchars_decode(preg_replace('/<p(.*?)>/i', '<p$1 style="color: #ffffff !important; margin: 8px !important; font-size: 18px !important; ">', $article->service_desc));
            $description = preg_replace('/<li>(.*?)<\/li>/i', '<p style="margin:0px; text-align:left !important; color: #ffffff !important;">✅ $1</p>', $description);
            $description = str_replace(['<ul>', '</ul>', '<ol>', '</ol>'], '', $description);
    
            // Improved Package HTML with Blade Variables Inside String
            $packageHtml = "<div class='service-package' style='
            background: #1E2A38; 
            border: 2px solid #59C94E !important; 
            border-radius: 30px !important; 
            box-shadow: 0 5px 5px rgba(0, 0, 0, 0.4) !important; 
            padding: 20px; 
            margin: 20px 0; 
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            '>
                <h3 style='
                    font-size: 22px; 
                    font-weight: 700;
                    margin-bottom: 10px; 
                    text-transform: uppercase;
                    color: #FFD700 !important;  
                    letter-spacing: 1px;
                '>" . htmlspecialchars($article->service_title) . "</h3>
    
                <div id='emoji' style='
                    text-align:left !important; 
                    font-size: 18px !important; 
                    line-height: 1.7;
                    text-align: left;
                    margin-bottom: 18px; 
                    color: #ffffff !important;
                '>" . $description . "</div>
    
                <div class='button-container' style='
                    display: flex; 
                    align-items: center; 
                    justify-content: space-between; 
                    gap: 10px; 
                    flex-wrap: wrap;
                '>
                    <div class='circle-container' style='
                        display: flex; 
                        gap: 10px;
                    '>
                        <a href='" . route('get-quote') . "' target='_blank' class='circle-button'>
                            <img src='https://cdn-icons-png.flaticon.com/128/18572/18572275.png' alt='Get A Quote'>
                        </a>
    
                        <a rel='noopener noreferrer' href='https://wa.link/vkb4au' target='_blank' class='circle-button'>
                            <img src='https://cdn-icons-png.flaticon.com/128/733/733585.png' alt='WhatsApp'>
                        </a>
    
                        <a href='mailto:" . $setting->email_one . "?subject=Inquiry&body=" . $article->title . "' class='circle-button'>
                            <img src='https://cdn-icons-png.flaticon.com/128/732/732200.png' alt='Email'>
                        </a>
                    </div>
    
                    <div>
                        <a target='_blank' href='" . url('service/' . htmlspecialchars($article->slug)) . "' style='
                            display: inline-block;
                            padding: 8px 25px;
                            background: linear-gradient(135deg, #00893B, #00B75D);
                            color: #ffffff;
                            border-radius: 30px;
                            text-decoration: none;
                            font-weight: bold;
                            box-shadow: 0 6px 50px rgba(0, 137, 59, 0.6);
                            transition: transform 0.3s ease, box-shadow 0.3s ease;
                        '
                        onmouseover=\"this.style.transform='scale(1.08)'; this.style.boxShadow='0 6px 10px rgba(0, 137, 59, 0.5)';\"
                        onmouseout=\"this.style.transform='scale(1)'; this.style.boxShadow='0 6px 10px rgba(0, 137, 59, 0.5)';\"
                        >Visit Now >></a>
                    </div>
                </div>
            </div>";
        }
    
        // Dynamic Placeholder Logic
        $placeholder = $article->placeholder ?? 'serviceshow';
    
        // Improved Regex for Better Placeholder Handling
        $article->description = preg_replace(
            '/(' . preg_quote($placeholder, '/') . ')(?!<\/span>)/i',
            $placeholder . " " . $packageHtml,
            $article->description
        );
    
        // Return the view
        return view('web.article-single', $data);
    }


    }        