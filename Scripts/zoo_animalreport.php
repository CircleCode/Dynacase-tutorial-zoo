<?php

/**
 * Count Animal by Classe
 *
 * @author Anakeen 2008
 * @version $Id: zoo_animalreport.php,v 1.5 2010-04-20 07:55:45 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package freedom-zoo
 * 
 *
 * @global docid Http var : document identificator
 * @global newname Http var : new animal name
 /**
 */


include_once("FDL/Class.Doc.php");
include_once("FDL/Class.SearchDoc.php");

$usage="usage  ";

$dbaccess=$appl->GetParam("FREEDOM_DB");
if ($dbaccess == "") {
  print "Freedom Database not found : param FREEDOM_DB";
  exit;
}

// search animals from classe
$s=new SearchDoc($dbaccess,"ZOO_ANIMAL");
$tdoc=$s->search();

$action->lay = new Layout(getLayoutFile("ZOO","zoo_animalreport.xml"), $action);
$action->lay->setBlockData("ANIMALS",$tdoc);
$action->lay->set("anicount",$s->count());
print $action->lay->gen();


?>