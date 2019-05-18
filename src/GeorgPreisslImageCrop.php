<?php

namespace GeorgPreissl\IC;



use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Configures the Contao news bundle.
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class GeorgPreisslImageCrop extends Bundle
{


    public function compile()
    {
        return "jo";
    }

}
