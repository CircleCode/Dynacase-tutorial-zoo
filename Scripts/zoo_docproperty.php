<?php

/**
 * View property
 *
 * @author Anakeen 2008
 * @version $Id: zoo_docproperty.php,v 1.5 2011-02-01 16:40:08 eric Exp $
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
$dbaccess=$action->getParam("FREEDOM_DB");
if ($dbaccess == "") {   
  $action->exitError( "Freedom Database not found : param FREEDOM_DB");
}
$docid = $action->getArgument("docid",9);
$doc=new_doc($dbaccess,$docid);
if ($doc->isAlive()) {
  print get_class($doc).":".$doc->getTitle()."\n";
 } else {
  print sprintf("Document <%s> is not alive\n",$docid);
 }


?>