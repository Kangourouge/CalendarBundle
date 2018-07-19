# CalendarBundle

AppKernel
---------

```php
<?php

public function registerBundles()
{
    $bundles = array(
        new KRG\CalendarBundle\KRGCalendarBundle()
    );
}
```

Configuration
-------------

```yaml
# app/config/config.yml

twig:
    form_themes:
        - 'KRGCalendarBundle:Form:appointment.html.twig'
        - 'KRGCalendarBundle:Form:event.html.twig'
        - 'KRGCalendarBundle:Form:slot.html.twig'
        
doctrine:
    orm:
        resolve_target_entities:
            KRG\CalendarBundle\Entity\SlotInterface: AppBundle\Entity\Slot
            KRG\CalendarBundle\Entity\AppointmentInterface: AppBundle\Entity\Appointment
```
