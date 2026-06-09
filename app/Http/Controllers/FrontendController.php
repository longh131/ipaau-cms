<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Page;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class FrontendController extends Controller
{
    public function home()
    {
        $articles = [];
        $categories = [];
        
        try {
            $menuItems = $this->getMenuItems('main');
        } catch (QueryException $e) {
            $menuItems = [];
        }

        if (empty($menuItems)) {
            $menuItems = $this->getDefaultMenuItems();
        }

        return view('frontend.home', compact('articles', 'categories', 'menuItems'));
    }

    private function getDefaultMenuItems()
    {
        return [
            [
                'id' => 1,
                'title' => 'About IPA',
                'url' => '#',
                'target' => '_self',
                'children' => [
                    ['id' => 11, 'title' => 'About the IPA', 'url' => '/about-ipa/about-the-ipa/', 'target' => '_self', 'children' => [
                        ['id' => 111, 'title' => 'Contact Us', 'url' => '/about-ipa/about-the-ipa/contact-us/', 'target' => '_self', 'children' => []],
                        ['id' => 112, 'title' => 'Recognition', 'url' => '/about-ipa/about-the-ipa/recognition/', 'target' => '_self', 'children' => []],
                        ['id' => 113, 'title' => 'Leadership Team', 'url' => '/about-ipa/about-the-ipa/leadership-team/', 'target' => '_self', 'children' => []],
                        ['id' => 114, 'title' => 'Meet Our State Teams', 'url' => '/about-ipa/about-the-ipa/meet-our-state-teams/', 'target' => '_self', 'children' => []],
                    ]],
                    ['id' => 12, 'title' => 'Social Value', 'url' => '/about-ipa/social-value/', 'target' => '_blank', 'children' => [
                        ['id' => 121, 'title' => 'CSR Partners', 'url' => '/about-ipa/social-value/csr-partners/', 'target' => '_self', 'children' => []],
                    ]],
                    ['id' => 13, 'title' => 'Governance', 'url' => '/about-ipa/governance/', 'target' => '_self', 'children' => [
                        ['id' => 131, 'title' => 'Rules & Standards', 'url' => '/about-ipa/governance/ipa-rules-standards/', 'target' => '_self', 'children' => []],
                        ['id' => 132, 'title' => 'Consumer Protection', 'url' => '/about-ipa/about-the-ipa/consumer-protection/', 'target' => '_self', 'children' => []],
                        ['id' => 133, 'title' => 'Complaints & Disciplinary Action', 'url' => '/about-ipa/governance/complaints-disciplinary-action/', 'target' => '_self', 'children' => []],
                    ]],
                    ['id' => 14, 'title' => 'Partner With Us', 'url' => '/about-ipa/partner-with-us/', 'target' => '_blank', 'children' => []],
                ],
            ],
            [
                'id' => 2,
                'title' => 'Students & Members',
                'url' => '#',
                'target' => '_self',
                'children' => [
                    ['id' => 21, 'title' => 'Become a Member', 'url' => '/students-members/become-a-member/', 'target' => '_self', 'children' => [
                        ['id' => 211, 'title' => 'Member Benefits', 'url' => '/students-members/become-a-member/member-benefits/', 'target' => '_self', 'children' => []],
                        ['id' => 212, 'title' => 'Member Requirements', 'url' => '/students-members/become-a-member/member-requirements/', 'target' => '_self', 'children' => []],
                    ]],
                    ['id' => 22, 'title' => 'Membership Types & Pathways', 'url' => '/students-members/membership-pathways-types/', 'target' => '_self', 'children' => [
                        ['id' => 221, 'title' => 'Joint Memberships', 'url' => '/students-members/membership-pathways-types/joint-membership/', 'target' => '_self', 'children' => []],
                        ['id' => 222, 'title' => 'Public Practice', 'url' => '/students-members/membership-pathways-types/public-practice/', 'target' => '_self', 'children' => []],
                    ]],
                    ['id' => 23, 'title' => 'Member Resources', 'url' => '/students-members/member-resources/', 'target' => '_self', 'children' => [
                        ['id' => 231, 'title' => 'Professional Assist', 'url' => '#', 'target' => '_self', 'children' => []],
                    ]],
                    ['id' => 24, 'title' => 'Member Portal', 'url' => '#', 'target' => '_self', 'children' => []],
                ],
            ],
            [
                'id' => 3,
                'title' => 'Education & Events',
                'url' => '#',
                'target' => '_self',
                'children' => [
                    ['id' => 31, 'title' => 'Events', 'url' => '/education-events/events/', 'target' => '_self', 'children' => []],
                    ['id' => 32, 'title' => 'Education', 'url' => '/education-events/education/', 'target' => '_self', 'children' => []],
                    ['id' => 33, 'title' => 'CPD', 'url' => '/education-events/cpd/', 'target' => '_self', 'children' => []],
                    ['id' => 34, 'title' => 'Certificates & Diplomas', 'url' => '/education-events/certificates-diplomas/', 'target' => '_self', 'children' => []],
                ],
            ],
            [
                'id' => 4,
                'title' => 'Migration Assessments',
                'url' => '/migration-assessments/',
                'target' => '_self',
                'children' => [],
            ],
            [
                'id' => 5,
                'title' => 'News & Insights',
                'url' => '/news-insights/',
                'target' => '_self',
                'children' => [],
            ],
            [
                'id' => 6,
                'title' => 'Careers',
                'url' => '/careers/',
                'target' => '_self',
                'children' => [],
            ],
        ];
    }

    public function render($type, $slug = null)
    {
        try {
            $menu = $this->getMenuItems('main');
        } catch (QueryException $e) {
            $menu = [];
        }
        
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
        $menu = Menu::where('slug', $menuSlug)->first();
        
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
