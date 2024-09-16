<?php

namespace App;

use App\Helpers\Str;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;

trait ComponentWithLiveUploads
{
    #[LiveAction]
    public function _uploadFile(#[LiveArg] string $fieldName, Request $request, FilesystemOperator $tmpStorage): void
    {
        /** @var UploadedFile $file */
        $file = $request->files->get($fieldName);

        if (!$file) {
            dd('What!');
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

        $this->formValues[$fieldName] = $fileName;
    }

    #[LiveAction]
    public function _deleteFile(#[LiveArg] string $fieldName, FilesystemOperator $tmpStorage): void
    {
        // todo: implement this
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