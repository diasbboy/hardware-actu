<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label'=> 'Email',
                'attr' => [
                    'placeholder' => 'Saisissez votre email...'
                ]
            ])

            ->add('sujet', TextType::class, [
                'label'=> 'Sujet',
                'attr' => [
                    'placeholder' => 'L\'objet de votre demande...'
                ]
            ])

            ->add('message', TextareaType::class, [
                'label'=> 'Message',
                'attr' => [
                    'placeholder' => 'Saisissez votre message...'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
