<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

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




array_insert($GLOBALS['TL_DCA']['tl_files']['list']['operations'], 1, array
	(
		'imagecrop' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_files']['imagecrop'],
				'href'                => 'key=imagecrop',
				'icon'                => 'system/modules/imagecrop/html/crop.svg',
				'button_callback'     => array('tl_imagecrop', 'getCropperIcon')
			)
	)
);


/**
 * Class tl_imagecrop
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 */

class tl_imagecrop extends tl_files
{


	/**
	 * Return the image_cropper button
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function getCropperIcon($row, $href, $label, $title, $icon, $attributes)
	{
		$this->import('BackendUser', 'User');

		if (!$this->User->isAdmin && !in_array('f5', $this->User->fop))
		{
			return '';
		}

		$strDecoded = urldecode($row['id']);

		if (is_dir(TL_ROOT . '/' . $strDecoded))
		{
			return '';
		}

		$objFile = new File($strDecoded);

		if (!in_array($objFile->extension, array('jpg','jpeg','png','gif')))
		{
			return '';
		}
		return '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ';
	}
	
	
	
}


?>
