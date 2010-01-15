<?php

/**
 * Count Animal by Classe
 *
 * @author Anakeen 2008
 * @version $Id: zoo_animalreport.php,v 1.1 2010-01-15 15:19:40 eric Exp $
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

$usage="usage  --classe=<latin name> ";

$dbaccess=$appl->GetParam("FREEDOM_DB");
if ($dbaccess == "") {
  print "Freedom Database not found : param FREEDOM_DB";
  exit;
}


$latin = (GetHttpVars("classe")); // 

if (! $latin)   $action->exitError("classe needed :\n $usage");  

// search the classe document
$s=new SearchDoc($dbaccess,"ZOO:CLASSE");
$s->addFilter("lower(cl_nomscientifique) = '".pg_escape_string(strtolower($latin))."'");
$s->setObjectReturn(); 
$s->slice=1;
$tdoc=$s->search();

if ($s->count()==0) $action->exitError(sprintf("no classe found %s",$latin));  
$docclass=$s->nextDoc();
print sprintf("Classe %s :\n",$docclass->title);

// search animals from classe
$s=new SearchDoc($dbaccess,"ANIMAL");
$s->addFilter("an_classe='".$docclass->id."'");
$tdoc=$s->search();


$action->lay = new Layout(getLayoutFile("ZOO","zoo_animalreport.xml"), $action);
$action->lay->setEncoding("utf-8");
$action->lay->setBlockData("ANIMALS",$tdoc);
$action->lay->set("anicount",count($tdoc));
print $action->lay->gen();


?>