<?php

namespace App\Form;

use App\Entity\AccountTypes;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           
            
            ->add('firstname',TextType::class, array(
                'label' => 'Nom',
                'attr'=>array('class'=>'form-control mb-3','placeholder'=>'Nom')
            ))
            ->add('lastname',TextType::class, array(
                'label' => 'prénom',
                'attr'=>array('class'=>'form-control mb-3','placeholder'=>'Prénom')
            ))

            ->add('type', EntityType::class, [
                'class' => AccountTypes::class,
                'required'=>true,
                'placeholder'=>'Veuillez choisir une valeur',
                'attr'=>array('class'=>'form-control mb-3','placeholder'=>'Prénom'),
                'label' => 'Type',
            ])

           
           

            ->add('phone',TelType::class, array(
                'label' => 'Numéro de téléphone',
                'attr'=>array('class'=>'form-control mb-3','placeholder'=>'Numéro de téléphone')
            ))
            ->add('address',TextType::class, array(
                'label' => 'adresse',
                'attr'=>array('class'=>'form-control mb-3','placeholder'=>'Adresse')
            ))

            


            
        ;

        
        if ($options['isEditing'] == false) {

            $builder
            ->add('email',EmailType::class, array(
                'label' => 'Email',
                'attr'=>array('class'=>'form-control mb-3','placeholder'=>'Email')
            ))->
            add('password',PasswordType::class, array(
                'label' => 'Mot de passe',
                'attr'=>array('class'=>'form-control mb-3','placeholder'=>'Mot de passe')
            ))
            ;
        }


        if ($options['adminEditing'] == true) {

            $builder
            ->add('isBlocked',CheckboxType::class, array(
                'required'=>false,
                'label' => "Empêcher l'utilisateur de publier du nouveau contenu",
                'attr'=>array('class'=>' d-block mb-3','placeholder'=>'')
            )) 
            

            ;
        }
    }
   
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'isEditing'=>false,
            'adminEditing' => false
        ]);
    }
}
