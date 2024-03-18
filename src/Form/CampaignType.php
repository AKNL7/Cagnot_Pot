<?php

namespace App\Form;

use App\Entity\Campaign;
// use Doctrine\DBAL\Types\IntegerType;
// use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
// use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CampaignType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options ): void
    {
        $builder

            ->add('title', TextType::class, [
                'attr' => [
                    'class' => 'validate'
                ]
            ])
            ->add('content', TextType::class, [
                'attr' => [
                    'class' => 'materialize-textarea'
                ]
               
            ])
            ->add('goal', IntegerType::class, [
                'attr' => [
                    'class' => 'validate'
                ]
            ])
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'validate'
                ]
            ])
          
            // ->add('created_at')
            // ->add('updated_at')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Campaign::class,
        ]);
    }
}
