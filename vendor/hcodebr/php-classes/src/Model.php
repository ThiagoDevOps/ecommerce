<?php

namespace Hcode;

// Classe com função de tratar todos os "gets" e "sets" do projeto
class Model {

	// Atributo que terá todos os valores dos campos do objeto
	private $values = [];

	// Método que sabe quando gets e sets são invocados e necessita do nome e dos arqumentos do método
	public function __call($name, $args)
	{
		// Instrução que detecta qual método foi invocado por contar as 3 primeira letras do método
		$method = substr($name, 0, 3);

		// Instrução que detecta qual campo será usado
		$fieldName = substr($name, 3, strlen($name));

		// Instrução que operacionaliza método get ou set dependendo do que foi detectado
		switch ($method) 
		{

			case "get":
				return (isset($this->values[$fieldName])) ? $this->values[$fieldName] : NULL;
			break;
			
			case "set":
				$this->values[$fieldName] = $args[0];
			break;

		}

	}

	public function setData($data = array())
	{
		foreach ($data as $key => $value) {

			$this->{"set".$key}($value);

		}

	}

	// Método que retorna os valores mantendo as boas práticas e segurança
	public function getValues()
	{

		return $this->values;

	}

}

?>