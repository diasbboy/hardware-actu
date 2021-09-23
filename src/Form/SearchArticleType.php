<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Search\SearchArticle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;


class SearchArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('filtrerParCategorie',EntityType::class,[
                'label' => 'Filtrer par catégorie',
                'required' => false,
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
            'data_class' => SearchArticle::class,
            'method' => 'get',
            'csrf_protection' => false
        ]);
    }
}
