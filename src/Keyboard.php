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

abstract class Keyboard {
	
	CONST ESC         = 27;
	CONST ENTER       = 13;
	const BACKSPACE   = NCURSES_KEY_BACKSPACE;
	const DOWN        = NCURSES_KEY_DOWN;
	const UP          = NCURSES_KEY_UP;
	const LEFT        = NCURSES_KEY_LEFT;
	const RIGHT       = NCURSES_KEY_RIGHT;
	const HOME        = NCURSES_KEY_HOME;
	const DELETE      = NCURSES_KEY_DC;
	const SCROLL_DOWN = NCURSES_KEY_SF;
	const SCROLL_UP   = NCURSES_KEY_SR;
	const PAGE_DOWN   = NCURSES_KEY_NPAGE;
	const PAGE_UP     = NCURSES_KEY_PPAGE;
	const TAB         = NCURSES_KEY_STAB;
	const END         = NCURSES_KEY_END;
	const HELP        = NCURSES_KEY_HELP;
	CONST MOUSE       = NCURSES_KEY_MOUSE;
	CONST SAVE        = NCURSES_KEY_SSAVE;
	CONST F0          = NCURSES_KEY_F0;
	CONST F1          = NCURSES_KEY_F1;
	CONST F2          = NCURSES_KEY_F2;
	CONST F3          = NCURSES_KEY_F3;
	CONST F4          = NCURSES_KEY_F4;
	CONST F5          = NCURSES_KEY_F5;
	CONST F6          = NCURSES_KEY_F6;
	CONST F7          = NCURSES_KEY_F7;
	CONST F8          = NCURSES_KEY_F8;
	CONST F9          = NCURSES_KEY_F9;
	CONST F10         = NCURSES_KEY_F10;
	CONST F11         = NCURSES_KEY_F11;
	CONST F12         = NCURSES_KEY_F12;
	
}