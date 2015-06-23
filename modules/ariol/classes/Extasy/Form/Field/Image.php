<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Form_Field_Image extends Form_Field
{
	protected $_upload_dir = NULL;
	protected $_allowed_extensions = array('gif', 'jpeg', 'jpg', 'png');
	protected $_denied_extensions = array();
	
	protected $_input_template = 'form/field/image';
	
	protected $_file_errors = array();
	
	protected $_old_value = NULL;
	
	public function __construct(array $field = array())
	{
		parent::__construct($field);
		$this->_upload_dir = Arr::get($field, 'upload_dir', $this->_upload_dir);
		$this->_old_value = $this->_value;
	}
	
	
	/**
	 * @TODO: NEED REFACTORING!!!
	 * 
	 * (non-PHPdoc)
	 * @see modules/extasy/classes/extasy/form/Extasy_Form_Field#prepare_to_populate($value)
	 */
	public function prepare_to_populate($value)
	{
		$old_file = $this->_old_value ? PUBLIC_ROOT.'/'.$this->_old_value : NULL;
		$new_value = $this->_old_value;
		if(Arr::get($value, 'delete'))
		{
			if($old_file AND file_exists($old_file))
			{
				unlink($old_file);
			}
			$new_value = '';
		}

		if($_FILES[$this->get_name()]['tmp_name']['file'] == '')
		{
			return $new_value;
		}
		
		$fileinfo = $_FILES[$this->get_name()];
		$new_name = $fileinfo['name']['file'];
		$destination_pref = PUBLIC_ROOT.'/'.$this->_upload_dir;
		
		if(file_exists($destination_pref.'/'.$new_name))
		{
			$i = 1;
			while(file_exists($destination_pref.'/'.$i.'_'.$new_name))
			{
				$i++;
			}
			$new_name = $i.'_'.$new_name;
		}
		
		if( ! move_uploaded_file($fileinfo['tmp_name']['file'], $destination_pref.'/'.$new_name))
		{
			$this->_file_errors[] = 'cant_upload';
			return $new_value;
		}
		
		$info = pathinfo($destination_pref.'/'.$new_name);
		$ext = $info['extension'];
		if( ! empty($this->_allowed_extensions))
		{
			if( ! in_array($ext, $this->_allowed_extensions))
			{
				$this->_file_errors[] = 'ext_not_allowed';
				unlink($destination_pref.'/'.$new_name);
				return $new_value;
			}
		}
		
		if(in_array($ext, $this->_denied_extensions))
		{
			$this->_file_errors[] = 'ext_not_allowed';
			unlink($destination_pref.'/'.$new_name);
			return $new_value;
		}
		
		$new_value = $this->_upload_dir.'/'.$new_name;
		
		if($old_file AND file_exists($old_file))
		{
			unlink($old_file);
		}
		
		return $new_value;
	}
	
	public function validate(Validate $array, $field)
	{
		if(empty($this->_file_errors))
		{
			return TRUE;
		}
		foreach($this->_file_errors as $error)
		{
			$array->error($field, $error);
		}
		return FALSE;
	}
}