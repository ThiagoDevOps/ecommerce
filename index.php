<?php 

session_start();

// Instruction that loads all dependencies of the autoload (Composer)
require_once("vendor/autoload.php");

// Namespace that invokes slim classes
use \Slim\Slim;

// Namespace that invokes the page classes
use \Hcode\Page;

// Namespace that invokes the admin page classes
use \Hcode\PageAdmin;

// Namespace that invokes the user page classes
use \Hcode\Model\User;

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

	User::verifyLogin();
    
    // Instruction that calls the __construct method and loads the "admin" header page
	$page = new PageAdmin();

	// Instruction that loads "admin" body(content) to page
	$page->setTpl("index");
	// The above statement calls __destruct method and loads the "admin" footer to the page

});

// Route "login" instruction in use
$app->get('/admin/login', function() {
    
    // Instruction that disable header and footer htmls pages
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("login");

});

$app->post('/admin/login', function() {

	User::login($_POST["login"], $_POST["password"]);

	header("Location: /admin");
	exit;

});

$app->get('/admin/logout', function() {

	User::logout();

	header("Location: /admin/login");
	exit;

});

// Instruction that executes all instructions on this page
$app->run();

 ?>