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

use jackkum\PHPCurses\Exception;
use jackkum\PHPCurses\Application;
use jackkum\PHPCurses\Colors;
use jackkum\PHPCurses\Logger;
use jackkum\PHPCurses\Window\Element\Button;
use jackkum\PHPCurses\Window\Dialog;

class Factory {
	
	/**
	 * ncurses is initialized
	 * @var boolean
	 */
	private static $_curses  = FALSE;
	
	/**
	 * windows list
	 * @var array
	 */
	private static $_windows = array();

	/**
	 * cant create factory
	 * @throws Exception
	 */
	private function __construct()
	{
		throw new Exception("Factory no object");
	}
	
	/**
	 * check curses init and terminal
	 * @return type
	 */
	public static function getCurses()
	{
		
		if ( ! posix_isatty(STDOUT)) {
			Factory::fatalError('Wrong terminal');
		}
		
		if( ! function_exists('ncurses_init')){
			Factory::fatalError('Project require ncurses');
		}
		
		if( ! self::$_curses){
			ncurses_init();
			ncurses_noecho();
			
			if(Colors::hasColors()){
				Colors::start();
			}
			
			self::$_curses = TRUE;
			register_shutdown_function(array('jackkum\\PHPCurses\\Factory', 'endCurses'));
			set_error_handler(array('jackkum\\PHPCurses\\Factory', 'errorHandler'));
		}
		
		return self::$_curses;
	}
	
	/**
	 * stop curses
	 */
	public static function endCurses()
	{
		
		if(self::$_curses){
			ncurses_end();
			self::$_curses = NULL;
		}
		
		// call onQuit on application
		Application::getInstance()->onQuit();
	}
	
	/**
	 * some fatal error
	 * @param string $message
	 */
	public static function fatalError($message)
	{
		Logger::debug($message);
		
		try {
			Application::getInstance()->setError($message);
		} catch (Exception $ex) {
			Logger::debug($ex);
			trigger_error($message);
		}
		exit;
	}
	
	/**
	 * some error
	 * @param integer $code
	 * @param string $message
	 * @param string $file
	 * @param integer $line
	 */
	public static function errorHandler($code, $message, $file, $line)
	{
		$error = sprintf("# %d => %s\n\t%s", $line, $file, $message);
		Logger::debug($error);
		Application::getInstance()->setError($error);
	}
	
	/**
	 * create messagebox
	 * @param string $title
	 * @param string $text
	 */
	public static function messageBox($title, $text, Window & $parent = NULL)
	{
		$dialog = new Dialog($title, $text, $parent);
		$dialog->show();
	}
	
	/**
	 * create new button
	 * @param String $text
	 * @param callable $callback
	 * @return Button
	 */
	public static function createButton($text, callable $callback)
	{
		return new Button($text, $callback);
	}
	
}