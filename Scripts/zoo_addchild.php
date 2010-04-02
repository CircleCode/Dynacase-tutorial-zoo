<?php

/**
 * Add child to an Animal
 *
 * @author Anakeen 2008
 * @version $Id: zoo_addchild.php,v 1.3 2010-04-02 14:49:05 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package freedom-zoo
 * 
 *
 * @global docid Http var : document identificator
 * @global n Http var : number of new childs
 /**
 */


include_once("FDL/Class.Doc.php");

$usage="usage  --docid=<doc identificator> --n=<number of child>";

$dbaccess=$appl->GetParam("FREEDOM_DB");
if ($dbaccess == "") {
  print "Freedom Database not found : param FREEDOM_DB";
  exit;
}

$docid = $action->getArgument("docid",0); // special docid
$n = intval($action->getArgument("n")); // 

if (! $n)   $action->exitError("n needed :\n $usage");  
if ($n<0) $action->exitError("n must be greater than 0 :\n $usage");  

$doc=new_doc($dbaccess,$docid);
if ($doc->isAlive()) {
  $animalid=getFamIdFromName($dbaccess,'ZOO_ANIMAL');
  if ($doc->fromid != $animalid) {
    $fdoc=$doc->getFamDoc();
    $action->exitError(sprintf("%s [%d] document is not an animal (it is a %s)",
			       $doc->title,$doc->id,$fdoc->title));
  }
  
  $childs=$doc->getTValue("an_enfant");
  $nc=count($childs);

  for ($i=0;$i<$n;$i++) {
    $anchild=createDoc($dbaccess,"ZOO_ANIMAL");
    if (! $anchild) $action->exitError("cannot create ANIMAL");
    $anchild->setValue("an_nom",sprintf("%s Junior %d",
					$doc->getValue("an_nom"),($nc+$i+1)));
    $err=$anchild->setValue("an_espece",$doc->getValue("an_espece"));
    $err.=$anchild->setValue("an_naissance",$doc->getDate());
    $err.=$anchild->setValue("an_entree",$doc->getDate());    
    if (! $err) $err=$anchild->Add();      
    if ($err != "") $action->exitError($err);
    $anchild->refresh();
    $anchild->postModify();
    print sprintf("%s [%d] created\n",$anchild->title,$anchild->id);
    $childs[]=$anchild->id;		    
  }

  if ($err != "") $action->exitError($err);
  $err=$doc->setValue("an_enfant",$childs);
  $err=$doc->modify();
  if ($err != "") $action->exitError($err);
  print sprintf("Document <%s [%d]> has new childs\n",$doc->title,$doc->id);
 } else {
  print sprintf("Document <%s> is not alive\n",$docid);
 }


?>