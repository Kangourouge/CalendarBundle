<?php

namespace KRG\CalendarBundle\Form\Type;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class SlotRangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('day', HiddenType::class)
            ->add('meridiem', HiddenType::class);

        $builder->addEventListener(FormEvents::POST_SET_DATA, [$this, 'onPostSetData']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $day = ceil(((int)$view->vars['name'] + 1) / 2) % 7;

        $view->vars['day'] = date('l', strtotime(sprintf('Sunday +%s days', $day)));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'translation_domain' => 'KRGCalendarBundle',
           'label_format'       => 'form.range.%name%',
           'am_start'     => 7,
           'am_end'       => 13,
           'pm_start'     => 13,
           'pm_end'       => 22,
        ]);
    }

    public function onPostSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $choices = self::getChoices(
            $form->get('meridiem')->getData(),
            $form->getConfig()->getOption('am_start'),
            $form->getConfig()->getOption('am_end'),
            $form->getConfig()->getOption('pm_start'),
            $form->getConfig()->getOption('pm_end')
        );

        $form
            ->add('start', ChoiceType::class, [
                'choices'           => $choices,
                'choices_as_values' => true,
                'required'          => false,
                'label'             => false,
                'placeholder'       => 'form.range.start',
            ])
            ->add('end', ChoiceType::class, [
                'choices'           => $choices,
                'choices_as_values' => true,
                'required'          => false,
                'label'             => false,
                'placeholder'       => 'form.range.end',
            ]);
    }

    private static function getChoices($meridiem, $amStart, $amEnd, $pmStart, $pmEnd)
    {
        $range = $meridiem === 'am' ? range($amStart, $amEnd, 0.5) : range($pmStart, $pmEnd, 0.5);
        $choices = [];
        foreach ($range as $time) {
            $hour = floor($time);
            $hour = str_pad($hour, 2, '0', STR_PAD_LEFT);
            $choices[] = sprintf('%s:%s', $hour, floor($time) === $time ? '00' : '30');
        }

        return array_combine($choices, $choices);
    }
}
