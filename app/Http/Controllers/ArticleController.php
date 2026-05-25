<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Helpers\NavigationHelper;

class ArticleController extends Controller
{
    public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        $articles = Article::where('category_id', $category->id)
            ->where('published_at', '<=', now())
            ->orderBy('sort_order')
            ->get();

        $pageData = [
            'category' => $category,
            'articles' => $articles,
            'navigation' => NavigationHelper::getMainNavigation(),
            'footer' => []
        ];

        return view('frontend.category', compact('pageData'));
    }

    public function show($slug)
    {
        $article = Article::where('slug', $slug)->firstOrFail();

        $pageData = [
            'article' => $article,
            'navigation' => NavigationHelper::getMainNavigation(),
            'footer' => []
        ];

        return view('frontend.article', compact('pageData'));
    }
}