<?php

namespace App\Form;

use App\Entity\Album;
use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ...
            ->add('category', EntityType::class, [
              // looks for choices from this entity
              'class' => Album::class,
              'required' => true,
              // uses the User.username property as the visible option string
              'choice_label' => 'name',

            ])
            ->add('brochure', FileType::class, [
                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
                'attr' => array('accept' => 'image/jpeg,image/png,image/gif,image/jpg'),
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'constraints' => [
                new All([
                    'constraints' => [
                      new File([
                        'maxSize' => '10M',
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                        'mimeTypes' => [
                          'image/*',
                        ]
                      ]),
                    ],
                  ]),
                  ]
                ])

            // ...
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}