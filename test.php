#!/usr/bin/php
<?php


require __DIR__ . '/vendor/autoload.php';

use jackkum\PHPCurses\Application;
use jackkum\PHPCurses\Factory;
use jackkum\PHPCurses\Keyboard;
use jackkum\PHPCurses\Window\Element\Toolbar;

class MyApp extends Application {
	
	protected $_title = "My application title";
	
	private $toolbar = null;
	
	public function onCreate()
	{
		//$this->toolbar = new MyToolbar($this); 
		//$this->toolbar->show(); 
	}

	public function onMouseClick($top, $left)
	{
		if(parent::onMouseClick($top, $left)){
			// its a child message
			return TRUE;
		}

		return TRUE;
	}

	/**
	 * keyboard event
	 * @param type $key
	 */
	public function onKeyPress($key)
	{

		// call application listener
		if(parent::onKeyPress($key)){
			// its a child message
			return TRUE;
		}
		
		switch($key)
		{	
			/**
			 * user press key "Enter"
			 */
			case Keyboard::ENTER:
				Factory::messageBox("Help window", "Help text\nsdsdfsdfssdfsd sdfsdf sdfsdfsdf sdfsdfs sdfsdfsdf sdfsdfsdfsdf sdsdfsdf sdsdfsdf sdfsdfsdf sdfsdfsdf dfsdfsdf\nsdfsdfsdfsdf sdfsdf sdfsd\nsdfsdfsdf sdfsdsdf\ndfdfgdfgdfgdfg\ndfgdfgdfgdfgdfgdfgdfgdf\ndfgdfdfgdfg\n", $this);
				break;
			
			/**
			 * user press key F10, default is exit
			 */
			case Keyboard::F10:
				exit();
				
			case Keyboard::F1:
				Factory::messageBox("Help window", "Help text\nsdsdfsdfsdfsdfsdf\nsdfsdfsdfsdf sdfsdf sdfsd\nsdfsdfsdf sdfsdsdf\ndfdfgdfgdfgdfg\ndfgdfgdfgdfgdfgdfgdfgdf\ndfgdfdfgdfg\n", $this);
				break;
			
			/**
			 * user press key F5, testing fatal error
			 */
			case Keyboard::F5:
				Factory::fatalError("Some fatal error");
				break;
		}
		
		return TRUE;
	}
}

class MyToolbar extends Toolbar {
	protected $_items = array(
		array(
			'text'   => '[F1] Help',
			'name'   => 'help',
			'method' => 'help'
		),
		array(
			'text'   => '[F10] Exit',
			'name'   => 'exit',
			'method' => 'quit'
		)
	);
	
	private function help()
	{
		Logger::debug("help method called");
	}
	
	private function quit()
	{
		exit();
	}
}


// get app instance
$app = MyApp::getInstance();
// usr mouse on app
$app->useMouse();
// show application
$app->show();
// listen keyboars an mouse
$app->loop();