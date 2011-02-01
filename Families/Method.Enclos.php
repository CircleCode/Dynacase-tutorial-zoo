<?php
/**
 * Gate comportment
 *
 * @author Anakeen 2010
 * @version $Id: Method.Enclos.php,v 1.5 2011-02-01 16:40:08 eric Exp $
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


	public function specRefresh() {
		$msg=$this->detectMaxCapacity();
		return $msg;
	}

	/**
	 * verify capacity
	 * @return string error message if maximum capacity reached
	 */
	public function detectMaxCapacity() {
		$nb=$this->getNbreAnimaux();
		if ($nb == intval($this->getValue("en_capacite"))) return _("zoo:Full Area");
		elseif ($nb > intval($this->getValue("en_capacite"))) return (sprintf(_("zoo:Maximum Capacity reached %d > %d"),$nb,intval($this->getValue("en_capacite"))));
	}

	/**
	 * return count of animals
	 * @return int
	 */
	public function getNbreAnimaux()  {
		return count($this->getTValue("en_animaux"));
	}

	/**
	 * default view for enclos to see animal's photo
	 * @return string warning message
	 */
	function viewenclos($target="_self",$ulink=true,$abstract=false ) {
		$this->viewdefaultcard($target,$ulink,$abstract);
		 
		$anidT=$this->getTValue("en_animaux");
		$anid=array();
		foreach ($anidT as $cle=>$val) {
			$anid[]=array("anid"=>$val);
		}

		$this->lay->setBlockData("PHOTO",$anid);
	}

	
	function enclos($target="_self",$ulink=true,$abstract=false ) {
	  //$this->viewdefaultcard($target,$ulink,$abstract);
	  $t[] = array( "V_VALEUR" => "1",
                        "V_ISTEST" => true);
	  $t[] = array( "V_VALEUR" => "2",
                        "V_ISTEST" => false);
	  $this->lay->set("TODAY",$this->getDate());
	  $this->lay->setBlockData("",$t);
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