<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SpecialCategoryPage;
use App\Support\CertificateLookupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CertificateLookupController extends Controller
{
    public function __construct(
        private readonly CertificateLookupService $certificateLookupService,
    ) {}

    public function store(Request $request, string $slug): RedirectResponse
    {
        $category = Category::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        SpecialCategoryPage::query()
            ->where('category_id', $category->id)
            ->where('feature_type', SpecialCategoryPage::FEATURE_CERTIFICATE_LOOKUP)
            ->firstOrFail();

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'member_number' => ['required', 'string', 'max:255'],
        ], [
            'full_name.required' => '请填写会员姓名。',
            'member_number.required' => '请填写证书编号。',
        ]);

        $result = $this->certificateLookupService->lookup(
            $validated['full_name'],
            $validated['member_number'],
        );

        return redirect()
            ->route('category.show', $category->slug)
            ->withFragment('certificate-lookup')
            ->withInput()
            ->with('certificate_lookup_result', $result);
    }
}
