<?php

namespace alps\sharepreviews\services;

use alps\sharepreviews\SharePreviews;
use craft\elements\Asset;
use craft\elements\Entry;
use alps\sharepreviews\Config;
use yii\base\Component;

class Images extends Component
{
    private Config $config;

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->config = new Config;
    }

    public function createConfigFromEntry(Entry $entry, int $templateId = null): Config
    {
        $title = $entry->title;

        $config = (new Config)->withContentText($title);
//
//        if ($templates->hasImage($templateId) === false) {
//            return $config;
//        }

//        $image = $entry->seoImage->one() ?? $entry->blogPostImage->one();

//        if (!$image) {
            return $config;
//        }

        $path = 'assets/' . $image->getPath();

        return $config->withImagePublicPath($path);
    }

    public function getShareImagePreviewUrl(Entry $entry): ?string
    {
        if ($this->autoGeneratedPreviewImagesEnabled($entry)) {
            return $this->createConfigFromEntry($entry)->getUrl();
        }

        $image = $this->getPreviewImage($entry);

        if (!$image) {
            return null;
        }

        return $image->getUrl([
            'width' => 1200,
            'height' => 630,
        ]);
    }

    private function getPreviewImage(Entry $entry): ?Asset
    {
        $seoImage = $entry->seoImage ? $entry->seoImage->one() : null;

        if ($seoImage) {
            return $seoImage;
        }

        $blogPostImage = $entry->blogPostImage ? $entry->blogPostImage->one() : null;

        if ($blogPostImage) {
            return $blogPostImage;
        }

        return collect([])
            ->push($entry->imageOrVideo ? $entry->imageOrVideo->all() : [])
            ->push($entry->imagesAndOrVideos ? $entry->imagesAndOrVideos->all() : [])
            ->push($entry->teamImageOrVideo ? $entry->teamImageOrVideo->all() : [])
            ->push($entry->productImage ? $entry->productImage->all() : [])
            ->map(function(array $set) {
                return collect($set)->first(function(Asset $asset) {
                    return $asset->kind === Asset::KIND_IMAGE;
                });
            })
            ->filter()
            ->first();
    }

    public function autoGeneratedPreviewImagesEnabled(Entry $entry): bool
    {
        return $entry->previewImageEnabled === true;
    }
}
