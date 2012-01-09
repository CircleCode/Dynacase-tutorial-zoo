<?php

/**
 * return order compatible with class
 *
 * @param string $dbaccess
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

    if($classId){
        $class = new_doc($dbaccess, $classId);
        if ($class->isAlive()) {
            //get orders for this class only
            $classScientificName = $class->getValue("CL_NOMSCIENTIFIQUE");
            $classOrders = $orders[$classScientificName];

            if (is_array($classOrders)) {
                foreach ($classOrders as $order) {
                    // only keep orders corresponding tu user input
                    if (($userInput == "")
                        || (preg_match("/$userInput/i", $order))
                    ) {
                        $result[] = array($order, $order, '', '');
                    }
                }
                return $result;
            } else {
                return _("zoo:no class referenced");
            }
        } else {
            return _("zoo:unknown class");
        }
    } else {
        foreach($orders as $classLatinName => $classOrders){
            $s = new SearchDoc($dbaccess, 'ZOO_CLASSE');
            $s->addFilter("cl_nomscientifique = '%s'", $classLatinName);
            $s->setSlice(1);
            $t = $s->search();
            $classId = $t[0]['initid'];
            $classTitle = $t[0]['title'];
            foreach ($classOrders as $order) {
                // only keep orders corresponding tu user input
                if (($userInput == "")
                    || (preg_match("/$userInput/i", $order))
                ) {
                    $result[] = array("[$classTitle] $order", $order, $classId, $classTitle);
                }
            }
        }
        return $result;
    }
}

/**
 * return address of a person
 *
 * @param string $dbaccess database coordonates
 * @param string $userInput optionnal filter to select personn
 *
 * @return string[][]|string
 */
function getAddress($dbaccess, $userInput = "")
{

    $s = new SearchDoc($dbaccess, "USER");

    if ($userInput != "") { // add optionnal filter on title
        $s->addFilter("title ~* '%s'", $userInput);
    }
    $s->slice = 100; // limit nb results
    $s->setObjectReturn(); // we want objects

    $result = array();
    foreach ($s->getDocumentList() as $user) {
        /* @var _USER $user */

        $displayTitle = sprintf("%s (%s)",
            $user->getTitle(),
            $user->getValue("us_society")
        );
        $mobile = $user->getValue("us_mobile");
        $phone = $user->getValue("us_phone");
        $postalAddress = sprintf("%s\n%s %s",
            $user->getValue("us_workaddr"),
            $user->getValue("us_workpostalcode"),
            $user->getValue("us_worktown"));

        // add this result
        $result[] = array(
            $displayTitle,
            $user->getProperty('initid'),
            $user->getTitle(),
            $postalAddress,
            $phone,
            $mobile
        );
    }
    return $result;
}

function zoo_searchspecies(&$action,$dbaccess,$id,$nom) {
    $action->lay->set("enclosname",$nom);
    $doc=new_doc($dbaccess,$id);
    
    if ($doc->isAlive()) {
        $action->lay->set("CAPACITY",$doc->getValue("en_capacite",_("zoo:Capacity not set")));
    } else {
        $action->lay->set("CAPACITY",_("zoo;Capacity not set"));
    }
}

?>
