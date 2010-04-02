<?php

/**
 * List Animal
 *
 * @author Anakeen 2008
 * @version $Id: zoo_animallist.php,v 1.3 2010-04-02 14:49:05 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package freedom-zoo
 * 
 *
 * @global docid Http var : document identificator
 * @global newname Http var : new animal name
 /**
 */


include_once("FDL/Class.Doc.php");
include_once("FDL/Lib.Dir.php");

$usage="usage  --mode=[TABLE|LIST|ITEM] ";

$dbaccess=$appl->GetParam("FREEDOM_DB");
if ($dbaccess == "") {
  print "Freedom Database not found : param FREEDOM_DB";
  exit;
}

$mode=(GetHttpVars("mode"));
if (! $mode)   $action->exitError("mode needed :\n $usage");  

$filtre=array();
$famid= getFamIdFromName($dbaccess,"ZOO_ANIMAL");
$sqlorder="an_espece,an_nom";

$time_start = microtime(true);
if ($mode=="LIST") {
  //------------------------------
  // FIRST TRY WITH LIST
  $memory1=memory_get_usage();
  $tdoc= getChildDoc($dbaccess,0,
		     "0","ALL",
		     $filtre,  			
		     $action->user->id,
		     "LIST",
		     $famid,false,$sqlorder  );

  $memory2=memory_get_usage();
  foreach ($tdoc as $k=>$v) {
    print sprintf("%03d) %20s : %s\n",$k,$v->getValue("an_espece"),$v->title);
  }
  $memory3=memory_get_usage();

 }

if ($mode=="TABLE") {
  //------------------------------
  // SECOND TRY WITH TABLE
  $memory1=memory_get_usage();
  $tdoc= getChildDoc($dbaccess,0,
		     "0","ALL",
		     $filtre,  			
		     $action->user->id,
		     "TABLE",
		     $famid,false,$sqlorder  );

  $memory2=memory_get_usage();
  foreach ($tdoc as $k=>$v) {
    print sprintf("%03d) %20s : %s\n",$k,getv($v,"an_espece"),$v["title"]);
  }
  $memory3=memory_get_usage();

 }

if ($mode=="ITEM") {
  //------------------------------
  // THIRD TRY WITH ITEM
  $memory1=memory_get_usage();
  $tdoc= getChildDoc($dbaccess,0,
		     "0","ALL",
		     $filtre,  			
		     $action->user->id,
		     "ITEM",
		     $famid,false,$sqlorder  );

  $memory2=memory_get_usage();
  $k=0;
  while ($doc=getNextDoc($dbaccess,$tdoc)) {
    print sprintf("%03d) %20s : %s\n",$k,$doc->getValue("an_espece"),$doc->title);
    $k++;
  }
  $memory3=memory_get_usage();
 }
print sprintf("$mode memory %dKo + %dKo = %dKo ",($memory2 - $memory1)/1024, 
	      ($memory3 - $memory3)/1024,
	      ($memory3 - $memory1)/1024);
print sprintf("in %.03f second\n",microtime(true)-$time_start);


?>