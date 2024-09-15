<?php

declare(strict_types=1);



namespace GeorgPreissl\ImageCrop\DependencyInjection;

// use InspiredMinds\ContaoFileUsage\Provider\FileUsageProviderInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ContaoFileUsageExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        (new YamlFileLoader($container, new FileLocator(__DIR__.'/../config')))->load('services.yaml');

        // $container->registerForAutoconfiguration(FileUsageProviderInterface::class)
        //     ->addTag('contao_file_usage.provider')
        // ;
    }
}
