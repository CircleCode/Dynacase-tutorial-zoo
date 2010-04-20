<?php
/**
 * Display sum of sales
 *
 * @author Anakeen 2008
 * @version $Id: zoo_ticketsales.php,v 1.4 2010-04-20 07:55:44 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package freedom-zoo
 *
 *
 */

include_once("FDL/Class.Doc.php");
include_once("FDL/Class.SearchDoc.php");
/**
 * Display sum of sales
 * @global date Http var : date of the report
 */
function zoo_ticketsales(&$action) {
    $date=$action->getArgument("date");
    $dbaccess=$action->getParam("FREEDOM_DB");


   
    if (!$date) $date=Doc::getDate();

    $s=new SearchDoc($dbaccess,"ZOO_ENTREE");
    $s->addFilter("ent_date='%s'",$date);
    $s->setObjectReturn();
    $tdoc=$s->search();


    $pes=0;
    $pes=0;
    $na=0;
    $ne=0;
    $t=array();
    while ($ticket=$s->nextDoc()) {
	$prixenfant=floatval($ticket->getParamValue("ent_prixenfant"));
	$prixadulte=floatval($ticket->getParamValue("ent_prixadulte"));
        $pe=intval($ticket->getValue("ent_enfant")) * $prixenfant;
        $pa=intval($ticket->getValue("ent_adulte")) * $prixadulte;
        $ne+=intval($ticket->getValue("ent_enfant"));
        $na+=intval($ticket->getValue("ent_adulte"));
        $pes+=$pe;
        $pas+=$pa;
        $t[]=array("nbadulte"=>  $ticket->getValue("ent_adulte"),
	       "nbenfant"=>  $ticket->getValue("ent_enfant"),
	       "prixenfant"=>  $pe,
	       "prixadulte"=>  $pa,
	       "prixtotal"=> $pe+$pa);
    }

    $action->lay->setBlockData("TICKETS",$t);
    $action->lay->set("nbadultes",$na);
    $action->lay->set("nbenfants",$ne);
    $action->lay->set("prixenfants",$pes);
    $action->lay->set("prixadultes",$pas);
    $action->lay->set("total",$pas+$pes);
    $action->lay->set("date",$date);
}

function zoo_xmlticketsales(&$action) {
    header('Content-type: text/xml; charset=utf-8');
    zoo_ticketsales($action);
}

?>