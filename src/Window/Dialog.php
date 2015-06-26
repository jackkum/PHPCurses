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

use jackkum\PHPCurses\Logger;
use jackkum\PHPCurses\Window;
use jackkum\PHPCurses\Window\Element\Toolbar;
use jackkum\PHPCurses\Keyboard;
use jackkum\PHPCurses\Colors;

class Dialog extends Window {
	
	const STATUS_CANCEL = 0;
	const STATUS_OK     = 1;

	protected $_status;
	protected $_content;
	protected $_buttons;
	
	public function __construct($title, $content, Window & $parent = NULL)
	{
		if(is_null($this->_style)){
			$this->_style = new Window\Style($this);
			$this->_style->setColorPair(
				Colors::setPair('defaultToolbar', Colors::WHITE, Colors::MAGENTA)
			);
		}
		
		parent::__construct($parent);
		
		$this->_style->setRows(5);
		$this->_style->setCols(50);
		$this->_style->setOffsetLeft(30);
		$this->_style->setOffsetTop(8);
		
		$this->_title   = $title;
		$this->_content = $this->prepareContent($content);
		
		$app = $this->getParentWindow();
		$app->onResize();
		
		$rows = $app->getStyle()->getRows();
		$cols = $app->getStyle()->getCols();
		
		$top  = ceil($rows / 2) - ceil($this->getStyle()->getRows() / 2);
		$left = ceil($cols / 2) - ceil($this->getStyle()->getCols() / 2);
		
		$this->_style->setOffsetLeft($left);
		$this->_style->setOffsetTop($top);
	}
	
	private function split($line)
	{
		$ready   = array();
		$maxLine = ($this->getStyle()->getCols() - 4);
		
		if(mb_strlen($line) <= $maxLine){
			return array($line);
		}
		
		$words = array();
		$tmp   = explode(" ", $line);
		
		foreach($tmp as $word){
			if(mb_strlen($word) <= $maxLine){
				$words[] = $word;
				continue;
			}
			
			$words = array_merge($words, str_split($word, $maxLine));
		}
		
		$line = NULL;
		
		foreach($words as $word){
			if(mb_strlen($line . " " . $word) > $maxLine){
				$ready[] = trim($line);
				$line = NULL;
			}
			
			$line .= " " . $word;
		}
		
		if( ! is_null($line)){
			$ready[] = trim($line);
		}
		
		return $ready;
	}
	
	private function prepareContent($content)
	{
		$lines = explode("\n", trim($content));
		$ready = array();
		
		foreach($lines as $line){
			$ready = array_merge($ready, $this->split($line));
		}
		
		$style    = $this->getStyle();
		$parent   = $this->getParentWindow();
		$maxLines = $parent->getStyle()->getRows() - 4;
		$style->setRows(count($ready)+3);
		if($style->getRows() > $maxLines){
			$style->setRows($maxLines);
			foreach($ready as $i => $line){
				if($i >= $maxLines){
					unset($ready[$i]);
				}
			}
		}
		
		return $ready;
	}
	
	public function onCreate()
	{
		$this->_buttons = new Toolbar($this, Toolbar::POSITION_BOTTOM, Toolbar::ALIGN_RIGHT);
		
		$this->_buttons->addItem(array(
			'text'   => 'OK',
			'name'   => 'OK',
			'method' => 'success'
		));
		
		$this->_buttons->addItem(array(
			'text'   => 'Cancel',
			'name'   => 'Cancel',
			'method' => 'cancel'
		));
		
		$this->_buttons->show();
	}
	
	public function success()
	{
		Logger::debug("success()");
		$this->_status = self::STATUS_OK;
		$this->close();
	}
	
	public function cancel()
	{
		Logger::debug("cancel()");
		$this->_status = self::STATUS_CANCEL;
		$this->close();
	}
	
	/**
	 * getting toolbar
	 * @return Toolbar
	 */
	public function getToolbar()
	{
		return $this->_buttons;
	}
	
	public function refresh()
	{
		// will clear window
		parent::refresh();
		
		foreach($this->_content as $i => $line){
			$this->write($line, $i+1, 2);
		}
		
		ncurses_wrefresh($this->getWindow());
		Logger::debug("refresh()");
	}
	
	public function getStatus()
	{
		return $this->_status;
	}
	
	public function onKeyPress($key)
	{
		
		switch ($key)
		{
			case Keyboard::ESC: return $this->cancel();
			case Keyboard::ENTER: return $this->success();
		}
		
		return FALSE;
	}
	
	public function onMouseClick($top, $left)
	{
		if($this->isCollision($top, $left)){
			// check childs
			parent::onMouseClick($top, $left);
			// no need other cheks
			return TRUE;
		}
		
		return FALSE;
	}
}