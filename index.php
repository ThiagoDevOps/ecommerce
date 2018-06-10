<?php 

// Instruction that loads all dependencies of the autoload (Composer)
require_once("vendor/autoload.php");

// Namespace that invokes slim classes
use \Slim\Slim;

// Namespace that invokes the page classes
use \Hcode\Page;

// Namespace that invokes the admin page classes
use \Hcode\PageAdmin;

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

// Route "admin" instruction in use
$app->get('/admin', function() {
    
    // Instruction that calls the __construct method and loads the "admin" header page
	$page = new PageAdmin();

	// Instruction that loads "admin" body(content) to page
	$page->setTpl("index");
	// The above statement calls __destruct method and loads the "admin" footer to the page

});

// Instruction that executes all instructions on this page
$app->run();

 ?>