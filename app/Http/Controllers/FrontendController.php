<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Page;
use App\Services\MenuService;
use App\Services\PageComponentService;
use App\Support\BreadcrumbBuilder;
use App\Support\PageTemplate\PageTemplateRegistry;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
            'sectionActive' => $this->pageComponentService->getHomeSectionActiveMap(),
            'hero' => $this->pageComponentService->getHeroData(),
            'footnoteCards' => $this->pageComponentService->getFootnoteCardsData(),
            'membership' => $this->pageComponentService->getMembershipData(),
            'stats' => $this->pageComponentService->getStatsData(),
            'cpdIntro' => $this->pageComponentService->getCpdIntroData(),
            'tabbedContent' => $this->pageComponentService->getTabbedContentData(),
            'testimonials' => $this->pageComponentService->getTestimonialsData(),
            'aboutIntro' => $this->pageComponentService->getAboutIntroData(),
            'diversity' => $this->pageComponentService->getDiversityData(),
            'ctaSection' => $this->pageComponentService->getCtaSectionData(),
            'faq' => $this->pageComponentService->getFaqData(),
            'newsletter' => $this->pageComponentService->getNewsletterData(),
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
                return redirect()->route('category.show', $slug, 301);

            case 'category':
                return $this->renderCategory($slug);

            case 'article':
                $article = Article::where('slug', $slug)->where('is_active', true)->firstOrFail();

                return view('frontend.article', compact('article'));

            default:
                abort(404);
        }
    }

    private function renderCategory(string $slug): View
    {
        $category = Category::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        if ($category->type === 'page') {
            $page = Page::query()
                ->with('category')
                ->where('category_id', $category->id)
                ->where('is_active', true)
                ->firstOrFail();

            return $this->renderPage($page);
        }

        $articles = Article::query()
            ->where('category_id', $category->id)
            ->where('is_active', true)
            ->orderByDesc('published_at')
            ->paginate(10);

        return view('frontend.categories.articles', [
            'category' => $category,
            'articles' => $articles,
            'breadcrumbs' => BreadcrumbBuilder::forCategory($category),
        ]);
    }

    private function renderPage(Page $page): View
    {
        $page->loadMissing('category');

        $view = 'frontend.pages.'.($page->template ?: Page::TEMPLATE_DEFAULT);

        if (! view()->exists($view)) {
            $view = 'frontend.pages.default';
        }

        return view($view, [
            'page' => $page,
            'category' => $page->category,
            'pageView' => PageTemplateRegistry::forFrontend($page->data, $page->template, $page),
            'breadcrumbs' => BreadcrumbBuilder::forCategory($page->category, $page->displayTitle()),
            'bodyClass' => 'cms-about-page',
            'headerBlobPartial' => 'blob-about',
        ]);
    }
}
