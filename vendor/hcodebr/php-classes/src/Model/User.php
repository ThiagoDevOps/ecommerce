<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;

// Classes extendidas de Model teram "gets" and "sets"
class User extends Model {

	// Instrução que cria uma sessão com todas os dados do objeto "User"
	const SESSION = "User";
	const SECRET = "HcodePHP7_Secret";

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

	public static function listAll()
	{

		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");

	}

	public function save()
	{
		
		$sql = new Sql();

		$results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
			":desperson"=>$this->getdesperson(),
			":deslogin"=>$this->getdeslogin(),
			":despassword"=>$this->getdespassword(),
			":desemail"=>$this->getdesemail(),
			":nrphone"=>$this->getnrphone(),
			":inadmin"=>$this->getinadmin()
		));

		$this->setData($results[0]);

	}

	public function get($iduser)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT *FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser + :iduser", array(
			":iduser"=>$iduser
		));

		$this->setData($results[0]);

	}

	public function update()
	{

		$sql = new Sql();

		$results = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
			":iduser"=>$this->getiduser(),
			":desperson"=>$this->getdesperson(),
			":deslogin"=>$this->getdeslogin(),
			":despassword"=>$this->getdespassword(),
			":desemail"=>$this->getdesemail(),
			":nrphone"=>$this->getnrphone(),
			":inadmin"=>$this->getinadmin()
		));

		$this->setData($results[0]);

	}

	public function delete()
	{

		$sql = new Sql();

		$sql->query("CALL sp_users_delete(:iduser)", array(
			":iduser"=>$this->getiduser()
		));

	}

	public static function getForgot($email)
	{

		$sql = new Sql();

		$results = $sql->select("
			SELECT * FROM tb_persons a 
			INNER JOIN tb_users b USING(idperson) 
			WHERE a.desemail = :email
		", array(
			":email"=>$email
		));

		if (count($results) === 0)
		{
		
			throw new \Exception("Não foi possível recuperar a senha.");

		}
		else
		{

			$data = $results[0];

			$sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, desip)", array(
				":iduser"=>$data["iduser"],
				"desip"=>$_SERVER["REMOTE_ADDR"]
			));

		

			if (count($results2) === 0)
			{
		
				throw new \Exception("Não foi possível recuperar a senha.");

			}
			else
			{

				$dataRecovery = $results2[0];

				$results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, desip)", array(
					":iduser"=>$data["iduser"],
					"desip"=>$_SERVER["REMOTE_ADDR"]
				));

				if (count($results2) === 0)
				{
		
					throw new \Exception("Não foi possível recuperar a senha.");

				}
				else
				{

					$dataRecovery = $results2[0];

					$code = base64_encode(openssl_encrypt(MCRYPT_RIJNDAEL_128, User::SECRET, $dataRecovery["idrecovery"], MCRYPT_MODE_ECB));

					$link = "http://hcodecommerce.com.br/admin/forgot/reset?code=$code";

					$mailer = new Mailer($data["desemail"], $data["desperson"], "Redefinir Senha da Hcode Store", "forgot", array(
						"name"=>$data["desperson"],
						"link"=>$link
					));

					$mailer->send();

					return $data;

				}

			}

		}

		public static function validForgotDecrypt($code)
		{

			idrecovery = openssl_encrypt(MCRYPT_RIJNDAEL_128, User::SECRET, base64_decode($code), MCRYPT_MODE_ECB);

			$sql = new Sql();

			$results = $sql->select("
				SELECT *
				FROM tb_userspasswordrecoveries a
				INNER JOIN tb_users b USING(iduser)
				INNER JOIN tb_persons c USING(idperson)
				WHERE
					a.idrecovery = :idrecovery
					AND
					a.dtrecovery IS NULL
					AND
					DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();
					", array(
						":idrecovery"=>idrecovery
					));

			if (count($results) === 0)
				{
		
					throw new \Exception("Não foi possível recuperar a senha.");

				}
				else
				{

					return $results[0];

				}

		}

		public static function setForgotUed($idrecovery)
		{

			$sql = new Sql();

			$sql->query("UPDATE tb_userspasswordrecoveries SET dtrecovery = NOW() WHERE idrecovery = :idrecovery", array(
				":idrecovery"=>$idrecovery
			));

		}

		public function setPassword($password)
		{

			$sql = new Sql();

			$sql->query("UPDATE tb_users SET despassword = :password WHERE iduser = :iduser", array(
				":password"=>$password,
				":iduser"=>$this->getiduser()
			));

		}

	}

}

?>
