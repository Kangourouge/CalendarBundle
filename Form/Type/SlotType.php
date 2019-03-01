<?php

namespace KRG\CalendarBundle\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use KRG\CalendarBundle\Entity\Slot;
use KRG\CalendarBundle\Entity\SlotInterface;
use KRG\CalendarBundle\Form\DataTransformer\SlotRangeDataTransformer;
use Sonata\CoreBundle\Form\Type\DatePickerType;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SlotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startAt', DateTimePickerType::class)
            ->add('endAt', DateTimePickerType::class)
            ->add('range', SlotRangeCollectionType::class)
            ->add('duration', ChoiceType::class, array(
                'choices'  => static::getDurationChoices(new \DateInterval($options['duration_interval']), new \DateInterval($options['duration_max_interval'])),
                'required' => false,
                'placeholder' => 'Unlimited'
            ))
            ->add('capacity', IntegerType::class)
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => SlotInterface::class,
            'label_format'      => 'form.slot.%name%',
            'duration_interval' => 'PT15M',
            'duration_max_interval' => 'PT2H',
            'cascade_validation'=> true,
        ));
    }

    protected static function getDurationChoices(\DateInterval $interval, \DateInterval $maxInterval) {
        $startAt = new \DateTime();
        $endAt = clone $startAt;

        $endAt->add($maxInterval);

        $period = new \DatePeriod($startAt, $interval, $endAt);

        $choices = [];

        foreach($period as $idx => $datetime) {

            $diff = $startAt->diff($datetime);
            $intervals = ['y' => ['%dY', '%d year(s)'], 'm' => ['%dM' => '%d month(s)'], 'd' => ['%dD', '%d day(s)'], 'h' => ['T%dH', '%dh'], 'i' => ['T%dM', '%d min'], 's' => ['T%dS', '%ds']];

            $choice = $label = '';
            foreach($intervals as $key => $value) {
                if ($diff->{ $key } === 0) {
                    continue;
                }
                $choice .= sprintf($value[0], $diff->{ $key });
                $label .= sprintf(' ' . $value[1], $diff->{ $key });
            }
            unset($value);

            if (strlen($choice) === 0) {
                continue;
            }

            $choices[ 'P' . $choice ] = $label;
        }

        return $choices;
    }

    public function getName()
    {
        return 'slot';
    }
}
