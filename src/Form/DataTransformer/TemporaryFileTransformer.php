<?php

namespace App\Form\DataTransformer;

use App\TemporaryFile;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Form\DataTransformerInterface;

class TemporaryFileTransformer implements DataTransformerInterface
{
    public function __construct(private FilesystemOperator $tmpStorage)
    {
    }

    public function transform(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        dd('value!', $value);
    }

    public function reverseTransform(mixed $value): ?TemporaryFile
    {
        if ($value === null) {
            return null;
        }

        return new TemporaryFile($value, $this->tmpStorage);
    }
}