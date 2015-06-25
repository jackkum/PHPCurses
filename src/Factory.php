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
	 * @throws NCException
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
			NCFactory::fatalError('Wrong terminal');
		}
		
		if( ! self::$_curses){
			ncurses_init();
			ncurses_noecho();
			
			if(NCColors::hasColors()){
				NCColors::start();
			}
			
			self::$_curses = TRUE;
			register_shutdown_function(array('NCFactory', 'endCurses'));
			set_error_handler(array('NCFactory', 'errorHandler'));
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
		NCApplication::getInstance()->onQuit();
	}
	
	/**
	 * some fatal error
	 * @param string $message
	 */
	public static function fatalError($message)
	{
		NCApplication::getInstance()->setError($message);
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
		NCApplication::getInstance()->setError($error);
	}
	
	/**
	 * create messagebox
	 * @param string $title
	 * @param string $text
	 */
	public static function messageBox($title, $text, NCWindow & $parent = NULL)
	{
		$dialog = new NCDialog($title, $text, $parent);
		$dialog->show();
	}
	
	/**
	 * create new button
	 * @param String $text
	 * @param callable $callback
	 * @return \NCButton
	 */
	public static function & createButton($text, callable $callback)
	{
		return new NCButton($text, $callback);
	}
	
}