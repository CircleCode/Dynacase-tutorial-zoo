<?php
/**
 * Gate comportment
 *
 * @author Anakeen 2010
 * @version $Id: Method.Enclos.php,v 1.2 2010-04-02 14:49:04 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package freedom-zoo
 */
/**
 */

/**
 * @begin-method-ignore
 * this part will be deleted when construct document class until end-method-ignore
 */
Class _ENCLOS extends Doc {
	/*
	 * @end-method-ignore
	 */
	public  $defaultview= "ZOO:VIEWENCLOS";


	function specRefresh() {
		$msg=$this->detectMaxCapacity();
		return $msg;
	}

	/**
	 * verify capacity
	 * @return string error message if maximum capacity reached
	 */
	function detectMaxCapacity() {
		$nb=$this->getNbreAnimaux();
		if ($nb == intval($this->getValue("en_capacite"))) return _("zoo:Full Area");
		elseif ($nb > intval($this->getValue("en_capacite"))) return (sprintf(_("zoo:Maximum Capacity reached %d > %s"),$nb,intval($this->getValue("en_capacite"))));
	}

	/**
	 * return count of animals
	 * @return int
	 */
	function getNbreAnimaux()  {
		return count($this->getTValue("en_animaux"));
	}

	/**
	 * default view for enclos to see animal's photo
	 * @return string warning message
	 */
	function viewenclos($target="_self",$ulink=true,$abstract=false ) {
		$this->viewdefaultcard($target,$ulink,$abstract);
		 
		$anidT=$this->getTValue("en_animaux");

		foreach ($anidT as $cle=>$val) {
			$anid[]=array("anid"=>$val);
		}

		$this->lay->setBlockData("PHOTO",$anid);
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