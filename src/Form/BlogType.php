<?php

namespace App\Form;

use App\Commands\BlogCreate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class BlogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'help' => 'Our help message',
                'constraints' => [
                    new Assert\Length(['min' => 5, 'max' => 100])
                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BlogCreate::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
