<?php
/**
 * Display sum of sales
 *
 * @author Anakeen 2008
 * @version $Id: zoo_ticketsales.php,v 1.7 2011-02-01 16:40:08 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package dynacase-zoo
 *
 *
 */

require_once "FDL/freedom_util.php";

/**
 * Display sum of sales
 *
 * @param Action $action
 * @global date Http var : date of the report
 */
function zoo_ticketsales(Action &$action) {
    $actionUsage = new ActionUsage($action);
    $date = $actionUsage->addOption("date", "date for which tickets should be displayed",array(), Doc::getDate());
    $actionUsage->verify();

    $dbaccess=$action->dbaccess;

    $s = new SearchDoc($dbaccess,"ZOO_ENTREE");
    $s->addFilter("ent_date = '%s'", $date);
    $s->setObjectReturn();

    $totlaRecetteAdultes=0;
    $totalRecetteEnfants=0;
    $totalNbAdultes=0;
    $totalNbEnfants=0;

    $tickets=array();
    foreach ($s->getDocumentList() as $ticket) {
        /* @var _ENTREE $ticket */
        $prixEnfant=floatval($ticket->getParamValue("ent_prixenfant"));
        $prixAdulte=floatval($ticket->getParamValue("ent_prixadulte"));

        $nbEnfants = intval($ticket->getValue("ent_enfant"));
        $nbAdultes = intval($ticket->getValue("ent_adulte"));
        $totalNbEnfants += $nbEnfants;
        $totalNbAdultes += $nbAdultes;

        $recetteEnfants=$nbEnfants * $prixEnfant;
        $recetteAdultes=$nbAdultes * $prixAdulte;
        $totalRecetteEnfants+=$recetteEnfants;
        $totlaRecetteAdultes+=$recetteAdultes;

        $tickets[]=array(
            "nbadulte"=> $nbAdultes,
            "nbenfant"=> $nbEnfants,
            "prixenfant"=>  $recetteEnfants,
            "prixadulte"=>  $recetteAdultes,
            "prixtotal"=> $recetteEnfants+$recetteAdultes
        );
    }

    $layout = $action->lay;
    /* @var Layout $layout */

    $layout->setBlockData("TICKETS",$tickets);
    $layout->set("nbadultes",$totalNbAdultes);
    $layout->set("nbenfants",$totalNbEnfants);
    $layout->set("prixenfants",$totalRecetteEnfants);
    $layout->set("prixadultes",$totlaRecetteAdultes);
    $layout->set("total",$totlaRecetteAdultes+$totalRecetteEnfants);
    $layout->set("date",$date);
}

/**
 * Display sum of sales in XML format
 *
 * @param Action $action
 * @global date Http var : date of the report
 */
function zoo_xmlticketsales(Action &$action) {
    header('Content-type: text/xml; charset=utf-8');
    zoo_ticketsales($action);
}

?>