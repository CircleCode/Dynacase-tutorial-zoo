<?php

/**
 * Ticket comportment
 *
 * @author Anakeen 2010
 * @version $Id: Method.Entree.php,v 1.3 2010-02-18 07:58:09 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package freedom-zoo
 */
/**
 */

/**
 * @begin-method-ignore
 * this part will be deleted when construct document class until end-method-ignore
 */
Class _ENTREE extends Doc {
	/*
	 * @end-method-ignore
	 */

	function postModify() {
	    $err=parent::postModify();
	    if ($err=="") $err=$this->setValue("ent_prix",$this->getCost());
		return $err;
	}

	/**
	 * return cost
	 * @return float
	 */
	function getCost() {
		$nb_adulte=intval($this->getValue("ent_adulte"));
		$nb_enfant=intval($this->getValue("ent_enfant"));
		$prix_adulte=floatval($this->getParamValue("ent_prixadulte"));
		$prix_enfant=floatval($this->getParamValue("ent_prixenfant"));

		$resultat=($nb_adulte*$prix_adulte)+($nb_enfant*$prix_enfant);

		return  $resultat;
	}


	/**
	 * view tickets one by personn
	 */
	function viewtickets($target="_self",$ulink=true,$abstract=false) {

	  $nb_adulte=intval($this->getValue("ent_adulte"));
	  $nb_enfant=intval($this->getValue("ent_enfant"));
	  $t=array();
	  for ($i=0;$i<$nb_adulte;$i++) {
	    $t[]=array("type"=>_("Adult") ,
		       "isAdult"=>true);
	  }
	  for ($i=0;$i<$nb_enfant;$i++) {
	    $t[]=array("type"=>_("Child")  ,
		       "isAdult"=>false );
	  }

	  $this->lay->set("today",$this->getValue("ent_date",$this->getDate()));

	  $this->lay->set("n",$nb_adulte+$nb_enfant);
	  $this->lay->setBlockData("TICKET",$t);

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