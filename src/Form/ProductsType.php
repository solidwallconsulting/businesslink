<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\City;
use App\Entity\Products;
use App\Entity\Shops;
use App\Repository\ShopsRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductsType extends AbstractType
{



    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder 
            ->add('title',TextType::class, array(
                'label' => "Titre de l'annonce",
                'attr'=>array('class'=>'form-control mb-3','placeholder'=>'')
            ))
            ->add('description',TextType::class, array(
                'label' => "Description",
                'attr'=>array('class'=>'form-control mb-3','placeholder'=>'')
            ))
            ->add('mainPhoto', FileType::class, [
                
                'label' => 'Image principale',
                'mapped' => false,
                'required' => true,
                'attr'=>array('class'=>'form-control   mb-4 form-control-lg form-control-solid','placeholder'=>'', 'accept'=>'images/*' ),
                'constraints' => [
                    new File([
                        
                    ])
                ],
            ])
            ->add('secondPhoto', FileType::class, [
                
                'label' => 'autres image ( facultatif )',
                'mapped' => false,
                'required' => false,
                'attr'=>array('class'=>'form-control   mb-4 form-control-lg form-control-solid','placeholder'=>'', 'accept'=>'images/*' ),
                'constraints' => [
                    new File([
                        
                    ])
                ],
            ])
            ->add('thirdPhoto', FileType::class, [
                
                'label' => 'autres image ( facultatif )',
                'mapped' => false,
                'required' => false,
                'attr'=>array('class'=>'form-control   mb-4 form-control-lg form-control-solid','placeholder'=>'', 'accept'=>'images/*' ),
                'constraints' => [
                    new File([
                        
                    ])
                ],
            ])
            ->add('price') 
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'required'=>true,
                'placeholder'=>'Veuillez choisir une valeur',
                'attr'=>array('class'=>'form-control mb-3','placeholder'=>'Prénom'),
                'label' => 'Je suis un',
            ])
            ->add('city', EntityType::class, [
                'class' => City::class,
                'required'=>true,
                'placeholder'=>'Veuillez choisir une valeur',
                'attr'=>array('class'=>'form-control mb-3','placeholder'=>'Prénom'),
                'label' => 'Je suis un',
            ])

           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
