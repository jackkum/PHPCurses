<?php

/* 
 * Copyright (C) 2014 jackkum
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

use jackkum\PHPCurses\Window;
use jackkum\PHPCurses\Colors;
use jackkum\PHPCurses\Exception;

class Style {
	
	/**
	 * rows number
	 * @var integer
	 */
	protected $_rows  = 0;
	
	/**
	 * cols number
	 * @var integer
	 */
	protected $_cols  = 0;
	
	/**
	 * left position window
	 * @var integer
	 */
	protected $_left  = 0;
	
	/**
	 * top position window
	 * @var integer
	 */
	protected $_top   = 0;
	
	/**
	 * windows borders 
	 * @see __constructor()
	 * @var object
	 */
	protected $_borders = NULL;
	
	/**
	 * number of color pair
	 * @var integer
	 */
	protected $_colorPair = NULL;
	
	/**
	 *
	 * @var Window
	 */
	protected $_parent;
	
	/**
	 * constructor
	 * @param integer $rows
	 * @param integer $cols
	 * @param integer $top
	 * @param integer $left
	 * @param object  $borders
	 * @param integer $colorPair
	 */
	public function __construct(Window $parent, $rows = 0, $cols = 0, $top = 0, $left = 0, $borders = NULL, $colorPair = NULL)
	{
		$this->_parent    = $parent;
		$this->_rows      = $rows;
		$this->_cols      = $cols;
		$this->_top       = $top;
		$this->_left      = $left;
		$this->_borders   = $borders;
		$this->_colorPair = $colorPair;
		
		if(is_null($this->_borders)){
			$this->_borders = (object)array(
				'left'     => '|',   'top'      => '-', 
				'right'    => '|',   'bottom'   => '-', 
				'tlCorner' => '+',   'trCorner' => '+', 
				'brCorner' => '+',   'blCorner' => '+'
			);
		}
		
		if(is_null($this->_colorPair)){
			$this->_colorPair = Colors::setPair('defaultWindow');
		}
	}
	
	/**
	 * create curses window
	 * @return Window
	 * @throws Exception
	 */
	public function create()
	{
		$window = ncurses_newwin($this->_rows, $this->_cols, $this->_top, $this->_left);
		
		if (empty($window)) {
			throw new Exception("Cant create a window");
		}
		
		ncurses_wcolor_set($window, $this->_colorPair);
		
		return $window;
	}
	
	/**
	 * destroy window
	 */
	public function destroy()
	{
		$window = $this->_parent->getWindow();
		
		if(is_resource($window)){
			ncurses_wclear($window);
			ncurses_delwin($window);
			$window = NULL;
		}
	}
	
	/**
	 * refresh window
	 */
	public function refresh()
	{
		$window = $this->_parent->getWindow();
		
		if( ! $window){
			return;
		}
		
		// clear window
		ncurses_wclear($window);
		
		for($row = 0; $row < $this->_rows; $row++){
			for($col = 0; $col < $this->_cols; $col++){
				$this->_parent->write(" ", $row, $col);
			}
		}
		
		if($this->_borders){
			// draw borders
			ncurses_wborder(
				$window, 
				ord($this->_borders->left),       ord($this->_borders->right), 
				ord($this->_borders->top),        ord($this->_borders->bottom), 
				ord($this->_borders->tlCorner),   ord($this->_borders->trCorner), 
				ord($this->_borders->brCorner),   ord($this->_borders->blCorner)
			);
		}
		
		// set window title
		$this->_parent->setTitle();
		
		// refresh current window
		ncurses_wrefresh($window);
		
	}
	
	public function setColorPair($pair)
	{
		$this->_colorPair = $pair;
	}
	
	public function setBorders($borders)
	{
		$this->_borders = $borders;
	}
	
	/**
	 * getter for window rows
	 * @return integer
	 */
	public function getRows()
	{
		return $this->_rows;
	}
	
	public function setRows($rows)
	{
		$this->_rows = $rows;
	}
	
	/**
	 * getter for window cols
	 * @return integer
	 */
	public function getCols()
	{
		return $this->_cols;
	}
	
	public function setCols($cols)
	{
		$this->_cols = $cols;
	}
	
	/**
	 * getter for window top offset
	 * @return integer
	 */
	public function getOffsetTop()
	{
		return $this->_top;
	}
	
	public function setOffsetTop($top)
	{
		$this->_top = $top;
	}
	
	/**
	 * 
	 * @return integer
	 */
	public function getOffsetLeft()
	{
		return $this->_left;
	}
	
	public function setOffsetLeft($left)
	{
		$this->_left = $left;
	}
	
	/**
	 * set window rect
	 * @param integer $rows
	 * @param integer $cols
	 * @param integer $left
	 * @param integer $top
	 */
	public function setRect($rows, $cols, $left, $top)
	{
		$this->_rows = $rows;
		$this->_cols = $cols;
		$this->_left = $left;
		$this->_top  = $top;
	}
}