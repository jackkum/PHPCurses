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

namespace jackkum\PHPCurses\Window;

use jackkum\PHPCurses\Colors;

abstract class Element extends Window {
	
	protected $_title = NULL;

	public function __construct(Window & $parent = NULL)
	{
		if(is_null($this->_colorPair)){
			// colors
			$this->_colorPair = Colors::setPair('defaultElement', Colors::WHITE, Colors::CYAN);
		}
		
		if(is_null($this->_borders)){
			// no borders
			$this->_borders   = FALSE;
		}
		
		parent::__construct($parent);
	}
	
}