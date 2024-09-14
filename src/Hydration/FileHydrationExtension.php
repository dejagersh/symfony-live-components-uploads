<?php

namespace App\Hydration;

use App\TemporaryFile;
use League\Flysystem\FilesystemOperator;
use Symfony\UX\LiveComponent\Hydration\HydrationExtensionInterface;

class FileHydrationExtension implements HydrationExtensionInterface
{
    public function __construct(private FilesystemOperator $tmpStorage)
    {

    }

    public function supports(string $className): bool
    {
        return $className === TemporaryFile::class;
    }

    public function hydrate(mixed $value, string $className): ?object
    {
        return new TemporaryFile($value['filename'], $this->tmpStorage);
    }

    public function dehydrate(object $object): mixed
    {
        return [
            'filename' => $object->getFilename()
        ];
    }
}