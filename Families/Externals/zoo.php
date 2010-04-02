<?php

/**
 * return order compatible with class
 * @param string $class scientific name of classes
 * @param string $name optionnal filter to select order
 */
function getOrdre($class,$name="") {
	$cl=new_doc(getParam("FREEDOM_DB"),$class);
	if ($cl->isAlive()) {
		$classtitle=$cl->getTitle();
		$tabOrdre= array(
		     "Mammalia"=>array("Rongeur","Artiodactyles","Cétacés"),
		     "Aves"=>array("Falconiforme","Procellariiforme","Struthioniforme"),
		     "Lissamphibia"=>array("Urodèles","Batracines","Anoures"),
		     "Reptilia"=>array("Crocodiliens", "Saurophidiens","Squamates"),
		     "Actinopterygii" => array("Hypotrème","Anguilliformes","Perciforme")

		);

		$resultat=$tabOrdre[$classtitle];
		$ordre=array();
		if (is_array($resultat)) {
			foreach($resultat as $cle=>$val) {
				if (($name == "") || (preg_match("/$name/i", $val )))   $ordre[]= array($val,$val);
			}
			return $ordre;
		} else {
			return _("zoo:no class referenced");
		}
	} else return _("zoo:unknown class");
}



/**
 * return address of a person
 * @param string $dbaccess database coordonates
 * @param string $name optionnal filter to select personn
 */
function getAddress($dbaccess,$name="") {
	include_once("FDL/Class.SearchDoc.php");

	$s=new SearchDoc($dbaccess,"USER");
	if ($name != "") {  // add optionnal filter on title
		$filter=sprintf("title ~* '%s'",pg_escape_string($name));
		$s->addFilter($filter);
	}
	$s->slice=100;
	$tdoc=$s->search();

	$tr = array();
	foreach($tdoc as $k=>$v) {
		$mobile=getv($v,"us_mobile");
		$phone=getv($v,"us_phone");
		$postalcode=sprintf("%s\n%s %s",
		          getv($v,"us_workaddr"),
		          getv($v,"us_workpostalcode"),
		          getv($v,"us_worktown"));
		$tr[] = array($v["title"] . '<i>'.$v["us_society"].'</i>',
		              $v["id"],$v["title"],$postalcode,$phone,$mobile);

	}
	return $tr;
}
?>