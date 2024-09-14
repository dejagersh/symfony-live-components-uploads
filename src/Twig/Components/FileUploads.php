<?php

namespace App\Twig\Components;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class FileUploads
{
    use DefaultActionTrait;

    #[LiveProp]
    public ?string $fileName = null;

    #[LiveAction]
    public function uploadFile(Request $request, string $projectDir): void
    {
        $tmpDir = $projectDir . '/storage/tmp';

        $file = $request->files->get('my_file');

        $fileName = $this->generateHashNameWithOriginalNameEmbedded($file);

        $file->move($tmpDir, $fileName);

        $this->fileName = $fileName;
    }

    #[LiveAction]
    public function submit(string $projectDir, Filesystem $filesystem)
    {
        $tmpDir = $projectDir . '/storage/tmp';

        $directory = $projectDir . '/storage/images';

        $filesystem->rename(
            $tmpDir . '/' . $this->fileName,
            $directory . '/' . $this->fileName
        );

        dd('Store this in DB: ' . $this->fileName);
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


    /**
     * https://github.com/livewire/livewire/blob/main/src/Features/SupportFileUploads/TemporaryUploadedFile.php#L167
     */
    public function generateHashNameWithOriginalNameEmbedded(UploadedFile $file): string
    {
        $hash = $this->random(30);
        $meta = '-meta' . base64_encode($file->getClientOriginalName()) . '-';
        $meta = strtr($meta, '/', '_');

        $extension = '.'.$file->guessExtension();

        return $hash.$meta.$extension;
    }
}
