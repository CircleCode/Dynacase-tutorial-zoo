<?php

/**
 * return order compatible with class
 *
 * @param strinf $dbaccess
 * @param string $classId identifiant of  classes
 * @param string $userInput optionnal filter to select order
 *
 * @return string[][]|string
 */
function getOrdre($dbaccess, $classId, $userInput = "")
{
    // init all orders
    $orders = array(
        "Mammalia"       => array("Rongeur", "Artiodactyles", "Cétacés"),
        "Aves"           => array("Falconiforme", "Procellariiforme", "Struthioniforme"),
        "Lissamphibia"   => array("Urodèles", "Batracines", "Anoures"),
        "Reptilia"       => array("Crocodiliens", "Saurophidiens", "Squamates"),
        "Actinopterygii" => array("Hypotrème", "Anguilliformes", "Perciforme")
    );

    $result = array();

    $class = new_doc($dbaccess, $classId);
    //get orders for this class only
    $classScientificName = $class->getValue("CL_NOMSCIENTIFIQUE");
    $classOrders = $orders[$classScientificName];

    foreach ($classOrders as $order) {
        // only keep orders corresponding tu user input
        if (($userInput == "")
            || (preg_match("/$userInput/i", $order))
        ) {
            $result[] = array($order, $order);
        }
    }

    return $result;
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