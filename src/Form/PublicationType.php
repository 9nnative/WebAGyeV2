<?php

namespace App\Form;

use App\Entity\Album;
use App\Entity\Category;
use App\Entity\Publication;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PublicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            
            ->add('Content', CKEditorType::class)

            ->add('title')
            
            ->add('category', EntityType::class, [
                // looks for choices from this entity
                'class' => Category::class,
                'required' => true,
                // uses the User.username property as the visible option string
                'choice_label' => 'name',
  
              ])
            ->add('album', EntityType::class, [
                'class' => Album::class,
                'multiple' => true,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Publication::class,
        ]);
    }
}
