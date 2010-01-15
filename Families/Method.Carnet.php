<?php
/**
 * Animal comportment
 *
 * @author Anakeen 2010
 * @version $Id: Method.Carnet.php,v 1.1 2010-01-15 15:16:38 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package freedom-zoo
 */
/**
 */

/**
 * @begin-method-ignore
 * this part will be deleted when construct document class until end-method-ignore
 */
Class _ANIMAL extends Doc {
	/*
	 * @end-method-ignore
	 */
	public $cviews=array("ZOO:VIEWORDONNANCE");

	//test de code
	function getTime()
	{
		return strftime("%T",time());
	}


	/**
	 * carnet de sante
	 * display new ordonnance
	 */
	function viewordonnance($target="_self",$ulink=true,$abstract=false) {
		$idveto=getHttpVars('ca_idveterinaire');
		$idanimal=getHttpVars('ca_idnom');
		$dateveto=getHttpVars('ca_date');
		$desc=getHttpVars('ca_description');

		$doc=new_Doc($this->dbaccess,$idveto);
		$this->lay->set("us_lname",$doc->getValue("us_lname"));
		$this->lay->set("us_fname",$doc->getValue("us_fname"));

		$this->lay->set("dateveto",$dateveto);
		$this->lay->set("v_desc",$desc);
		$docAnimal=new_Doc($this->dbaccess,$idanimal);
		$this->lay->set("an_nom",$docAnimal->getValue("an_nom"));

	}

	/**
	 * carnet de sante
	 * view resume of diseases
	 */
	function maladie($target="_self",$ulink=true,$abstract=false) {

		$animal=new_doc($this->dbaccess,$this->getValue("ca_idnom"));
		if ($animal->isAlive()) {
			$this->lay->set("animal_name",$animal->getValue("an_nom"));
			$this->lay->set("espece",$animal->getValue("an_espece"));
			$this->lay->set("classe",$animal->getValue("an_classe"));
			$this->lay->set("n",count($this->getTValue("ca_date")));
		} else {
			addWarningMsg(_("the animal document is not found"));
		}
	}
	/**
	 * @begin-method-ignore
	 * this part will be deleted when construct document class until end-method-ignore
	 */
}
/*
 * @end-method-ignore
 */
?>
