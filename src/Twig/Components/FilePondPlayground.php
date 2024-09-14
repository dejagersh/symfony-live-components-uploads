<?php

namespace App\Twig\Components;

use App\TemporaryFile;
use App\WithFileUploads;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class FilePondPlayground
{
    use DefaultActionTrait;
    use WithFileUploads;

    #[LiveProp]
    public ?TemporaryFile $file = null;

    #[LiveProp]
    public ?TemporaryFile $fileTwo = null;
}
