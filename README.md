# CalendarBundle

AppKernel
---------

```
<?php

public function registerBundles()
{
    $bundles = array(
        // ...
        new KRG\CalendarBundle\KRGCalendarBundle()
        // ...
    );
}
```

Configuration
-------------

```
# app/config/config.yml
doctrine:
    orm:
        resolve_target_entities:
            KRG\CalendarBundle\Entity\SlotInterface: AppBundle\Entity\Slot
            KRG\CalendarBundle\Entity\AppointmentInterface: AppBundle\Entity\Appointment
```
