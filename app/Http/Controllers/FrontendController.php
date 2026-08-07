<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Course;
use App\Models\Page;
use App\Models\SpecialCategoryPage;
use App\Services\MenuService;
use App\Services\PageComponentService;
use App\Support\BreadcrumbBuilder;
use App\Support\CategoryListTemplate\CategoryListTemplateRegistry;
use App\Support\PageTemplate\PageTemplateRegistry;
use Illuminate\Http\RedirectResponse;
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
                return $this->renderArticle($slug);

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

        if (CategoryListTemplateRegistry::isCourseTable($category)) {
            return $this->renderCourseCategory($category);
        }

        if (CategoryListTemplateRegistry::isSpecialCourseList($category)) {
            return $this->renderSpecialCourseListCategory($category);
        }

        if (CategoryListTemplateRegistry::isSpecialCertificateLookup($category)) {
            return $this->renderSpecialCertificateLookupCategory($category);
        }

        if (CategoryListTemplateRegistry::isSpecialVideoHub($category)) {
            return $this->renderSpecialVideoHubCategory($category);
        }

        $articles = CategoryListTemplateRegistry::applyArticleOrdering(
            Article::query()
                ->where('category_id', $category->id)
                ->where('is_active', true),
            $category,
        )->paginate(CategoryListTemplateRegistry::perPageFor($category));

        return view(CategoryListTemplateRegistry::viewFor($category), [
            'category' => $category,
            'articles' => $articles,
            'breadcrumbs' => BreadcrumbBuilder::forCategory($category),
            'listFields' => \App\Support\ArticleExtraFields::listFields($category->article_extra_field_schema),
            'introductionHtml' => \App\Support\CategoryIntroduction::toHtml($category),
            'initialVisible' => CategoryListTemplateRegistry::initialVisibleFor($category),
        ]);
    }

    private function renderCourseCategory(Category $category): View
    {
        $courses = Course::query()
            ->where('category_id', $category->id)
            ->where('is_active', true)
            ->orderByDesc('starts_at')
            ->orderByDesc('legacy_added_at')
            ->orderByDesc('id')
            ->paginate(CategoryListTemplateRegistry::perPageFor($category));

        return view(CategoryListTemplateRegistry::viewFor($category), [
            'category' => $category,
            'courses' => $courses,
            'breadcrumbs' => BreadcrumbBuilder::forCategory($category),
            'introductionHtml' => \App\Support\CategoryIntroduction::toHtml($category),
        ]);
    }

    private function renderSpecialCourseListCategory(Category $category): View
    {
        $pageSettings = SpecialCategoryPage::query()
            ->where('category_id', $category->id)
            ->first();

        $courses = Course::query()
            ->whereIn('category_id', $pageSettings?->resolvedCourseCategoryIds() ?? Course::categoryIds())
            ->where('is_active', true)
            ->orderByDesc('starts_at')
            ->orderByDesc('legacy_added_at')
            ->orderByDesc('id')
            ->paginate(CategoryListTemplateRegistry::perPageFor($category));

        return view(CategoryListTemplateRegistry::viewFor($category), [
            'category' => $category,
            'courses' => $courses,
            'breadcrumbs' => BreadcrumbBuilder::forCategory($category),
            'introductionHtml' => \App\Support\CategoryIntroduction::toHtml($category),
            'bodyHtmlTop' => $pageSettings?->bodyHtmlTopForFrontend() ?? '',
            'bodyHtmlBottom' => $pageSettings?->bodyHtmlBottomForFrontend() ?? '',
        ]);
    }

    private function renderSpecialCertificateLookupCategory(Category $category): View
    {
        $pageSettings = SpecialCategoryPage::query()
            ->where('category_id', $category->id)
            ->where('feature_type', SpecialCategoryPage::FEATURE_CERTIFICATE_LOOKUP)
            ->first();

        return view(CategoryListTemplateRegistry::viewFor($category), [
            'category' => $category,
            'breadcrumbs' => BreadcrumbBuilder::forCategory($category),
            'introductionHtml' => \App\Support\CategoryIntroduction::toHtml($category),
            'bodyHtmlTop' => $pageSettings?->bodyHtmlTopForFrontend() ?? '',
            'bodyHtmlBottom' => $pageSettings?->bodyHtmlBottomForFrontend() ?? '',
            'certificateTitle' => $pageSettings?->certificateTitleForFrontend() ?? '证书查询',
            'certificateSummary' => $pageSettings?->certificateSummaryForFrontend() ?? '',
            'lookupResult' => session('certificate_lookup_result'),
        ]);
    }

    private function renderSpecialVideoHubCategory(Category $category): View
    {
        $pageSettings = SpecialCategoryPage::query()
            ->where('category_id', $category->id)
            ->where('feature_type', SpecialCategoryPage::FEATURE_VIDEO_HUB)
            ->first();

        return view(CategoryListTemplateRegistry::viewFor($category), [
            'category' => $category,
            'breadcrumbs' => BreadcrumbBuilder::forCategory($category),
            'introductionHtml' => \App\Support\CategoryIntroduction::toHtml($category),
            'bodyHtmlTop' => $pageSettings?->bodyHtmlTopForFrontend() ?? '',
            'bodyHtmlBottom' => $pageSettings?->bodyHtmlBottomForFrontend() ?? '',
            'videoSections' => \App\Support\CategoryListTemplate\VideoHubSectionData::sectionsForFrontend(),
        ]);
    }

    private function renderArticle(string $slug): View|RedirectResponse
    {
        $article = Article::query()
            ->with('category')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        if (filled($article->redirect_url)) {
            return redirect()->away($article->redirect_url);
        }

        $article->increment('view_count');

        $category = $article->category;

        if ($category && CategoryListTemplateRegistry::isTeamIntro($category)) {
            return view('frontend.articles.team_intro', [
                'article' => $article,
                'category' => $category,
                'breadcrumbs' => BreadcrumbBuilder::forArticle($article),
                'introductionHtml' => \App\Support\CategoryIntroduction::toHtml($category),
                'jobTitle' => \App\Support\ArticleExtraFields::teamJobTitle($article->extra_fields),
                'coverUrl' => \App\Support\ArticleExtraFields::teamCoverUrl($article->extra_fields, $article->cover_image),
            ]);
        }

        if ($category && CategoryListTemplateRegistry::isEventsCpd($category)) {
            return view('frontend.articles.events_cpd', [
                'article' => $article,
                'category' => $category,
                'breadcrumbs' => BreadcrumbBuilder::forArticle($article),
                'extraFieldItems' => array_values(array_filter(
                    \App\Support\ArticleExtraFields::forFrontend(
                        $article->extra_fields,
                        $article->category?->article_extra_field_schema,
                    ),
                    fn (array $item): bool => ($item['key'] ?? '') !== \App\Support\CategoryListTemplate\EventsCpdTemplate::EVENT_NAME_KEY,
                )),
                'registrationUrl' => \App\Support\CategoryListTemplate\EventsCpdTemplate::registrationUrl(
                    $article->extra_fields,
                ),
            ]);
        }

        if ($category && CategoryListTemplateRegistry::isVideoList($category)) {
            return view('frontend.articles.video', [
                'article' => $article,
                'category' => $category,
                'breadcrumbs' => BreadcrumbBuilder::forArticle($article),
                'videoUrl' => \App\Support\CategoryListTemplate\VideoListTemplate::videoPublicUrlForArticle($article),
                'posterUrl' => \App\Support\CategoryListTemplate\VideoListTemplate::posterPublicUrlForArticle($article),
                'videoMimeType' => \App\Support\CategoryListTemplate\VideoListTemplate::videoMimeType(
                    \App\Support\CategoryListTemplate\VideoListTemplate::videoPublicUrlForArticle($article),
                ),
            ]);
        }

        return view('frontend.article', [
            'article' => $article,
            'category' => $article->category,
            'breadcrumbs' => BreadcrumbBuilder::forArticle($article),
            'extraFieldItems' => \App\Support\ArticleExtraFields::forFrontend(
                $article->extra_fields,
                $article->category?->article_extra_field_schema,
            ),
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
            'pageSlugClass' => 'cms-page--'.$page->slug,
            'headerBlobPartial' => 'blob-about',
        ]);
    }
}
