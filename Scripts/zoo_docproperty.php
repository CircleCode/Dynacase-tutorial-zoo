<?php

/**
 * View property
 *
 * @author Anakeen 2008
 * @version $Id: zoo_docproperty.php,v 1.1 2010-01-15 15:19:40 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package freedom-zoo
 * 
 *
 * @global docid Http var : document identificator
 */
 /**
 */


include_once("FDL/Class.Doc.php");

$usage="usage  --docid=<doc identificator>";
$dbaccess=$action->GetParam("FREEDOM_DB");
if ($dbaccess == "") {
  print "Freedom Database not found : param FREEDOM_DB";
  exit;
}

$docid = 9;

$doc=new_doc($dbaccess,$docid);
if ($doc->isAlive()) {
  print_r($doc);
 } else {
  print sprintf("Document <%s> is not alive\n",$docid);
 }


?>