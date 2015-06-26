<?php

/* 
 * Copyright (C) 2014 Евгений
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace jackkum\PHPCurses\Window\Element;

use jackkum\PHPCurses\Window;
use jackkum\PHPCurses\Colors;
use jackkum\PHPCurses\Logger;
use jackkum\PHPCurses\Window\Element;
use jackkum\PHPCurses\Window\Element\Button;

class Toolbar extends Element {
	
	const POSITION_TOP    = 0;
	const POSITION_BOTTOM = 1;
	const ALIGN_LEFT      = 0;
	const ALIGN_RIGHT     = 1;
	
	/**
	 * toolbar position
	 * @var integer
	 */
	protected $_position = self::POSITION_TOP;
	
	
	/**
	 * items aligment
	 * @var integer
	 */
	protected $_aligment = self::ALIGN_LEFT;
	
	/**
	 * background color
	 * @var integer
	 */
	protected $_background;
	
	/**
	 * child items
	 * @var array
	 */
	protected $_items = array();
	
	/**
	 * array of child elements
	 * @var array
	 */
	protected $_elements = array();
	
	/**
	 * 
	 * @param Window $parent
	 * @param integer $position
	 */
	public function __construct(Window $parent = NULL, $position = self::POSITION_TOP, $aligment = self::ALIGN_LEFT)
	{
		
		if(is_null($this->_style)){
			$this->_style = new Window\Style($this);
			$this->_style->setColorPair(
				Colors::setPair('defaultToolbar', Colors::WHITE, Colors::CYAN)
			);
			
			$this->_style->setBorders(FALSE);
		}
		
		// toolbar position
		$this->_position = $position;
		// toolbar aligment elemetns
		$this->_aligment = $aligment;
		
		parent::__construct($parent);
	}
	
	public function onResize()
	{
		parent::onResize();
		
		// calc positions
		$this->_calculatePosition();
	}

		/**
	 * create toolbar
	 */
	public function create()
	{
		// calc positions
		$this->_calculatePosition();
		
		// create window
		parent::create();
		
		$style = $this->getStyle();
		
		if($this->_aligment == self::ALIGN_LEFT){
			// left offset
			$left = $style->getOffsetLeft();
			$top  = $style->getOffsetTop();
			foreach($this->_items as $item){
				$button = $this->_createItem($item);
				$width  = $button->getButtonWidth();
				
				$button->getStyle()->setRect(1, $width, $left, $top);
				
				$left += $width + 1;
			}
		} else {
			// left offset
			$left = $style->getOffsetLeft() + $style->getCols();
			$top  = $style->getOffsetTop();
			foreach(array_reverse($this->_items) as $item){
				$button = $this->_createItem($item);
				$width  = $button->getButtonWidth();
				$left  -= $width;
				$left   = $left < 1 ? 1 : $left;
				
				$button->getStyle()->setRect(1, $width, $left, $top);
				
				if( ! --$left) { break; }
			}
		}
	}
	
	/**
	 * add new item
	 * @param array $item
	 */
	public function addItem(array $item)
	{
		$this->_items[] = $item;
	}
	
	/**
	 * getting child element by name
	 * @param string $name
	 * @return NCElement|null
	 */
	public function & getItem($name)
	{
		return $this->_elements[$name];
	}
	
	private function _createItem(array $item)
	{
		$name   = isset($item['name'])   ? $item['name']   : NULL;
		$text   = isset($item['text'])   ? $item['text']   : NULL;
		$method = isset($item['method']) ? $item['method'] : NULL;
		
		$this->_elements[$name] = new Button($text, array($this->getParentWindow(), $method), $this);
		
		return $this->_elements[$name];
	}
	
	/**
	 * no title toobar
	 * @param string $title
	 */
	public function setTitle($title = NULL)
	{
		// no title
	}

	/**
	 * calculate position toolbar
	 */
	private function _calculatePosition()
	{
		// get paren window
		$parent = $this->getParentWindow();
		// refresh window size
		$parent->onResize();
		// get current style
		$style = $this->getStyle();
		// width toolbar
		$style->setCols($parent->getStyle()->getCols() - 2);
		$style->setRows(1);
		$style->setOffsetLeft($parent->getStyle()->getOffsetLeft() + 1);
		switch($this->_position){
			case self::POSITION_TOP:
				$style->setOffsetTop($parent->getStyle()->getOffsetTop() + 1);
				break;
			case self::POSITION_BOTTOM:
				$style->setOffsetTop(($parent->getStyle()->getOffsetTop() + $parent->getStyle()->getRows()) - 2);
				break;
		}
		
	}
	
}