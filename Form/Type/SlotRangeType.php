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

        $builder->addEventListener(FormEvents::POST_SET_DATA, array($this, 'onPostSetData'));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $day = ceil(((int)$view->vars['name'] + 1) / 2) % 7;

        $view->vars['day'] = date('l', strtotime(sprintf('Sunday +%s days', $day)));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'by_reference'       => false,
            'translation_domain' => 'messages',
            'label_format'       => 'form.range.%name%',
        ));
    }

    public function getName()
    {
        return 'slot_range';
    }

    /* EVENTS */

    public function onPostSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $meridiem = $form->get('meridiem')->getData();
        $choices = self::getChoices($meridiem);

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

    private static function getChoices($meridiem)
    {
        $range = $meridiem === 'am' ? range(7, 13, 0.5) : range(13, 22, 0.5);
        $choices = [];
        foreach ($range as $time) {
            $hour = floor($time);

            $hour = str_pad($hour, 2, '0', STR_PAD_LEFT);
            $choices[] = sprintf('%s:%s', $hour, floor($time) === $time ? '00' : '30');
        }

        return array_combine($choices, $choices);
    }
}
