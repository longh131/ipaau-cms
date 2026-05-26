<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Page;
use App\Models\Menu;
use App\Models\MenuItem;
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

        $menu = $this->getMenuItems('main');

        return view('frontend.home', compact('articles', 'categories', 'menu'));
    }

    public function render($type, $slug = null)
    {
        $menu = $this->getMenuItems('main');
        
        switch ($type) {
            case 'page':
                $page = Page::where('slug', $slug)->where('is_active', true)->firstOrFail();
                return view('frontend.page', compact('page', 'menu'));
            
            case 'category':
                $category = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();
                $articles = Article::where('category_id', $category->id)
                    ->where('is_active', true)
                    ->orderBy('published_at', 'desc')
                    ->paginate(10);
                return view('frontend.category', compact('category', 'articles', 'menu'));
            
            case 'article':
                $article = Article::where('slug', $slug)->where('is_active', true)->firstOrFail();
                return view('frontend.article', compact('article', 'menu'));
            
            default:
                abort(404);
        }
    }

    public function getMenuItems($menuSlug)
    {
        $menu = Menu::where('slug', $menuSlug)->where('is_active', true)->first();
        
        if (!$menu) {
            return [];
        }

        return $this->buildMenuTree($menu->items()->where('parent_id', 0)->orderBy('sort_order')->get());
    }

    private function buildMenuTree($items)
    {
        return $items->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'url' => $this->resolveMenuItemUrl($item),
                'target' => $item->target,
                'children' => $this->buildMenuTree($item->children),
            ];
        })->toArray();
    }

    private function resolveMenuItemUrl($item)
    {
        switch ($item->link_type) {
            case 'page':
                return route('page.show', $item->link_id ? Page::find($item->link_id)?->slug : '');
            case 'category':
                return route('category.show', $item->link_id ? Category::find($item->link_id)?->slug : '');
            case 'article':
                return route('article.show', $item->link_id ? Article::find($item->link_id)?->slug : '');
            case 'url':
            default:
                return $item->url;
        }
    }
}