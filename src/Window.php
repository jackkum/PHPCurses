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

use jackkum\PHPCurses\Logger;
use jackkum\PHPCurses\Application;
use jackkum\PHPCurses\Window;

abstract class Window {
	
	/**
	 * window resourse
	 * @var resource
	 */
	protected $_window;
	
	/**
	 * window title
	 * @var string
	 */
	protected $_title = "Abstract window title";
	
	/**
	 * window style
	 * @var \Window\Style
	 */
	protected $_style;


	/**
	 * parent window
	 * @var Window
	 */
	protected $_parent = NULL;
	
	/**
	 * child elements
	 * @var array
	 */
	protected $_childs = array();
	
	/**
	 * current window is active
	 * @var boolean
	 */
	protected $_active = FALSE;
	
	/**
	 * init borders window
	 */
	public function __construct(Window & $parent = NULL)
	{
		if( ! is_null($parent)){
			$parent->addChild($this);
			$this->_parent = $parent;
		}
	}
	
	public function __call($method, $params)
	{
		Logger::debug("__call: ".get_class($this)."->".$method."()");
		return call_user_func(array($this, $method), $params);
	}
	
	public static function __callStatic($method, $params)
	{
		Logger::debug("__callStatic: ".get_called_class()."::".$method."()");
		return call_user_func(array(get_called_class(), $method), $params);
	}


	/**
	 * destruct window
	 */
	public function __destruct()
	{
		// if window opened
		if( ! empty($this->_window)){
			$this->close();
		}
	}
	
	/**
	 * add new child to current window
	 * @param Window $window
	 */
	public function addChild(Window $window)
	{
		$this->_childs[] = $window;
		$window->getStyle()->setZIndex(
			(int) $this->getStyle()->getZIndex() + 1
		);
	}
	
	/**
	 * remove child window
	 * @param Window $window
	 */
	public function removeChild(Window $window)
	{
		foreach($this->_childs as $i => $child){
			if($child === $window){
				unset($this->_childs[$i]);
			}
		}
	}
	
	/**
	 * get window resourse
	 * @return resourse
	 */
	public function getWindow()
	{
		return $this->_window;
	}
	
	/**
	 * getting parent window
	 * @return Window
	 */
	public function getParentWindow()
	{
		return $this->_parent ? $this->_parent : Application::getInstance();
	}
	
	/**
	 * 
	 * @return type
	 */
	public function getStyle()
	{
		if($this->_style === NULL){
			$this->_style = new Window\Style($this);
		}
		
		return $this->_style;
	}

	/**
	 * create new window
	 */
	public function create()
	{
		$this->_window = $this->getStyle()->create();
	}
	
	/**
	 * destroy window
	 */
	protected function destroy()
	{
		
		$this->getStyle()->destroy($this->getWindow());
		
		foreach($this->_childs as $child){
			$child->destroy();
		}
		
		if( ! is_null($this->_parent)){
			$this->_parent->removeChild($this);
		}
	}

	/**
	 * refresh window
	 */
	public function refresh()
	{
		$this->getStyle()->refresh();
		
		// refresh childs
		foreach($this->_childs as $window){
			$window->refresh();
		}
		
	}

	/**
	 * show window
	 */
	public function show()
	{
		$this->create();
		
		foreach($this->_childs as $window){
			$window->show();
		}
		
		$this->onCreate();
		$this->activate();
	}
	
	/**
	 * close window
	 */
	public function close()
	{
		$this->getParentWindow()->activate();
		$this->destroy();
	}
	
	/**
	 * set window title
	 * @param string $title
	 */
	public function setTitle($title = NULL)
	{
		if( ! is_null($title)){
			$this->_title = $title;
		}
		
		if(is_null($this->_title)){
			return;
		}
		
		ncurses_wattron($this->_window, NCURSES_A_BOLD);
		//ncurses_wattron($this->_window, NCURSES_A_REVERSE);
		$this->write(sprintf(" %s ", $this->_title), 0, 3);
		//ncurses_wattroff($this->_window, NCURSES_A_REVERSE);
		ncurses_wattroff($this->_window, NCURSES_A_BOLD);
	}

	/**
	 * write string on windows
	 * @param string $text
	 * @param integer $top
	 * @param integer $left
	 */
	public function write($text, $top = 0, $left = 0)
	{
		ncurses_mvwaddstr($this->_window, $top, $left, $text);
	}
	
	/**
	 * current window is active
	 * @return boolean
	 */
	public function isActive()
	{
		return $this->_active;
	}
	
	public function isCollision($top, $left)
	{
		$style = $this->getStyle();
		if($top >= $style->getOffsetTop() && $top <= ($style->getOffsetTop() + $style->getRows())){
			if($left >= $style->getOffsetLeft() && $left <= ($style->getOffsetLeft() + $style->getCols())){
				return TRUE;
			}
		}
		
		return FALSE;
	}
	
	/**
	 * set active window
	 */
	public final function activate()
	{
		$app = Application::getInstance();
		
		foreach($app->getAllWindows($app) as $window){
			$window->deactivate();
		}
		
		$this->_active = TRUE;
	}
	
	/**
	 * set deactive window
	 */
	public final function deactivate()
	{
		$this->_active = FALSE;
	}
	
	/**
	 * getting childs
	 * @return array
	 */
	public function & getChilds()
	{
		return $this->_childs;
	}

	public function reDraw()
	{
		ncurses_clear();
		ncurses_refresh();
		//ncurses_update_panels();
		
		$this->refresh();
		
		foreach($this->_childs as $window){
			$window->refresh();
		}
		
	}
	
	
	// Events
	/**
	 * update window size
	 */
	public function onResize()
	{
		Logger::debug("onResize(".get_class($this).")");
	}
	
	/**
	 * user do soumething
	 * @param integer $key
	 */
	public function onKeyPress($key)
	{
		if($key === Keyboard::MOUSE){
			$mevent = array();
			ncurses_getmouse($mevent);
			if($mevent["mmask"] & NCURSES_BUTTON1_CLICKED){
				$left = $mevent["x"];
				$top  = $mevent["y"];
				$this->onMouseClick($top, $left);
			}
			
			return TRUE;
		}
		
		foreach($this->_childs as $window){
			if($window->onKeyPress($key)){
				return TRUE;
			}
		}
		
		return FALSE;
	}
	
	/**
	 * user use a mouse
	 * @param integer $top
	 * @param integer $left
	 */
	public function onMouseClick($top, $left)
	{
		foreach($this->_childs as $window){
			if($window->onMouseClick($top, $left)){
				return TRUE;
			}
		}
		
		return FALSE;
	}
	
	public function onCreate()
	{
		
	}
	
}