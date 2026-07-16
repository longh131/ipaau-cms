<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));

        $articles = $query === ''
            ? new \Illuminate\Pagination\LengthAwarePaginator([], 0, 12, 1, [
                'path' => $request->url(),
                'query' => $request->query(),
            ])
            : Article::query()
                ->with('category')
                ->where('is_active', true)
                ->where(function ($builder) use ($query): void {
                    $like = '%'.$query.'%';

                    $builder->where('title', 'like', $like)
                        ->orWhere('summary', 'like', $like)
                        ->orWhere('author', 'like', $like)
                        ->orWhere('source', 'like', $like);
                })
                ->orderByDesc('is_sticky')
                ->orderByDesc('published_at')
                ->orderByDesc('sort_order')
                ->paginate(12)
                ->withQueryString();

        return view('frontend.search', [
            'query' => $query,
            'articles' => $articles,
            'breadcrumbs' => [
                [
                    'label' => 'Home',
                    'url' => route('home'),
                    'is_home' => true,
                    'is_current' => false,
                ],
                [
                    'label' => '搜索结果',
                    'url' => null,
                    'is_home' => false,
                    'is_current' => true,
                ],
            ],
        ]);
    }
}
