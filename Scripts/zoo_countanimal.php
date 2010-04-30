<?php

/**
 * Count Animal by Classe
 *
 * @author Anakeen 2008
 * @version $Id: zoo_countanimal.php,v 1.6 2010-04-30 13:44:07 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package freedom-zoo
 * 
 *
 * @global classe Http var : latin classes
 */
 /**
 */


include_once("FDL/Class.Doc.php");
include_once("FDL/Class.SearchDoc.php");

$usage="usage  --classe=<latin name> ";

$dbaccess=$appl->GetParam("FREEDOM_DB");
if ($dbaccess == "") {
  print "Freedom Database not found : param FREEDOM_DB";
  exit;
}

$latin = $action->getArgument("classe"); // 
if (! $latin)   $action->exitError("classe needed :\n $usage");  

// search the classe document
$s=new SearchDoc($dbaccess,"ZOO_CLASSE");
$s->addFilter("lower(cl_nomscientifique) = '%s'",strtolower($latin));
$s->setObjectReturn(); 
$s->slice=1;
$s->search();

if ($s->count()==0) {
  $s=new SearchDoc($dbaccess,"ZOO_CLASSE");
  $s->setObjectReturn(); 
  $s->search();
  $k=0;
  while ($doc=$s->nextDoc()) {
      print $k++.")".$doc->getValue("cl_nomscientifique")."\n";
  }
  $action->exitError(sprintf("no classe found %s",$latin));  
}
$docclass=$s->nextDoc();
print sprintf("Classe %s :",$docclass->getTitle());

// search animals from classe

$s=new SearchDoc($dbaccess,"ZOO_ANIMAL");
$s->setObjectReturn();
$s->addFilter("an_classe='%d'",$docclass->id);
$s->search();

print sprintf("%d animals found.\n",$s->count());

?>