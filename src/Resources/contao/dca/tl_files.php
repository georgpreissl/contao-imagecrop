<?php



use Contao\File;




array_insert($GLOBALS['TL_DCA']['tl_files']['list']['operations'], 1, array
	(
		'imagecrop' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_files']['imagecrop'],
				'href'                => 'key=imagecrop',
				'icon'                => 'bundles/georgpreisslimagecrop/icons/crop.svg',
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
			//return ''; enable for all backend users
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
