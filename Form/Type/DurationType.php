<?php

namespace KRG\CalendarBundle\Form\Type;

use EasyCorp\Bundle\EasyAdminBundle\Form\Type\EasyAdminGroupType;
use KRG\CalendarBundle\Entity\Slot;
use KRG\CalendarBundle\Entity\SlotInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class DurationType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'duration_interval'     => 'PT15M',
           'duration_max_interval' => 'PT2H',
        ]);

        $resolver->setNormalizer('choices', function(Options $options) {
            return self::getDurationChoices(new \DateInterval($options['duration_interval']), new \DateInterval($options['duration_max_interval']));
        });
    }

    protected static function getDurationChoices(\DateInterval $interval, \DateInterval $maxInterval)
    {
        $startAt = new \DateTime();
        $endAt = clone $startAt;
        $endAt->add($maxInterval);
        $period = new \DatePeriod($startAt, $interval, $endAt);

        $choices = [];
        foreach ($period as $idx => $datetime) {
            $diff = $startAt->diff($datetime);
            $intervals = [
                'y' => ['%dY', '%d year(s)'],
                'm' => ['%dM' => '%d month(s)'],
                'd' => ['%dD', '%d day(s)'],
                'h' => ['T%dH', '%dh'],
                'i' => ['T%dM', '%d min'],
                's' => ['T%dS', '%ds'],
            ];

            $choice = $label = '';
            foreach ($intervals as $key => $value) {
                if ($diff->{$key} === 0) {
                    continue;
                }
                $choice .= sprintf($value[0], $diff->{$key});
                $label .= sprintf(' '.$value[1], $diff->{$key});
            }
            unset($value);

            if (strlen($choice) === 0) {
                continue;
            }

            $choices[$label] = 'P'.$choice;
        }

        return $choices;
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
