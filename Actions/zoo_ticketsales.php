<?php
/**
 * Display sum of sales
 *
 * @author Anakeen 2008
 * @version $Id: zoo_ticketsales.php,v 1.1 2010-01-15 15:15:38 eric Exp $
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
  $date=getHTTPVars("date");
  $dbaccess=getParam("FREEDOM_DB");
  

  $wticket=new_doc($dbaccess,"ENTREE");
  if (!$date) $date=$wticket->getDate();

  $s=new SearchDoc($dbaccess,"ENTREE");
  $s->addFilter("ent_date='".pg_escape_string($date)."'");
  $tdoc=$s->search();
  

  $prixenfant=floatval($wticket->getParamValue("ent_prixenfant"));
  $prixadulte=floatval($wticket->getParamValue("ent_prixadulte"));
  $pes=0;
  $pes=0;
  $na=0;
  $ne=0;
  $t=array();
  foreach ($tdoc as $k=>$v) {
    $pe=intval(getv($v,"ent_enfant")) * $prixenfant;
    $pa=intval(getv($v,"ent_adulte")) * $prixadulte;
    $ne+=intval(getv($v,"ent_enfant"));
    $na+=intval(getv($v,"ent_adulte"));
    $pes+=$pe;
    $pas+=$pa;
    $t[]=array("nbadulte"=> sprintf("%2d", getv($v,"ent_adulte")),
	       "nbenfant"=> sprintf("%2d", getv($v,"ent_enfant")),
	       "prixenfant"=> sprintf("%4d", $pe),
	       "prixadulte"=> sprintf("%4d", $pa),
	       "prixtotal"=> sprintf("%5d", $pe+$pa));
  }
  
  $action->lay->setBlockData("TICKETS",$t);
  $action->lay->set("nbadultes",sprintf("%2d",$na));
  $action->lay->set("nbenfants",sprintf("%2d",$ne));
  $action->lay->set("prixenfants",sprintf("%4d",$pes));
  $action->lay->set("prixadultes",sprintf("%4d",$pas));
  $action->lay->set("total",sprintf("%5d",$pas+$pes));
  $action->lay->set("date",$date);
}
?>