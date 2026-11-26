<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SitemapController extends Controller
{
    public function __invoke(): StreamedResponse
    {
        return response()->stream(function (): void {
            echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
            echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

            $this->writeUrl(url('/'), now(), 'daily', '1.0');

            $categories = Category::query()
                ->where('is_active', true)
                ->where(function ($query): void {
                    $query->where('requires_member_login', false)
                        ->orWhereNull('requires_member_login');
                })
                ->orderBy('id')
                ->get(['slug', 'updated_at']);

            foreach ($categories as $category) {
                $this->writeUrl(
                    route('category.show', $category->slug),
                    $category->updated_at,
                    'weekly',
                    '0.8',
                );
            }

            Article::query()
                ->select(['id', 'slug', 'published_at', 'updated_at'])
                ->where('is_active', true)
                ->whereHas('category', function ($query): void {
                    $query->where('is_active', true)
                        ->where(function ($inner): void {
                            $inner->where('requires_member_login', false)
                                ->orWhereNull('requires_member_login');
                        });
                })
                ->orderBy('id')
                ->cursor()
                ->each(function (Article $article): void {
                    $this->writeUrl(
                        route('article.show', $article->slug),
                        $article->updated_at ?? $article->published_at,
                        'weekly',
                        '0.6',
                    );
                });

            echo '</urlset>'."\n";
        }, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    private function writeUrl(string $loc, mixed $lastmod, string $changefreq, string $priority): void
    {
        echo "  <url>\n";
        echo '    <loc>'.e($loc)."</loc>\n";

        $formatted = $this->formatLastmod($lastmod);

        if (filled($formatted)) {
            echo '    <lastmod>'.e($formatted)."</lastmod>\n";
        }

        echo '    <changefreq>'.e($changefreq)."</changefreq>\n";
        echo '    <priority>'.e($priority)."</priority>\n";
        echo "  </url>\n";
    }

    private function formatLastmod(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->toAtomString();
        } catch (\Throwable) {
            return null;
        }
    }
}
