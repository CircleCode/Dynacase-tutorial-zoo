<?php

/**
 * Ticket comportment
 *
 * @author Anakeen 2010
 * @version $Id: Method.Entree.php,v 1.1 2010-01-15 15:16:38 eric Exp $
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
		$err=$this->setValue("ent_prix",$this->getCost());
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
	 * carnet de sante
	 */
	function viewtickets($target="_self",$ulink=true,$abstract=false) {

		$nb_adulte=intval($this->getValue("ent_adulte"));
		$nb_enfant=intval($this->getValue("ent_enfant"));

		for ($i=0;$i<$nb_adulte;$i++) {
			$t[]=array("type"=>_("Adult")  );
		}
		for ($i=0;$i<$nb_enfant;$i++) {
			$t[]=array("type"=>_("Child")  );
		}

		$this->lay->set("today",$this->getDate());

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