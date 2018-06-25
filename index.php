<?php 

// Instrução que inicia o uso de sessions
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
    
    // Instruction that calls the __construct method and loads the "site" header page
	$page = new Page();

	// Instruction that loads "site" body(content) to page
	$page->setTpl("index");
	// The above statement calls __destruct method and loads the "site" footer to the page

});



// Route "admin"
$app->get('/admin', function() {

	// Método estático que verifica login
	User::verifyLogin();
    
    // Instruction that calls the __construct method and loads the "admin" header page
	$page = new PageAdmin();

	// Instruction that loads "admin" body(content) to page
	$page->setTpl("index");
	// The above statement calls __destruct method and loads the "admin" footer to the page

});



// Route "login"
$app->get('/admin/login', function() {
    
    // Instruction that disable header and footer htmls pages 
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	// instruction that call template login
	$page->setTpl("login");

});



$app->post('/admin/login', function() {

	// Método estático que reccebe formulário post de admin/login
	User::login($_POST["login"], $_POST["password"]);

	// Instrução que redireciona a rota para a pasta /admin
	header("Location: /admin");

	// Instrução que para a execução
	exit;

});



// Instrução que define a rota de logout
$app->get('/admin/logout', function() {

	// Instrução que exclue a session
	User::logout();

	// Instrução que redireciona o usuário para página /admin/login
	header("Location: /admin/login");
	exit;

});



$app->get("/admin/users", function() {

	User::verifyLogin();

	$users = User::listAll();

	// Instruction that calls the __construct method and loads the "admin" header page
	$page = new PageAdmin();

	// Instruction that loads "admin" body(content) to page
	$page->setTpl("users", array(
		"users"=>$users
	));
	// The above statement calls __destruct method and loads the "admin" footer to the page

});



$app->get("/admin/users-create", function() {

	User::verifyLogin();

	// Instruction that calls the __construct method and loads the "admin" header page
	$page = new PageAdmin();

	// Instruction that loads "admin" body(content) to page
	$page->setTpl("users-create");
	// The above statement calls __destruct method and loads the "admin" footer to the page

});



$app->get("/admin/users/:iduser/delete", function($iduser) {

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$user->delete();

	header("Location: /admin/users");
	exit;
	
});



$app->get("/admin/users/:iduser", function($iduser) {

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	// Instruction that calls the __construct method and loads the "admin" header page
	$page = new PageAdmin();

	// Instruction that loads "admin" body(content) to page
	$page->setTpl("users-update", array(
		"user"=>$user->getValues()
	));
	// The above statement calls __destruct method and loads the "admin" footer to the page

});



$app->post("/admin/users/create", function() {

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->setData($_POST);

	$user->save();

	header("Location: /admin/users");
	exit;

});



$app->post("/admin/users/:iduser", function($iduser) {

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->get((int)$iduser);

	$user->setData($_POST);

	$user->update();

	header("Location: /admin/users");
	exit;
	
});



$app->get("/admin/forgot", function() {

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot");

});



$app->post("/admin/forgot", function() {

	$user = User::getForgot($_POST["email"]);

	header("Location: /admin/forgot/sent");
	exit;

});



$app->get("/admin/forgot/sent", function(){

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-sent");

});



$app->get("/admin/forgot/reset", function(){

	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));

});


$app->post("/admin/forgot/reset", function(){

	$forgot = User::validForgotDecrypt($_POST["code"]);

	User::setForgotUsed($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, [
		"cost"=>12
	])

	$user->setPassword($password);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset-success");

});




// Instruction that executes all instructions on this page
$app->run();

 ?>