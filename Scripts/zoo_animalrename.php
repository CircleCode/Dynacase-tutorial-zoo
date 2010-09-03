<?php

/**
 * Rename Animal
 *
 * @author Anakeen 2008
 * @version $Id: zoo_animalrename.php,v 1.5 2010-09-03 07:07:12 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package freedom-zoo
 * 
 *
 * @global docid Http var : document identificator
 * @global newname Http var : new animal name
 /**
 */


include_once("FDL/Class.Doc.php");

$usage="usage  --docid=<doc identificator> --newname=<new animal name>";

$dbaccess=$action->GetParam("FREEDOM_DB");
if ($dbaccess == "") {
  $action->exitError( "Freedom Database not found : param FREEDOM_DB");
}

$docid = $action->getArgument("docid",0); // special docid
$newname = $action->getArgument("newname"); // 

if ($newname=="") {
  $action->exitError("attribute newname is needed:\n$usage");
 }
$doc=new_Doc($dbaccess,$docid);
if ($doc->isAlive()) {
  $oldtitle=$doc->getTitle();
  if ($doc->fromname != 'ZOO_ANIMAL') {
    $fdoc=$doc->getFamDoc();
    $action->exitError(sprintf("%s [%d] document is not an animal (it is a %s)",
			       $doc->getTitle(),$doc->id,$fdoc->getTitle()));
  }
  $err=$doc->setValue("an_nom",$newname);  
  if ($err != "") $action->exitError($err);
  $err=$doc->modify();
  if ($err != "") $action->exitError($err);
  print sprintf("Document <%s [%d]> has be renamed to %s\n",$oldtitle,$doc->id,$doc->getTitle());
 } else {
  print sprintf("Document <%s> is not alive\n",$docid);
 }


?>