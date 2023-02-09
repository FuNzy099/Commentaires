<?php

namespace App\Form;

use App\Entity\Opinion;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class OpinionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudonyme', TextType::class, [
                'label' => 'Pseudonyme :',
                'attr' => [
                    'placeholder' => 'Tapez votre pseudonyme'
                ]
            ])

            ->add('email', EmailType::class,[
                'label' => 'E-mail : ',
                'attr' => [
                    'placeholder' => 'Saisissez votre Email'
                ]
            ])

            ->add('note', IntegerType::class,[
                'label' => 'Note :',
                'attr' => [
                    'placeholder' => 'Note du produit :',
                    'min' => 0, 
                    'max' => 5
                ]
            ])

            ->add('comment', CKEditorType::class,[
                'purify_html' => true,
                'label' => 'commentaire :',
                'attr' => [
                    'placeholder' => 'Votre commantaire'
                ]
            ])

            ->add('picture', FileType::class, [
                'label' => 'photo :',
                'attr' => ['class' => "form-control previewAvatar", 'accept' => 'image/*', 'onchange' => 'showPreview(event)'  ],
                'data_class' => null,
                'required' => false,
                'mapped' => false,
            ])
            
            ->add('submit', SubmitType::class,[
                'label' => 'Valider',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Opinion::class,
        ]);
    }
}
