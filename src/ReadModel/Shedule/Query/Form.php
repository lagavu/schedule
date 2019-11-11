<?php

declare(strict_types=1);

namespace App\ReadModel\Shedule\Query;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('userId', IntegerType::class, [
                'required' => true,
            ])
            ->add('start_date', DateType::class, [
                'attr'=>['autocomplete' => 'off'],
                'required' => true,
                'widget' => 'single_text',
                'html5' => false,
            ])
            ->add('end_date', DateType::class, [
                'attr'=>['autocomplete' => 'off'],
                'required' => true,
                'widget' => 'single_text',
                'html5' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Query::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
