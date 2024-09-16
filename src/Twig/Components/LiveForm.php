<?php

namespace App\Twig\Components;

use App\Form\LiveFilepondType;
use App\ComponentWithLiveUploads;
use App\TemporaryFile;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class LiveForm
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentWithLiveUploads;

    public function __construct(
        private FormFactoryInterface $formFactory,
        private FilesystemOperator $permanentStorage,
    ) {
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->createBuilder()
            ->add('file', LiveFilepondType::class, [
                'live' => true,
            ])
            ->add('someText', TextType::class)
            ->getForm();
    }

    #[LiveAction]
    public function submit(): void
    {
        $this->submitForm();

        /** @var ?TemporaryFile $file */
        $file = $this->getForm()->get('file')->getData();

        if ($file) {
            $file->move($this->permanentStorage);

            dd('Yup, that worked!');
        } else {
            dd('Please upload a file :(');
        }
    }
}
