<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Page;
use App\Services\MenuService;
use App\Services\PageComponentService;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function __construct(
        private readonly MenuService $menuService,
        private readonly PageComponentService $pageComponentService,
    ) {}

    public function home()
    {
        return view('frontend.home', [
            'menuItems' => $this->menuService->getHeaderMenuItems(),
            'hero' => $this->pageComponentService->getHeroData(),
            'footnoteCards' => $this->pageComponentService->getFootnoteCardsData(),
            'membership' => $this->pageComponentService->getMembershipData(),
            'stats' => $this->pageComponentService->getStatsData(),
            'cpdIntro' => $this->pageComponentService->getCpdIntroData(),
            'tabbedContent' => $this->pageComponentService->getTabbedContentData(),
            'testimonials' => $this->pageComponentService->getTestimonialsData(),
            'articles' => [],
            'categories' => [],
        ]);
    }

    public function render(Request $request, string $slug)
    {
        $type = match ($request->route()?->getName()) {
            'page.show' => 'page',
            'category.show' => 'category',
            'article.show' => 'article',
            default => null,
        };

        if (! $type) {
            abort(404);
        }

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
