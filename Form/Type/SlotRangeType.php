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
        $builder
            ->add('day', HiddenType::class)
            ->add('meridiem', HiddenType::class);

        $builder->addEventListener(FormEvents::POST_SET_DATA, array($this, 'onPostSetData'));
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        parent::finishView($view, $form, $options);

        $day = ceil(((int)$view->vars['name'] + 1) / 2) % 7;
        $view->vars['attr']['class'] = sprintf('inline-form opening-form range-%d', $day);
        foreach ($view->children as $name => &$children) {
            if ($name === 'start' && $view->children['meridiem']->vars['value'] === 'am') {
                $children->vars['label'] = 'form.range.'.$day;
            }
            $children->vars['widget_only'] = true;
        }
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

    public function onPostSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $meridiem = $form->get('meridiem')->getData();
        $choices = self::getChoices($meridiem);

        $form
            ->add('start', ChoiceType::class, array(
                'choices'     => $choices,
                'required'    => false,
                'label'       => false,
                'placeholder' => 'form.range.start',
            ))
            ->add('end', ChoiceType::class, array(
                'choices'     => $choices,
                'required'    => false,
                'label'       => false,
                'placeholder' => 'form.range.end',
            ));
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
