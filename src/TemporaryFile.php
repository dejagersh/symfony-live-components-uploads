<?php

namespace App;

use App\Helpers\Str;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Mime\MimeTypes;

class TemporaryFile
{
    public function __construct(private string $filename, private FilesystemOperator $tmpStorage)
    {
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function move(FilesystemOperator $targetStorage): string
    {
        $mimeType = $this->tmpStorage->mimeType($this->getFilename());
        $extension = MimeTypes::getDefault()->getExtensions($mimeType)[0];

        $targetPath = Str::random(40) . '.' . $extension;

        $targetStorage->writeStream(
            $targetPath,
            $this->tmpStorage->readStream($this->getFilename()),
        );

        $this->tmpStorage->delete($this->getFilename());

        return $targetPath;
    }

    public function getClientOriginalName(): string
    {
        return $this->extractOriginalNameFromFilePath($this->filename);
    }

    /**
     * https://github.com/livewire/livewire/blob/main/src/Features/SupportFileUploads/TemporaryUploadedFile.php#L185
     */
    public function extractOriginalNameFromFilePath($path): string
    {
        $path = strtr($path, '_', '/');
        $parts = explode('-meta', $path);
        $lastPart = end($parts);
        $subParts = explode('-', $lastPart);
        $encodedName = reset($subParts);

        return base64_decode($encodedName);
    }
}