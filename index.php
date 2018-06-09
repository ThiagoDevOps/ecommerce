<?php 

// Instruction that loads all dependencies of the autoload (Composer)
require_once("vendor/autoload.php");

// Namespace that invokes slim classes
use \Slim\Slim;

// Namespace that invokes the page classes
use \Hcode\Page;

// Instruction that invokes routes
$app = new Slim();

// Instruction invoking code debug
$app->config('debug', true);

// Route instruction in use
$app->get('/', function() {
    
    // Instruction that calls the __construct method and loads the header page
	$page = new Page();

	// Instruction that loads body(content) to page
	$page->setTpl("index");
	// The above statement calls __destruct method and loads the footer to the page

});

// Instruction that executes all instructions on this page
$app->run();

 ?>