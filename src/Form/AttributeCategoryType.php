<?php

namespace App\Form;

use App\Entity\AttributeCategory;
use App\Entity\AttributeCategoryModel;
use App\Entity\Attributes;
use App\Entity\Category;
 
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AttributeCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('attribute', EntityType::class, [
                'class' => Attributes::class,
                'required'=>true,
                'mapped'=>true,
                'placeholder'=>'Veuillez choisir une valeur',
                'attr'=>array('class'=>'form-control mb-3','placeholder'=>'Prénom'),
                'label' => 'Attribut',
            ])
            ->add('category', ChoiceType::class, [
                'required'=>true,
                'placeholder'=>'Veuillez choisir une valeur',
                'attr'=>array('class'=>'form-control mb-3','placeholder'=>''),
                'label' => 'Catégorie',

                'choices' => $options['categories'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AttributeCategoryModel::class,
            'categories'=> []
        ]);
    }
}
