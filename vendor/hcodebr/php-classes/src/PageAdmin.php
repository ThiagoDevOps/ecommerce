<?php

// "namespace" indicates where the class is located
namespace Hcode;

// Class that creates all admin project pages
class PageAdmin extends Page{

	public function __construct($opts = array(), $tpl_dir = "/views/admin/"){

		parent::__construct($opts, $tpl_dir);

	}

}

?>