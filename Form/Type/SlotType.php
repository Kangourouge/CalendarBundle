<?php

namespace KRG\CalendarBundle\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use KRG\CalendarBundle\Entity\Slot;
use KRG\CalendarBundle\Entity\SlotInterface;
use KRG\CalendarBundle\Form\DataTransformer\SlotRangeDataTransformer;
use Sonata\CoreBundle\Form\Type\DatePickerType;
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
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startAt', DatePickerType::class, array(
                'required' => true,
                'widget'   => 'single_text',
                'format'   => 'dd/MM/yyyy',
                'attr'     => array(
                    'data-date-format' => 'DD/MM/YYYY',
                    'class'            => 'datepicker',
                ),
            ))
            ->add('endAt', DatePickerType::class, array(
                'required' => true,
                'widget'   => 'single_text',
                'format'   => 'dd/MM/yyyy',
                'attr'     => array(
                    'data-date-format' => 'DD/MM/YYYY',
                    'class'            => 'datepicker',
                ),
            ))
            ->add('range', SlotRangeCollectionType::class, array(
                'entry_type' => SlotRangeType::class,
                'mapped'     => false,
                'label'      => false
            ))
            ->add('duration', ChoiceType::class, array(
                'required' => true,
                'choices'  => static::getDurationChoices(new \DateInterval($options['duration_interval']), new \DateInterval($options['duration_max_interval']))
            ))
            ->add('capacity', IntegerType::class)
        ;

        $builder->addEventListener(FormEvents::POST_SET_DATA, array($this, 'onPostSetData'));
        $builder->addEventListener(FormEvents::POST_SUBMIT, array($this, 'onPostSubmit'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $className = $this->entityManager->getClassMetadata(SlotInterface::class)->getName();

        $resolver->setDefaults(array(
            'data_class'        => $className,
            'label_format'      => 'form.slot.%name%',
            'duration_interval' => 'PT15M',
            'duration_max_interval' => 'PT2H',
            'cascade_validation'=> true,
        ));
    }

    public function onPostSetData(FormEvent $event)
    {
        /* @var $slot SlotInterface */
        $slot = $event->getData();
        $event->getForm()->get('range')->setData($this->getRange($slot));
    }

    public function onPostSubmit(FormEvent $event)
    {
        /* @var $slot SlotInterface */
        $slot = $event->getData();

        $range = $event->getForm()->get('range')->getData();
        $this->setRange($slot, $range);

        $event->setData($slot);
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

    private function getRange(Slot $slot = null)
    {
        $data = array();
        foreach (range(1, 7) as $day) {
            $data[$day] = array(
                'am' => array('day' => $day, 'meridiem' => 'am', 'start' => null, 'end' => null),
                'pm' => array('day' => $day, 'meridiem' => 'pm', 'start' => null, 'end' => null),
            );
        }

        $range = $slot !== null && $slot->getRange() ? array_replace_recursive($data, $slot->getRange()) : $data;
        $range = array_map('array_values', $range);
        $range = call_user_func_array('array_merge', $range);

        return $range;
    }

    private function setRange(Slot $slot, array $data)
    {
        $range = array();
        foreach ($data as $_data) {
            $day = $_data['day'];
            $meridiem = $_data['meridiem'];
            if (!isset($range[$day])) {
                $range[$day] = array();
            }
            if (!isset($range[$day])) {
                $range[$day][$meridiem] = array();
            }
            unset($_data['day']);
            unset($_data['meridiem']);
            $range[$day][$meridiem] = $_data;
        }

        $slot->setRange($range);
    }

    public function getName()
    {
        return 'slot';
    }
}
