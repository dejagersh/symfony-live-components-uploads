<?php

namespace App;

use App\Helpers\Str;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;

trait WithFileUploads
{
    #[LiveAction]
    public function _uploadFile(#[LiveArg] string $propertyName, Request $request, FilesystemOperator $tmpStorage): void
    {
        /** @var UploadedFile $file */
        $file = $request->files->get($propertyName);

        if (!$file) {
            return;
        }

        if (!$file->isValid()) {
            dd('Whoops!');
        }

        $fileName = $this->generateHashNameWithOriginalNameEmbedded($file);

        $stream = fopen($file->getRealPath(), 'r');
        $tmpStorage->writeStream($fileName, $stream);

        if (is_resource($stream)) {
            fclose($stream);
        }

        /**
         * Note to self: might be dangerous to write to any $this->${property} directly.
         * I think Livewire solves this by setting through `app('livewire')->updateProperty($this, $name, $file);
         */
        $this->$propertyName = new TemporaryFile($fileName, $tmpStorage);
    }

    #[LiveAction]
    public function _deleteFile(#[LiveArg] string $propertyName, FilesystemOperator $tmpStorage): void
    {
        if (!$this->$propertyName) {
            return;
        }

        $tmpStorage->delete($this->file->getFilename());

        $this->$propertyName = null;
    }

    /**
     * https://github.com/livewire/livewire/blob/main/src/Features/SupportFileUploads/TemporaryUploadedFile.php#L167
     */
    public function generateHashNameWithOriginalNameEmbedded(UploadedFile $file): string
    {
        $hash = Str::random(30);
        $meta = '-meta' . base64_encode($file->getClientOriginalName()) . '-';
        $meta = strtr($meta, '/', '_');

        $extension = '.' . $file->guessExtension();

        return $hash.$meta.$extension;
    }
}