<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;

// Classes extendidas de Model teram "gets" and "sets"
class User extends Model {

	// Instrução que cria uma sessão com todas os dados do objeto "User"
	const SESSION = "User";

	// Método que procura no banco de dados se o login e senha(hash) existem
	public static function login($login, $password)
	{
		// Instrução que acessa o banco de dados
		$sql = new Sql();

		// Instrução que acessa a tabela tb_users do banco de dados NOTA: Essa forma de acesso ao banco de dados evita SQL Injection
		$results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array( 
			":LOGIN"=>$login
		));

		// Instrução que verifica se login foi encontrado
		if (count($results) === 0)
		{
			// Instrução que mostra um alerta por não ter encontrado login. "\Exception" é enviada a pasta principal Hcode
			throw new \Exception("Usuário inexistente ou senha inválida.");
		}

		// Instrução que registra os $results
		$data = $results[0];

		// Instrução que verifica o password que é um hash vindo do banco de dados pela variável "$data"
		if (password_verify($password, $data["despassword"]) === true)
		{
			
			// Instrução que cria objeto "User"
			$user = new User();

			// Instrução que invoka os dados de "User"
			$user->setiduser($data);

			// Instrução que invoca os valores do objeto "User" para a sessão
			$_SESSION[User::SESSION] = $user->getValeus();
			
			return $user;

			// Instrução que mostra um alerta por não ter encontrado senha. O alerta é esse para confundir o usuário
		} else {
			throw new \Exception("Usuário inexistente ou senha inválida.");
		}

	}

	// Method that verify login
	public static function verifyLogin($inadmin = true)
	{

		// Instrução que em etapas verifica se "User" está logado, se não, redireciona para /admin/login
		if (
			!isset($_SESSION[User::SESSION])// Verifica se a session não foi definida
			||
			!$_SESSION[User::SESSION]//  Verifica se a session está vazia
			||
			!(int)$_SESSION[User::SESSION]["iduser"] > 0 // Verifica se a session usuário
			||
			(bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin // Verifica se é uma session admin
		) {
			//
			header("Location: /admin/login");
			exit;

		}

	}

	// Método que Exclui a session
	public static function logout()
	{

		$_SESSION[User::SESSION] = NULL;

	}

}

?>