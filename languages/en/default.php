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


/**
 * Intro
 */
$GLOBALS['TL_LANG']['MSC']['imagecropHeadline'] = 'Crop the image';
$GLOBALS['TL_LANG']['MSC']['imagecropIntroduction'] = 'Use handles on the border to define the crop area.';

/**
 * Section headlines
 */
$GLOBALS['TL_LANG']['MSC']['imagecropData'] = 'Data';
$GLOBALS['TL_LANG']['MSC']['imagecropPreview'] = 'Preview';
$GLOBALS['TL_LANG']['MSC']['imagecropAspectRatio'] = 'Aspect ratios';
$GLOBALS['TL_LANG']['MSC']['imagecropSizes'] = 'Pixel sizes';
$GLOBALS['TL_LANG']['MSC']['imagecropTools'] = 'Tools';

/**
 * Section data
 */
$GLOBALS['TL_LANG']['MSC']['imagecropWidth'] = 'Width';
$GLOBALS['TL_LANG']['MSC']['imagecropHeight'] = 'Height';

/**
 * Submit buttons
 */
$GLOBALS['TL_LANG']['MSC']['imagecropUpdateImage'] = 'Save';
$GLOBALS['TL_LANG']['MSC']['imagecropSaveCopy'] = 'Save as new version';

/**
 * Credits
 */
$GLOBALS['TL_LANG']['MSC']['imagecropCredits'] = 'This extension uses the JavaScript library <a href="https://fengyuanchen.github.io/cropperjs/">Cropper.js</a> from <a href="http://chenfengyuan.com">Fengyuan Chen.</a>';

/**
 * Tooltips
 */
$GLOBALS['TL_LANG']['MSC']['imagecropToolMove'] = 'Move image or trim frame';
$GLOBALS['TL_LANG']['MSC']['imagecropToolCrop'] = 'Move or recreate the trim frame';
$GLOBALS['TL_LANG']['MSC']['imagecropToolZoomIn'] = 'Zoom into the picture';
$GLOBALS['TL_LANG']['MSC']['imagecropToolZoomOut'] = 'Zoom out of the picture';
$GLOBALS['TL_LANG']['MSC']['imagecropToolMoveLeft'] = 'Move the image 10px to the right';
$GLOBALS['TL_LANG']['MSC']['imagecropToolMoveRight'] = 'Move the image 10px to the left';
$GLOBALS['TL_LANG']['MSC']['imagecropToolMoveDown'] = 'Move the image 10px down';
$GLOBALS['TL_LANG']['MSC']['imagecropToolMoveUp'] = 'Move the image 10px up';
$GLOBALS['TL_LANG']['MSC']['imagecropToolReset'] = 'Reset';

?>