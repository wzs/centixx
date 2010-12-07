<?php
class Centixx_Form_AddItem extends Zend_Form
{
	protected $_items;
	public $itemLabel = '';
	public $submitLabel = 'Przypisz';

	public function rebuild()
	{
		$this->setMethod(self::METHOD_POST);

		$this->addElement('select', 'new_item', array(
			'label'		=> $this->itemLabel,
			'required'	=> true,
			'multiOptions' => $this->_items,
		));

		$this->addelement('submit', 'submit', array(
			'label'		=> $this->submitLabel,
			'ignore'	=> true,
		));
	}

	public function setValues($array) {
		if (array_key_exists('items', $array)) {
			$this->_items = $array['items'];
		}
		$this->rebuild();
		return $this;
	}

	public function hasItems() {
		return count($this->_items) !== 0;
	}
}