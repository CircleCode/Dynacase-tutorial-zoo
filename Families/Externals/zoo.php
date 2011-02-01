<?php

/**
 * return order compatible with class
 * @param string $class identifiant of  classes
 * @param string $name optionnal filter to select order
 */
function getOrdre($dbaccess,$class,$name="") {
  $cl=new_doc($dbaccess,$class);
  if ($cl->isAlive()) {
    $classtitle=$cl->getValue("CL_NOMSCIENTIFIQUE");
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
    $s->addFilter("title ~* '%s'",$name);
  }
  $s->slice=100;
  $s->setObjectReturn();
  $s->search();

  $tr = array();
  while ($doc=$s->nextDoc()) {
    $mobile=$doc->getValue("us_mobile");
    $phone=$doc->getValue("us_phone");
    $postalcode=sprintf("%s\n%s %s",
			$doc->getValue("us_workaddr"),
			$doc->getValue("us_workpostalcode"),
			$doc->getValue("us_worktown"));
    $tr[] = array($doc->getTitle() . ' ('.$doc->getValue("us_society").')',
		  $doc->getProperty('initid'),
		  $doc->getTitle(),
		  $postalcode,
		  $phone,
		  $mobile);

  }
  return $tr;
}

function zoo_searchspecies(&$action,$dbaccess,$id,$nom) {
   // print "DB=$dbaccess, NOM=$nom ID=$id";
    $action->lay->set("enclosname",$nom);
    $doc=new_doc($dbaccess,$id);
    
    if ($doc->isAlive()) {
        $action->lay->set("CAPACITY",$doc->getValue("en_capacite",_("zoo:Capacity not set")));
    } else {
        $action->lay->set("CAPACITY",_("zoo;Capacity not set"));
    }
}
?>