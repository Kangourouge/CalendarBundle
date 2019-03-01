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

class SlotDayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('am', SlotRangeType::class, ['meridiem' => 'am', 'required' => false])
            ->add('pm', SlotRangeType::class, ['meridiem' => 'pm', 'required' => false]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $day = ceil(((int) $view->vars['name'] + 1) / 2) % 7;

        $view->vars['label'] = date('l', strtotime(sprintf("Sunday +%s days", $day)));
    }
}
