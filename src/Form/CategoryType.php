<?php

namespace App\Form;

use App\Entity\Category;
use Doctrine\ORM\Query\Expr\Func;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class, array(
                'label' => 'Category',
                'attr'=>array('class'=>'form-control mb-3','placeholder'=>'')
            ))
            ->add('icon', FileType::class, [
                'row_attr'=>array( 'class'=>'col-sm-4'),
                'label' => 'Icon',
                'mapped' => false,
                'required' => true,
                'attr'=>array('class'=>'form-control tg-fileinput  mb-4 form-control-lg form-control-solid','placeholder'=>'', 'accept'=>'images/*' ),
                'constraints' => [
                    new File([
                        
                    ])
                ],
            ])
            ->add('parent',  EntityType::class, [
                'required' => false,
                'class' => Category::class,
                'placeholder'=>'Veuillez choisir une valeur',
                'attr'=>array('class'=>'form-control mb-3','placeholder'=>''),
                'label' => 'Parent',
                
                 
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
