<?php 

use GeorgPreissl\ImageCrop\Controller\Cropper;

$section = 'system';
if(!array_key_exists('files', $GLOBALS['BE_MOD']['system'])) {
	$section = 'content';
}


$GLOBALS['BE_MOD'][$section]['files']['imagecrop'] = array(Cropper::class, 'cropImage');
