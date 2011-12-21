<?php

/**
 * get animals density for each enclos
 *
 * @author Anakeen 2008
 * @version $Id: zoo_density.php,v 1.4 2010-04-30 13:44:07 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package dynacase-zoo
 *
 * @global Action $action
 */

require_once "FDL/freedom_util.php";

$usage = new ApiUsage();
$usage->setText("get animals density for each enclos");
$usage->verify();

// Check database access
$dbaccess = $action->dbaccess;
if ($dbaccess == "") {
    $action->exitError("Database not found");
}

$s = new SearchDoc($dbaccess, "ZOO_ENCLOS");
$s->setObjectReturn(); 
$s->search(); 

if ($s->count()==0){
    $action->exitError(sprintf("no enclos found"));
}

foreach ($s->getDocumentList() as $enclos) {
    /* @var _ENCLOS $enclos */
    print sprintf("Enclos %30s : surface %4dm² :",
        $enclos->getTitle(),
        $enclos->getValue("en_surface")
    );

    $nbAnimals = count($enclos->getTvalue("en_animaux"));
    if ($nbAnimals > 0){
        $density = floatval($enclos->getValue("en_surface")) / $nbAnimals;
    } else{
        $density = 0;
    }
    print sprintf("%.02f m²/animal (%d animals)\n",
        $density,
        $nbAnimals
    );
}

?>