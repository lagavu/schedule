<?php

namespace App\Form;

use App\Entity\Shedule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userId', IntegerType::class)
            ->add('start_date', DateType::class, array(
                'widget' => 'single_text',

                // предотвращает его отображение как type="date", чтобы избежать определителей даты HTML5
                'html5' => false,

                // добавляет класс, который может быть выбран в JavaScript
                'attr' => ['class' => 'js-datepicker'],
            ))
            ->add('end_date', DateType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
          //  'data_class' => Shedule::class,
        ]);
    }
}
