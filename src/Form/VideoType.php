<?php

namespace App\Form;

use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre',TextType::class,[
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Ajouter un titre...'
                ]
            ])
            ->add('lienVideo',TextType::class,[
                'label' => 'Lien de la vidéo',
                'attr' => [
                    'placeholder' => 'Ajouter le lien de la vidéo...'
                ]
            ])
            ->add('description',TextareaType::class,[
                'label' => 'Déscription',
                'attr' => [
                    'placeholder' => 'Ajouter une désciption'
                ]
            ])
            ->add('source',TextType::class,[
                'label' => 'Source',
                'attr' => [
                    'placeholder' => 'Ajouter le nom de la chaîne youtube ...'
                ]
            ])
            ->add('lienSource',TextType::class,[
                'label' => 'Lien Url de la source',
                'attr' => [
                    'placeholder' => 'ajouter le lien url de votre source'
                ]
            ])
            ->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
