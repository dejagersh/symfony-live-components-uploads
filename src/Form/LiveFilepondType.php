<?php

namespace App\Form;

use App\Form\DataTransformer\TemporaryFileTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LiveFilepondType extends AbstractType
{
    public function __construct(private TemporaryFileTransformer $temporaryFileTransformer)
    {

    }

    public function getParent(): string
    {
        return TextType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(
            $this->temporaryFileTransformer
        );
    }

    public function getBlockPrefix(): string
    {
        return 'filepond_live';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'live' => false,
        ]);
        $resolver->setAllowedTypes('live', 'bool');
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void {
        $view->vars['live'] = $options['live'];
    }
}
