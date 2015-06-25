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

class Colors {
	
	private static $_pairs = array();
	private static $_index = 1;
	
	const BLACK   = NCURSES_COLOR_BLACK;
	const WHITE   = NCURSES_COLOR_WHITE;
	const RED     = NCURSES_COLOR_RED;
	const GREEN   = NCURSES_COLOR_GREEN;
	const YELLOW  = NCURSES_COLOR_YELLOW;
	const BLUE    = NCURSES_COLOR_BLUE;
	const CYAN    = NCURSES_COLOR_CYAN;
	const MAGENTA = NCURSES_COLOR_MAGENTA;

	/**
	 * check for can use colors
	 * @return boolean
	 */
	public static function hasColors()
	{
		return ncurses_has_colors();
	}
	
	/**
	 * start color pairs
	 */
	public static function start()
	{
		// start colors
		ncurses_start_color();
		
		// create default pair
		self::setPair('defaultWindow');
	}
	
	/**
	 * create new pair
	 * @param string $name associated name of pair
	 * @param integer $fontColor font color
	 * @param integer $backgroundColor background color
	 * @return integer 
	 */
	public static function setPair($name, $fontColor = self::WHITE, $backgroundColor = self::BLACK)
	{
		if(isset(self::$_pairs[$name])){
			return (int) self::$_pairs[$name];
		}
		
		$index = self::$_index++;
		ncurses_init_pair($index, $fontColor, $backgroundColor);
		self::$_pairs[$name] = $index;
		
		return $index;
	}
	
	/**
	 * getting pair by associated name
	 * @param string $name
	 * @return integer
	 * @throws NCException
	 */
	public static function getPair($name)
	{
		if( ! isset(self::$_pairs[$name])){
			throw new Exception("Цвет не установлен");
		}
		
		return (int) self::$_pairs[$name];
	}
	
}