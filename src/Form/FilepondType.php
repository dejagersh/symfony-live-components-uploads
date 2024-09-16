<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class FilepondType extends AbstractType
{
    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['multipart'] = true;
    }

    public function getParent(): string
    {
        return FileType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'filepond';
    }
}
