<?php

/**
 * TYPOlight webCMS
 *
 * The TYPOlight webCMS is an accessible web content management system that 
 * specializes in accessibility and generates W3C-compliant HTML code. It 
 * provides a wide range of functionality to develop professional websites 
 * including a built-in search engine, form generator, file and user manager, 
 * CSS engine, multi-language support and many more. For more information and 
 * additional TYPOlight applications like the TYPOlight MVC Framework please 
 * visit the project website http://www.typolight.org.
 *
 * 
 *
 * @author     Georg Preissl <http://www.georg-preissl.at> 
 * @package    imagecrop
 * @author	   Cropper.js by Fengyuan Chen <http://chenfengyuan.com>       
 * @author	   based on the extension Moo_imagecropper by Lightive (erwan.ripoll)       
 * @license    GPL 
 * 
 */


/**
 * Palettes
 */


$GLOBALS['TL_DCA']['tl_settings']['palettes']['__selector__'][] = 'useImagecropARs';
$GLOBALS['TL_DCA']['tl_settings']['palettes']['__selector__'][] = 'useImagecropSizes';

$GLOBALS['TL_DCA']['tl_settings']['subpalettes']['useImagecropARs'] =  'imagecropAspectRatios';
$GLOBALS['TL_DCA']['tl_settings']['subpalettes']['useImagecropSizes'] =  'imagecropSizes';



$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] = $GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] . ';{imagecrop_legend:hide},useImagecropSizes,useImagecropARs';





$GLOBALS['TL_DCA']['tl_settings']['fields']['useImagecropARs'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings_imagecrop']['useImagecropARs'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true),
	'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['imagecropAspectRatios'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_settings_imagecrop']['imagecropAspectRatios'],
	'exclude' 		=> true,
	'inputType' 		=> 'multiColumnWizard',
	'eval' 			=> array
	(
		'columnFields' => array
		(
			'imagecropArWidth' => array
			(
				'label'                 => &$GLOBALS['TL_LANG']['tl_settings_imagecrop']['imagecropArWidth'],
				'exclude'               => true,
				'inputType'             => 'text',
				'eval' 				    => array('mandatory'=>true, 'maxval'=>2000, 'minval'=>1, 'rgxp'=>'digit', 'nospace'=>true, 'style'=>'width:150px')
			),
			'imagecropArHeight' => array
			(
				'label' 		=> &$GLOBALS['TL_LANG']['tl_settings_imagecrop']['imagecropArHeight'],
				'inputType' 		=> 'text',
				'eval'                  => array('mandatory'=>true, 'maxval'=>2000, 'minval'=>1, 'rgxp'=>'digit', 'nospace'=>true, 'style'=>'width:150px')
			),
			'imagecropArHint' => array
			(
				'label' 		=> &$GLOBALS['TL_LANG']['tl_settings_imagecrop']['imagecropArHint'],
				'inputType' 		=> 'text',
				'eval'                  => array('style'=>'width:250px')
			)
		),
		'tl_class'=>'clr' 
	),
	'sql'                     => "blob NULL"
);





$GLOBALS['TL_DCA']['tl_settings']['fields']['useImagecropSizes'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings_imagecrop']['useImagecropSizes'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true),
	'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['imagecropSizes'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_settings_imagecrop']['imagecropSizes'],
	'exclude' 		=> true,
	'inputType' 		=> 'multiColumnWizard',
	'eval' 			=> array
	(
		'columnFields' => array
		(
			'imagecropWidth' => array
			(
				'label'                 => &$GLOBALS['TL_LANG']['tl_settings_imagecrop']['imagecropWidth'],
				'exclude'               => true,
				'inputType'             => 'text',
				'eval' 				    => array('mandatory'=>true, 'maxval'=>2000, 'minval'=>1, 'rgxp'=>'digit', 'nospace'=>true, 'style'=>'width:150px')
			),
			'imagecropHeight' => array
			(
				'label' 		=> &$GLOBALS['TL_LANG']['tl_settings_imagecrop']['imagecropHeight'],
				'inputType' 		=> 'text',
				'eval'                  => array('mandatory'=>true, 'maxval'=>2000, 'minval'=>1, 'rgxp'=>'digit', 'nospace'=>true, 'style'=>'width:150px')
			),
			'imagecropHint' => array
			(
				'label' 		=> &$GLOBALS['TL_LANG']['tl_settings_imagecrop']['imagecropHint'],
				'inputType' 		=> 'text',
				'eval'                  => array('style'=>'width:250px')
			)
		),
		'tl_class'=>'clr' 
	),
	'sql'                     => "blob NULL"
);