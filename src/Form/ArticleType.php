<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Categorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre',TextareaType::class,[
                'label' => 'titre',
                'attr' => [
                 'placeholder' => 'Le titre de l\'article...'
                ]
            ])
            ->add('contenu',TextareaType::class,[
                'label' => 'Contenu',
                'attr' =>[
                    'placeholder' => 'Le contenu de l\'article...'
                ]
            ])
            ->add('source',TextType::class,[
                'label' => 'Source',
                'attr' => [
                    'placeholder' => 'Le nom de votre source...'
                ]
            ])
            ->add('lien',TextType::class,[
                'label' => 'Lien',
                'attr' => [
                    'placeholder' => 'Ajouter un lien du site de votre source'
                ]
            ])
            ->add('image_upload',FileType::class,[
                'label' => 'Ajouter une image',
                'mapped' => false
            ])
            ->add('categorie',EntityType::class,[
                'required' => false,
                'label' => 'Catégorie',
                'placeholder' => '-- Choisir une catégorie --',
                'class' => Categorie::class,
                'choice_label' => function(Categorie $categorie){
                    return strtoupper($categorie->getNom());
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
