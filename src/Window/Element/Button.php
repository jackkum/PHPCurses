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
use jackkum\PHPCurses\Window\Element;

class Button extends Element {
	
	protected $_text;
	protected $_callback;
	
	public function __construct($text, $callback, Window & $parent = NULL)
	{
		$this->_text     = $text;
		$this->_callback = $callback;
		
		if(is_null($this->_style)){
			$this->_style = new Window\Style($this);
			$this->_style->setColorPair(
				Colors::setPair('defaultButton', Colors::WHITE, Colors::RED)
			);
			
			$this->_style->setBorders(FALSE);
		}
		
		$this->_style->setRows(1);
		$this->_style->setCols($this->getButtonWidth());
		
		parent::__construct($parent);
	}
	
	public function getButtonWidth()
	{
		return mb_strlen($this->_text) + 2;
	}
	
	public function refresh() 
	{
		// will clear window
		parent::refresh();
		
		$this->write($this->_text, 0, 1);
		ncurses_wrefresh($this->getWindow());
	}
	
	public function onMouseClick($top, $left)
	{
		if($this->isCollision($top, $left)){
			call_user_func($this->_callback);
			return TRUE;
		}
		
		return FALSE;
	}
	
}