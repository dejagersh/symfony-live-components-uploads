<?php

namespace App\Twig\Components;

use App\TemporaryFile;
use App\WithFileUploads;
use League\Flysystem\FilesystemOperator;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class SimpleLiveComponent
{
    use DefaultActionTrait;
    use WithFileUploads;

    #[LiveProp]
    public ?TemporaryFile $file = null;

    #[LiveProp]
    public ?TemporaryFile $fileTwo = null;

    #[LiveAction]
    public function submit(FilesystemOperator $permanentStorage): void
    {
        $this->fileTwo->move($permanentStorage);
        $this->file->move($permanentStorage);
    }
}
