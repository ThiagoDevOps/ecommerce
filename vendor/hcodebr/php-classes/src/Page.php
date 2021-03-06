<?php
// Page.php contains magic methods "__construct" and "__destruct" to create almost all pages of the project, such as: header, body, footer. Rain TPL will be used as a micro-framework for project templates

// "namespace" indicates where the class is located
namespace Hcode;

// "use" indicates the Rain namespace of the Tpl class
use Rain\Tpl;

// Class that creates all project pages
class Page {

	private $tpl;
	private $options = [];
	// template's default variables. A invocação dos defaults é automatica
	private $defaults = [
		"header"=>true,
		"footer"=>true,
		"data"=>[]
	];

	// Magic method "__construct" is first to run. The use of the page class depends on the routes passed by the slim framework
	public function __construct($opts = array(), $tpl_dir = "/views/"){

		// This statement performs a merge of the optional elements of the template with the default elements of the template. The opts overlap defaults
		$this->options = array_merge($this->defaults, $opts);

		// Setting up the magic method __construct. The template needs a folder to get the html files and a cache folder. DOCUMENT_ROOT is server environment variable that define the root folder do projeto: "ecommerce"
		$config = array(		
			"tpl_dir"       => $_SERVER["DOCUMENT_ROOT"].$tpl_dir, // Folder containing htmls files invoked by the methods of class "Page.php"
			"cache_dir"     => $_SERVER["DOCUMENT_ROOT"]."/views-cache/", // Não há necessidade de criar outra pasta "views-cache", porque, o "rainTPL" cria um tipo de nome específico para cada operação
			"debug"         => false // set to false to improve the speed
		);

		Tpl::configure( $config );

		// Method that create the Tpl object
		$this->tpl = new Tpl;

		// Method that passes optional template data
		$this->setData($this->options["data"]);
		
		// Method that valid and draw the site header html on pages
		if ($this->options["header"] === true) $this->tpl->draw("header");
		
	}

	// Method that pass data of template
	private function setData($data = array())
	{

		foreach ($data as $key => $value){
			$this->tpl->assign($key, $value);
		}

	}

	// Method that creates html pages of the body(content)
	public function setTpl($name, $data = array(), $returnHTML = false){// "returnHTML" or load a page or return an html
	
		$this->setData($data);

		return $this->tpl->draw($name, $returnHTML);

	}
	// Method that valid and draw the site footer html on pages
	public function __destruct(){

		if ($this->options["footer"] === true) $this->tpl->draw("footer");

	}

}

?>