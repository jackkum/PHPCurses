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

namespace jackkum\PHPCurses;

use jackkum\PHPCurses\Window;
use jackkum\PHPCurses\Factory;

abstract class Panel {
	
	CONST POS_TOP    = 0;
	CONST POS_BOTTOM = 1;
	CONST POS_LEFT   = 2;
	CONST POS_RIGHT  = 3;

	/**
	 * window object
	 * @var Window
	 */
	protected $_window;
	
	/**
	 * panel resourse
	 * @var resourse 
	 */
	protected $_panel;
	
	public function __construct(Window & $window)
	{
		$this->_window = $window;
		$window->addPanel($this);
	}
	
	/**
	 * get window resourse
	 * @return resourse
	 */
	public function getPanel()
	{
		return $this->_panel;
	}
	
	/**
	 * get window object
	 * @return Window
	 */
	public function getParent()
	{
		return $this->_window;
	}
	
	/**
	 * create new window
	 */
	protected function create()
	{
		$this->_panel = ncurses_new_panel($this->getParent()->getWindow());
		
		if (empty($this->_panel)) {
			Factory::fatalError('Unable to create panel');
		}
	}
	
	/**
	 * destroy window
	 */
	protected function destroy()
	{
		if($this->_panel){
			ncurses_del_panel($this->_panel);
		}
	}
	
	public function refresh()
	{
		$this->write("Iam panel");
	}
	
	/**
	 * show panel
	 */
	public function show()
	{
		if(empty($this->_panel)){
			$this->create();
		}
		
		ncurses_show_panel($this->_panel);
		ncurses_top_panel($this->_panel);
	}
	
	/**
	 * hide panel
	 */
	public function hide()
	{
		ncurses_hide_panel($this->getPanel());
	}
	
	public function write($text, $top = 0, $left = 0)
	{
		//ncurses_mvwaddstr($this->_panel, $top, $left, $text);
	}
}