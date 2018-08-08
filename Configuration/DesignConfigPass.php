<?php

namespace KRG\CalendarBundle\Configuration;

use EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigPassInterface;
use KRG\EasyAdminExtensionBundle\Configuration\DesignConfigPass as KrgDesignConfigPass;

class DesignConfigPass implements ConfigPassInterface
{
    public function process(array $backendConfig)
    {
        if (class_exists('KRG\EasyAdminExtensionBundle\Configuration\DesignConfigPass')) {
            $backendConfig = KrgDesignConfigPass::addCssFile('/bundles/krgcalendar/css/fullcalendar.min.css', $backendConfig);
            $backendConfig = KrgDesignConfigPass::addCssFile('/bundles/krgcalendar/css/slot.css', $backendConfig);
            $backendConfig = KrgDesignConfigPass::addJsFile('/bundles/krgcalendar/js/moment.min.js', $backendConfig);
            $backendConfig = KrgDesignConfigPass::addJsFile('/bundles/krgcalendar/js/fullcalendar.min.js', $backendConfig);
            $backendConfig = KrgDesignConfigPass::addJsFile('/bundles/krgcalendar/js/locale-all.js', $backendConfig);
            $backendConfig = KrgDesignConfigPass::addJsFile('/bundles/krgcalendar/js/jquery.serializejson.min.js', $backendConfig);
        }

        return $backendConfig;
    }
}
