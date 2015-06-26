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

use jackkum\PHPCurses\Factory;
use jackkum\PHPCurses\Window;
use jackkum\PHPCurses\Colors;
use jackkum\PHPCurses\Logger;
use jackkum\PHPCurses\Exception;

abstract class Application extends Window {
	
	/**
	 * Application title
	 * @var string
	 */
	protected $_title = "Example application title";
	
	/**
	 * last error
	 * @var string|null
	 */
	protected $_error = NULL;
	
	/**
	 * self instance
	 * @var NCApplication
	 */
	protected static $_instance;
	
	
	/**
	 * 
	 */
	public function __construct()
	{
		// chick init curses
		Factory::getCurses();
		
		$this->_style = new Window\Style($this);
		$this->_style->setColorPair(
			Colors::setPair('defaultToolbar', Colors::WHITE, Colors::BLUE)
		);
		// app window should be always in background
		$this->_style->setZIndex(-1);
		
		// call parent
		parent::__construct();
		
	}
	
	/**
	 * create singleton
	 * 
	 * @throws Exception
	 * @return self
	 */
	final public static function & getInstance()
	{
		$class = get_called_class();
		
        if( ! isset(static::$_instance)){
			if($class === 'jackkum\PHPCurses\Application'){
				throw new Exception("Cannot instantiate abstract class Application");
			}
			
            static::$_instance = new $class();
		}

        return static::$_instance;
	}
	
	/**
	 * callend when application closed
	 * no more curses, use any print methods
	 * show application error or goodby message
	 */
	public function onQuit()
	{
		Logger::debug("Quit application");
		if($this->getError()){
			echo $this->getError(), PHP_EOL;
		} else {
			echo "Application finished", PHP_EOL;
		}
		
	}
	
	public function onResize()
	{
		parent::onResize();
		
		$rows = NULL;
		$cols = NULL;
		ncurses_getmaxyx($this->_window, $rows, $cols);
		
		$this->getStyle()->setRows($rows);
		$this->getStyle()->setCols($cols);
	}
	
	/**
	 * Allow use mouse on application
	 */
	public function useMouse()
	{
		Logger::debug("Use mouse on application");
		$newmask = NCURSES_ALL_MOUSE_EVENTS + NCURSES_REPORT_MOUSE_POSITION;
		$mask    = ncurses_mousemask($newmask, $oldmask);
	}
	
	/**
	 * set last error message
	 * @param string $error
	 */
	public function setError($error)
	{
		$this->_error = $error;
	}
	
	/**
	 * get last error message
	 * @return string|null
	 */
	public function getError()
	{
		return $this->_error;
	}
	
	/**
	 * build all windows array
	 * @param NCWindow|null $parent
	 * @return array
	 */
	public function getAllWindows(Window $parent)
	{
		$windows = array();
		
		foreach($parent->getChilds() as $window){
			$windows = array_merge($windows, $this->getAllWindows($window));
		}
		
		usort($windows, array($this, '_sortWindowsByZIndex'));
		
		return $windows;
	}
	
	private function _sortWindowsByZIndex(Window $w1, Window $w2)
	{
		return $w1->getStyle()->getZIndex() < $w2->getStyle()->getZIndex();
	}


	/**
	 * getting active window
	 * @return Window
	 */
	public function getActiveWindow()
	{
		$windows = $this->getAllWindows($this);
		
		foreach($windows as $window){
			if($window->isActive()){
				return $window;
			}
		}
		
		return $this;
	}
	
	
	/**
	 * loop listen keyboard and mouse
	 */
	public function loop()
	{
		Logger::debug("Loop application");
		while(TRUE){
			// check size windows
			$this->onResize();
			// redraw, some is change or only start
			$this->reDraw();
			// pressed key
			$key = ncurses_getch();
			Logger::debug("Key pressed: " . $key);
			// wait event
			$this->onKeyPress($key);
		}
		
	}
	
}