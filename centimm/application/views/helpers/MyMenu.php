<?php
//opakowuje linki w menu w dodatkowy span
class Zend_View_Helper_MyMenu extends Zend_View_Helper_Navigation_Menu
{

	public function myMenu(Zend_Navigation_Container $container = null)
	{
		return parent::menu($container);
	}

	/**
	 * (non-PHPdoc)
	 * @see library/Zend/View/Helper/Navigation/Zend_View_Helper_Navigation_Menu::htmlify()
	 */
	public function htmlify(Zend_Navigation_Page $page)
	{
		// get label and title for translating
		$label = $page->getLabel();
		$title = $page->getTitle();

		// translate label and title?
		if ($this->getUseTranslator() && $t = $this->getTranslator()) {
			if (is_string($label) && !empty($label)) {
				$label = $t->translate($label);
			}
			if (is_string($title) && !empty($title)) {
				$title = $t->translate($title);
			}
		}

		// get attribs for element
		$attribs = array(
            'id'     => $page->getId(),
            'title'  => $title,
            'class'  => $page->getClass()
		);

		// does page have a href?
		if ($href = $page->getHref()) {
			$element = 'a';
			$attribs['href'] = $href;
			$attribs['target'] = $page->getTarget();
		} else {
			$element = 'span';
		}

		return '<' . $element . $this->_htmlAttribs($attribs) . '>'
		. '<span>'
		. $this->view->escape($label)
		. '</span>'
		. '</' . $element . '>';
	}
}
