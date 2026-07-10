<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use App\Models\Page;
use App\Support\MediaUrl;
use App\Support\PageTemplate\PageBodyBlocks;
use App\Support\PageTemplate\PageTemplateRegistry;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $template = (string) ($data['template'] ?? $this->getRecord()->template);
        $rawData = is_array($data['data'] ?? null) ? $data['data'] : [];
        $data['data'] = PageTemplateRegistry::forForm($rawData, $template);

        if ($template !== Page::TEMPLATE_DEFAULT) {
            if ($template === Page::TEMPLATE_BASIC_CONTENT) {
                $data['data']['heading'] = trim((string) ($data['data']['heading'] ?? ''));

                if (blank($data['data']['body'] ?? null) && filled($this->getRecord()->content)) {
                    $data['data']['body'] = $this->getRecord()->content;
                }

                if (blank($data['data']['heading'])) {
                    $data['data']['heading'] = $this->getRecord()->displayTitle();
                }
            }

            if ($template === Page::TEMPLATE_GOVERNANCE) {
                $data['data']['heading'] = trim((string) ($data['data']['heading'] ?? ''));

                if (blank($data['data']['heading'])) {
                    $data['data']['heading'] = $this->getRecord()->displayTitle();
                }
            }

            if ($template === Page::TEMPLATE_GENERAL_SECONDARY) {
                $data['data']['heading'] = trim((string) ($data['data']['heading'] ?? ''));

                if (blank($data['data']['heading'])) {
                    $data['data']['heading'] = $this->getRecord()->displayTitle();
                }
            }

            return $data;
        }

        $blocks = $data['data']['body_blocks'] ?? [];

        if ($blocks === [] && filled($this->getRecord()->content)) {
            $blocks[] = [
                'type' => PageBodyBlocks::TYPE_RICH_TEXT,
                'html' => $this->getRecord()->content,
            ];
        }

        $heroImage = (string) ($rawData['hero_image'] ?? '');
        $heroIntro = trim((string) ($rawData['hero_intro'] ?? ''));

        if ((filled($heroImage) || filled(strip_tags($heroIntro)))
            && ! static::heroAlreadyMigratedToBlocks($blocks, $heroImage, $heroIntro, $this->getRecord()->displayTitle())
        ) {
            array_unshift($blocks, [
                'type' => PageBodyBlocks::TYPE_MEDIA_SPLIT,
                'image_position' => 'left',
                'image_shape' => 'acorn',
                'image' => $heroImage,
                'tagline' => '',
                'title' => $this->getRecord()->displayTitle(),
                'content' => $heroIntro,
                'buttons' => [],
            ]);
        }

        $data['data']['body_blocks'] = $blocks;

        return $data;
    }

    /**
     * @param  array<int, array<string, mixed>>  $blocks
     */
    protected static function heroAlreadyMigratedToBlocks(
        array $blocks,
        string $heroImage,
        string $heroIntro,
        string $pageTitle,
    ): bool {
        $normalizedHeroImage = MediaUrl::normalizeStoredPath($heroImage);

        foreach ($blocks as $block) {
            if (($block['type'] ?? '') !== PageBodyBlocks::TYPE_MEDIA_SPLIT) {
                continue;
            }

            if (filled($normalizedHeroImage)
                && MediaUrl::normalizeStoredPath($block['image'] ?? '') === $normalizedHeroImage
            ) {
                return true;
            }

            if (blank($normalizedHeroImage)
                && trim((string) ($block['title'] ?? '')) === trim($pageTitle)
                && trim((string) ($block['content'] ?? '')) === $heroIntro
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return CreatePage::normalizePageData($data);
    }
}
