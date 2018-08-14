<?php

namespace KRG\CalendarBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

class KRGCalendarExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('krg_calendar.colors', $config['calendar']['colors'] ?? []);
    }

    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('twig', [
            'form_themes' => [
                'KRGCalendarBundle:form:appointment.html.twig',
                'KRGCalendarBundle:form:event.html.twig',
                'KRGCalendarBundle:form:range.html.twig',
            ]
        ]);
    }
}
