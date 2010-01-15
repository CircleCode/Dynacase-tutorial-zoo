<?php
/**
 * Animal comportment
 *
 * @author Anakeen 2010
 * @version $Id: Method.Animal.php,v 1.1 2010-01-15 15:16:38 eric Exp $
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
	/**
	 * return document identificator of ascendant
	 * @param string $sexeVar the sexe of ascendant M or F
	 * @return int
	 */
	function getAscendant($sexeVar) {
		include_once("FDL/Class.SearchDoc.php");

		$s=new SearchDoc($this->dbaccess,$this->fromid);
		$s->addFilter("an_enfant ~ '\\\\y{$this->initid}\\\\y'");
		$s->slice=3;
		$tdoc=$s->search();
		if (count($tdoc)==0) return " ";
		foreach($tdoc as $k=>$v) {
			$sexe=getv($v,"an_sexe");
			if ($sexe==$sexeVar) $resultat= $v["id"];
		}

		return  $resultat;
	}

	function postModify() {
		return $this->refreshChilds();
	}
	function preCreated() {
		if ($this->revision == 0) return $this->verifyCapacity();
	}

	function verifyCapacity() {
		include_once("FDL/Class.SearchDoc.php");


		$s=new SearchDoc($this->dbaccess,"ENCLOS");
		$idespece=$this->getValue("an_espece");
		$s->addFilter("en_espece ~ '\\\\y$idespece\\\\y'");
		$s->noViewControl(); // no test view acl
		$s->setObjectReturn();
		$s->search();

		$nbdoc=$s->count();

		if ($nbdoc==0) $err=_("no enclos");
		else {
			while ($doc=$s->nextDoc()) {
				$err=$doc->detectMaxCapacity();
				if ($err=="") break; // first found

			}
		}
		return $err;
	}

	/**
	 * refresh all childs to recompute father and mother
	 */
	function refreshChilds() {
		$idchild=$this->getTValue("an_enfant");
		$oldidchild=$this->_val2array($this->getOldValue("an_enfant"));

		// union unique of old and new values
		$childs=array();
		foreach ($idchild as $child) $childs[$child]=$child;
		foreach ($oldidchild as $child) $childs[$child]=$child;

		foreach ($childs as $idc) {
			$tdoc=getTDoc($this->dbaccess,$idc);
			if ($tdoc) {
				$anw=getDocObject($this->dbaccess, $tdoc);
				$anw->refresh();
			}
		}

		return $err;
	}

	/**
	 * return id of its health card
	 */
	function getHealthCardId() {
		include_once("FDL/Class.SearchDoc.php");
		$famid= "CARNETSANTE";

		$s=new SearchDoc($this->dbaccess,"CARNETSANTE");
		$s->addFilter("ca_idnom =".$this->initid);
		$s->noViewControl(); // no test view acl
		$s->slice=3;
		$tdoc=$s->search();

		if (count($tdoc)==1) return $tdoc[0]["id"];
		return 0;
	}

	/**
	 * create its health card
	 */
	function createHealthCard() {
		$hc=createDoc($this->dbaccess,"CARNETSANTE");
		if ($hc) {
			$hc->setValue("ca_idnom",$this->initid);
			$err=$hc->Add();
			$hc->refresh();

		}

		return $err;
	}

	/**
	 * view to notify veterinary
	 */
	function de_mail_sick($target="_self",$ulink=true,$abstract=false) {
		$this->viewdefaultcard($target,$ulink,$abstract);
		$idcarnet=$this->getHealthCardId();
		$this->lay->set("idcarnet",$idcarnet);
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


