<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\City;
use App\Entity\ProductAddModel;
use App\Entity\Products;
use App\Entity\Shops;
use App\Entity\Town;
use App\Repository\ShopsRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductAddType extends AbstractType
{

    private $shopsRepository;
    public function __construct(ShopsRepository $shopsRepository)
    {
        $this->shopsRepository = $shopsRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder 
             
            ->add('title',TextType::class, array(
                'label' => "Titre de l'annonce",
                'attr'=>array('class'=>'form-control mb-3','placeholder'=>'')
            ))
            ->add('description',TextareaType::class, array(
                'label' => "Description",
                'attr'=>array('class'=>'form-control mb-3','placeholder'=>'')
            ))
            ->add('mainPhoto', FileType::class, [
                'row_attr'=>array( 'class'=>'col-sm-4'),
                'label' => 'Image principale',
                'mapped' => false,
                'required' => $options['isEditing'] ==false ? true : false,
                'attr'=>array('class'=>'form-control tg-fileinput  mb-4 form-control-lg form-control-solid','placeholder'=>'', 'accept'=>'images/*' ),
                'constraints' => [
                    new File([
                        
                    ])
                ],
            ])
            ->add('secondPhoto', FileType::class, [
                'row_attr'=>array( 'class'=>'col-sm-4'),
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
                'row_attr'=>array( 'class'=>'col-sm-4'),
                'label' => 'autres image ( facultatif )',
                'mapped' => false,
                'required' => false,
                'attr'=>array('class'=>'form-control   mb-4 form-control-lg form-control-solid','placeholder'=>'', 'accept'=>'images/*' ),
                'constraints' => [
                    new File([
                        
                    ])
                ],
            ])
            ->add('price',IntegerType::class, array(
                'label' => "Prix",
                'attr'=>array('class'=>'form-control mb-3','placeholder'=>'')
            ))
            ->add('category', ChoiceType::class, [
                'required'=>true,
                'placeholder'=>'Veuillez choisir une valeur',
                'attr'=>array('class'=>'form-control mb-3','placeholder'=>''),
                'label' => 'Catégorie',

                'choices' => $options['categories'],
            ])


            ->add('town', EntityType::class, [
                'class' => Town::class,
                'required' => $options['isEditing'] ==false ? true : false,
                'placeholder'=>'Veuillez choisir une valeur',
                'attr'=>array('class'=>'form-control mb-3','placeholder'=>''),
                'label' => 'Gouvernorat',
            ])

            ->add('city',  EntityType::class, [
                'class' => City::class,
                'placeholder'=>'Veuillez choisir une valeur',
                'attr'=>array('class'=>'form-control mb-3','placeholder'=>''),
                'label' => 'Délégation',
                 
            ])


            ->add('shop', EntityType::class, [
                'class' => Shops::class,
                'choices' => $options['shops'],
                'required'=>false,
                'placeholder'=>'Veuillez choisir une valeur',
                'attr'=>array('class'=>'form-control mb-3','placeholder'=>'Prénom'),
                'label' => 'Mettre dans une de mes boutiques',
            ])

            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductAddModel::class,
            'categories' => [],
            "isEditing" => false,
            "shops"=>null
        ]);
    }
}
