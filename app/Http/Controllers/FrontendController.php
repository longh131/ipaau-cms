<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Page;
use App\Models\Menu;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function home()
    {
        $articles = Article::where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->take(6)
            ->get();

        $categories = Category::where('is_active', true)->get();

        $menu = Menu::where('slug', 'main')->first();

        return view('frontend.home', compact('articles', 'categories', 'menu'));
    }

    public function render($type, $slug = null)
    {
        switch ($type) {
            case 'page':
                $page = Page::where('slug', $slug)->where('is_active', true)->firstOrFail();
                return view('frontend.page', compact('page'));
            
            case 'category':
                $category = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();
                $articles = Article::where('category_id', $category->id)
                    ->where('is_active', true)
                    ->orderBy('published_at', 'desc')
                    ->paginate(10);
                return view('frontend.category', compact('category', 'articles'));
            
            case 'article':
                $article = Article::where('slug', $slug)->where('is_active', true)->firstOrFail();
                return view('frontend.article', compact('article'));
            
            default:
                abort(404);
        }
    }
}