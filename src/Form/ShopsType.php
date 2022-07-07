<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\City;
use App\Entity\ShopCategory;
use App\Entity\Shops;
use App\Entity\Town;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ShopsType extends AbstractType
{
    private $categoryRepository;
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder 
           

            ->add('shopBanner', FileType::class, [
                'required'=> $options['edit'] == null ? true : false,
                'label' => 'Bannière Web (1920 X 400) *',
                'mapped' => false,
                
                'attr'=>array('class'=>'form-control   mb-4 form-control-lg form-control-solid','placeholder'=>'', 'accept'=>'images/*' ),
                'constraints' => [
                    new File([
                        
                    ])
                ],
            ])
             
            ->add('logoURL', FileType::class, [
                'required'=> $options['edit'] == null ? true : false,
                'label' => 'Image de profile du shop *',
                'mapped' => false, 
                'attr'=>array('class'=>'form-control   mb-4 form-control-lg form-control-solid','placeholder'=>'', 'accept'=>'images/*' ),
                'constraints' => [
                    new File([
                        
                    ])
                ],
            ])
        
             




            ->add('label',TextType::class, array(
                'label' => "Nom de shop",
                'attr'=>array('class'=>'form-control mb-3','placeholder'=>'')
            ))
            ->add('email',EmailType::class, array(
                'label' => "Email professionnelle *",
                'attr'=>array('class'=>'form-control mb-3','placeholder'=>'')
            ))
            ->add('link',TextType::class, array(
                'label' => "Lien : (site web, facebook, instegram,...)",
                'attr'=>array('class'=>'form-control mb-3','placeholder'=>'')
            ))
            ->add('description',TextareaType::class, array(
                'label' => "Description",
                'attr'=>array('class'=>'form-control mb-3','placeholder'=>'')
            ))
            

            ->add('phone',TextType::class, array(
                'label' => "Numéro de téléphone *",
                'attr'=>array('class'=>'form-control mb-3','placeholder'=>'')
            ))
            ->add('fixPhone',TextType::class, array(
                'label' => "Téléphone fixe",
                'attr'=>array('class'=>'form-control mb-3','placeholder'=>'')
            ))
            ->add('whatsAppNumber',TextType::class, array(
                'label' => "WhatsUp",
                'attr'=>array('class'=>'form-control mb-3','placeholder'=>'')
            ))
             
             
            ->add('town', EntityType::class, [
                'class' => Town::class,
                'required'=>true,
                'placeholder'=>'Veuillez choisir une valeur',
                'attr'=>array('class'=>'form-control mb-3' ),
                'label' => 'Selectionnez le governerat *',
            ])

            ->add('categories', EntityType::class , [ 
                
                'multiple' => true,
                'expanded' => true,
                'class' => Category::class, 
                'mapped' => false,

                'required'=>true,

                'placeholder'=>'Veuillez choisir une valeur',
                'attr'=>array('class'=>'form-control mb-3' ),
                'label' => 'Selectionnez la category (max : 3) : *',
                'choices' => $this->categoryRepository->findAllByParent(null),
                'row_attr'=>[
                    "class"=>"multiple-check-boxes"
                ],
                
            ])
             
             
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Shops::class,
            'edit'=>null
        ]);
    }
}
