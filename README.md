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

```yaml
# app/config/routing.yml

krg_calendar_bundle:
    resource: "@KRGCalendarBundle/Controller/"
    type:     annotation
    prefix:   /
```

```yaml
# app/config/admin.yml

imports:
    - { resource: '@KRGCalendarBundle/Resources/config/easyadmin.yml' }
```

## EasyAdmin calendar

Menu item

````
# easyadmin.yml

easy_admin:
    design:
        menu:
            - { route: 'admin_appointment_show', label: 'Rendez-vous' }
````

Define custom Calendar Model (event fetcher)

```yaml
# services.yml

KRG\CalendarBundle\Controller\Admin\AppointmentController:
    calls:
        - [setCalendarModel, ['AppBundle\Calendar\Model\AppointmentModel']]
        - [setOptions, [{ editable: true }]]
```
