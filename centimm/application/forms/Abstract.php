<?php
class Application_Form_Abstract extends Zend_Form
{
	public function isValid($data)
	{
		$valid = true;
		foreach ($this->getElements() as $key => $element)
			if ($element instanceof Zend_Form_Element_Hidden)
			{
				$value = $this->getValue($key);
				if ($value && ($data[$key] != $value))
					$valid = false;
			}
		$parent = parent::isValid($data);
		
		if (!$valid)
			return false;
		
		return $parent;
	}
}
