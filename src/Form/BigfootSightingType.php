<?php

namespace App\Form;

use App\Entity\BigFootSighting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BigfootSightingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title')
            ->add('description')
            ->add('latitude')
            ->add('longitude')
            ->add('images', TextareaType::class, [
                'help' => 'Add the URL where the images live separated by commas',
                'required' => false
            ]);

        $builder->get('images')->addModelTransformer(new CallbackTransformer(
            function ($images) {
                if (!$images) {
                    return '';
                }

                // transform the array to a string
                return implode(', ', $images);
            },
            function ($images) {
                if (!$images) {
                    return [];
                }

                // transform the string back to an array
                return explode(', ', $images);
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BigFootSighting::class
        ]);
    }
}
