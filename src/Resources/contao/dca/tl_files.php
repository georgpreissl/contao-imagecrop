<?php

namespace GeorgPreissl\ImageCrop;

use Contao\File;
use Contao\System;
use Contao\StringUtil;
use Contao\Image;
use Contao\Backend;



$GLOBALS['TL_DCA']['tl_files']['list']['operations']['imagecrop'] = array
(
	'label'               => &$GLOBALS['TL_LANG']['tl_files']['imagecrop'],
	'href'                => 'key=imagecrop',
	'icon'                => 'bundles/georgpreisslimagecrop/icons/crop.svg',
	'button_callback'     => array('\GeorgPreissl\ImageCrop\DcaCallbacks', 'getCropperIcon')
);


