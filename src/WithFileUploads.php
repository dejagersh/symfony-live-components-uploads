<?php

namespace App;

use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;

trait WithFileUploads
{
    #[LiveAction]
    public function _uploadFile(#[LiveArg] string $property, Request $request, FilesystemOperator $tmpStorage): void
    {
        /** @var UploadedFile $file */
        $file = $request->files->get($property);

        if (!$file) {
            return;
        }

        if (!$file->isValid()) {
            dd('Whoops!');
        }

        $fileName = $this->generateHashNameWithOriginalNameEmbedded($file);

        $stream = fopen($file->getRealPath(), 'r');
        $tmpStorage->writeStream($fileName, $stream);
        fclose($stream);

        /**
         * Note to self: might be dangerous to write to any $this->${property} directly.
         * I think Livewire solves this by setting through `app('livewire')->updateProperty($this, $name, $file);
         */
        $this->$property = new TemporaryFile($fileName, $tmpStorage);
    }

    #[LiveAction]
    public function deleteFile(): void
    {
        $this->file = null;
    }

    /**
     * https://github.com/livewire/livewire/blob/main/src/Features/SupportFileUploads/TemporaryUploadedFile.php#L167
     */
    public function generateHashNameWithOriginalNameEmbedded(UploadedFile $file): string
    {
        $hash = $this->random(30);
        $meta = '-meta' . base64_encode($file->getClientOriginalName()) . '-';
        $meta = strtr($meta, '/', '_');

        $extension = '.' . $file->guessExtension();

        return $hash.$meta.$extension;
    }

    /**
     * https://github.com/illuminate/support/blob/master/Str.php#L991
     */
    public function random(int $length): string
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytesSize = (int) ceil(($size) / 3) * 3;

            $bytes = random_bytes($bytesSize);

            $string .= substr(
                str_replace(['/', '+', '='], '', base64_encode($bytes)),
                0,
                $size,
            );
        }

        return $string;
    }

}