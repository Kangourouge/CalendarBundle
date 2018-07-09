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
doctrine:
    orm:
        resolve_target_entities:
            KRG\CalendarBundle\Entity\SlotInterface: AppBundle\Entity\Slot
            KRG\CalendarBundle\Entity\AppointmentInterface: AppBundle\Entity\Appointment
```
