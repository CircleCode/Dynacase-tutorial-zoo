<?php

/**
 * Animal Density
 *
 * @author Anakeen 2008
 * @version $Id: zoo_density.php,v 1.4 2010-04-30 13:44:07 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package freedom-zoo
 * 
 */
 /**
 */


include_once("FDL/Class.Doc.php");
include_once("FDL/Class.SearchDoc.php");

$usage="usage   ";

$dbaccess=$appl->GetParam("FREEDOM_DB");
if ($dbaccess == "") {
  print "Freedom Database not found : param FREEDOM_DB";
  exit;
}

// search the classe document

$s=new SearchDoc($dbaccess,"ZOO_ENCLOS");
$s->setObjectReturn(); 
$s->search(); 

if ($s->count()==0) $action->exitError(sprintf("no enclos found"));  
while ($doc=$s->nextDoc()) {
  print sprintf("Enclos %30s : surface %4dm² :",$doc->getTitle(),
                      $doc->getValue("en_surface"));
  $nbani=count($doc->getTvalue("en_animaux"));
  if ($nbani > 0) $density=floatval($doc->getValue("en_surface"))/$nbani;
  else $density=0;
  print sprintf("%.02f m²/animal (%d animals)\n",$density,$nbani);
}

?>