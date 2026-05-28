<?php

declare(strict_types=1);

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;

if (! function_exists('secure_media_url')) {
    /**
     * Build a viewing URL for a model's first media in a collection,
     * routed through cleaniquecoders/laravel-media-secure so private-disk
     * files are served via the package's auth/policy layer instead of
     * Spatie's default /storage/{id}/{file} URL (which 404s when the file
     * lives on the `media` disk).
     *
     * @param  HasMedia|null  $model       Any HasMedia model, or null
     *                                     (helper handles the null case
     *                                     so call sites don't need a
     *                                     guard).
     * @param  string         $collection  Spatie media collection name
     * @param  bool           $signed      When true, returns a time-
     *                                     limited signed URL safe to
     *                                     embed on public pages. When
     *                                     false, returns the
     *                                     authenticated /media/view URL
     *                                     — only works inside an
     *                                     authenticated request.
     * @param  int|null       $minutes     Signed URL TTL in minutes.
     *                                     Defaults to the package config.
     */
    function secure_media_url(
        ?HasMedia $model,
        string $collection = 'default',
        bool $signed = false,
        ?int $minutes = null,
    ): ?string {
        if ($model === null) {
            return null;
        }

        $media = $model->getFirstMedia($collection);
        if ($media === null) {
            return null;
        }

        return $signed
            ? get_signed_view_url($media, $minutes)
            : get_view_media_url($media);
    }
}

if (! function_exists('has_secure_media')) {
    /**
     * Cheap check used in templates before rendering an <img> — saves a
     * route lookup when nothing's there.
     */
    function has_secure_media(?HasMedia $model, string $collection = 'default'): bool
    {
        return $model !== null && $model->getFirstMedia($collection) !== null;
    }
}

if (! function_exists('upload_media_file')) {
    /**
     * Upload a file to a media collection with optional idempotency.
     *
     * @param  HasMedia  $model  The model using Spatie Media Library
     * @param  UploadedFile  $file  The file to upload
     * @param  string  $collection  Media collection name (default: 'default')
     * @param  bool  $idempotent  Avoid re-uploading same file content (default: true)
     * @return string URL or path of uploaded media
     */
    function upload_media_file(
        HasMedia $model,
        UploadedFile $file,
        string $collection = 'default',
        bool $idempotent = true
    ): string {
        $filename = $file->getClientOriginalName();
        $incomingHash = hash_file('sha256', $file->getRealPath());

        if ($idempotent) {
            $existing = $model->getMedia($collection)->first(function ($media) use ($incomingHash) {
                if (! file_exists($media->getPath())) {
                    return false;
                }

                return hash_file('sha256', $media->getPath()) === $incomingHash;
            });

            if ($existing) {
                return $existing->getUrl();
            }
        }

        return $model->addMedia($file)
            ->usingName(pathinfo($filename, PATHINFO_FILENAME))
            ->usingFileName(Str::random(10).'_'.$filename)
            ->toMediaCollection($collection)
            ->getUrl();
    }
}
