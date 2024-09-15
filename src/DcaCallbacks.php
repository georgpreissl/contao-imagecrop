<?php



namespace GeorgPreissl\ImageCrop;


use Contao\System;
use Contao\File;
use Contao\StringUtil;
use Contao\Image;
use Contao\Backend;

class DcaCallbacks extends Backend
{






	public function getCropperIcon($row, $href, $label, $title, $icon, $attributes)
	{


		$strDecoded = urldecode($row['id']);


		$rootDir = System::getContainer()->getParameter('kernel.project_dir');


		if (is_dir($rootDir . '/' . $strDecoded))
		{
			return '';
		}

		$objFile = new File($strDecoded);

		if (!in_array($objFile->extension, array('jpg','jpeg','png','gif')))
		{
			return '';
		}
		return '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'. StringUtil::specialchars($title).'"'.$attributes.'>'.  Image::getHtml($icon, $label)         .'</a> ';
	}
	




}
