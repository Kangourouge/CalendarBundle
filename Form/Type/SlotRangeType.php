<?php

namespace KRG\CalendarBundle\Form\Type;

use KRG\CalendarBundle\Form\DataTransformer\SlotRangeDataTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class SlotRangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $choices = self::getChoices($options['meridiem']);

        $builder
            ->add('start', ChoiceType::class, array(
                'choices'           => $choices,
                'choices_as_values' => true,
                'required'          => false,
                'label'             => false,
                'placeholder'       => 'form.range.start',
            ))
            ->add('end', ChoiceType::class, array(
                'choices'           => $choices,
                'choices_as_values' => true,
                'required'          => false,
                'label'             => false,
                'placeholder'       => 'form.range.end',
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('meridiem');
        $resolver->setAllowedValues('meridiem', ['am', 'pm']);
    }

    private static function getChoices($meridiem)
    {
        $range = $meridiem === 'am' ? range(7, 13, 0.5) : range(13, 22, 0.5);
        $choices = array();
        foreach ($range as $time) {
            $hour = floor($time);

            $hour = str_pad($hour, 2, '0', STR_PAD_LEFT);
            $choices[] = sprintf('%s:%s', $hour, floor($time) === $time ? '00' : '30');
        }

        return array_combine($choices, $choices);
    }
}
