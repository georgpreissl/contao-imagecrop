<?php

namespace GeorgPreissl\ImageCrop\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use GeorgPreissl\ImageCrop\GeorgPreisslImageCrop;


class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(GeorgPreisslImageCrop::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}
