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

class Dialog extends Window {
	
	const STATUS_CANCEL = 0;
	const STATUS_OK     = 1;

	protected $_status;
	protected $_content;
	protected $_buttons;
	
	protected $_rows  = 5;
	protected $_cols  = 50;
	protected $_left  = 30;
	protected $_top   = 8;
	
	public function __construct($title, $content, Window & $parent = NULL)
	{
		$this->_colorPair = Colors::setPair('defaultDialog', Colors::WHITE, Colors::MAGENTA);
		
		parent::__construct($parent);
		
		$this->_title   = $title;
		$this->_content = $this->prepareContent($content);
		
		$app = $this->getParentWindow();
		$app->onResize();
		
		$rows = $app->getStyle()->getRows();
		$cols = $app->etStyle()->getCols();
		
		$this->_top  = ceil($rows / 2) - ceil($this->_rows / 2);
		$this->_left = ceil($cols / 2) - ceil($this->_cols / 2);
	}
	
	private function split($line)
	{
		$ready   = array();
		$maxLine = ($this->_cols - 4);
		
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
		
		$parent         = $this->getParentWindow();
		$maxLines       = $parent->getStyle()->getRows() - 4;
		$this->_rows    = count($ready)+3;
		if($this->_rows > $maxLines){
			$this->_rows = $maxLines;
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
		Log::debug("success()");
		$this->_status = self::STATUS_OK;
		$this->close();
	}
	
	public function cancel()
	{
		Log::debug("cancel()");
		$this->_status = self::STATUS_CANCEL;
		$this->close();
	}
	
	/**
	 * getting toolbar
	 * @return Toolbar
	 */
	public function & getToolbar()
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