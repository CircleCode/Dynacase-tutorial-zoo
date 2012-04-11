<?php

/**
 * View properties of a document
 *
 * @author Anakeen 2008
 * @version $Id: zoo_docproperty.php,v 1.5 2011-02-01 16:40:08 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package dynacase-zoo
 *
 * @global Action $action
 */


require_once "FDL/freedom_util.php";

$usage = new ApiUsage();
$usage->setText("View properties of a document");
$docid = $usage->addOption("docid", "document id", array(), 9);
$usage->verify();

// Check database access
$dbaccess = $action->dbaccess;
if ($dbaccess == "") {
    $action->exitError("Database not found");
}

$doc = new_doc($dbaccess, $docid);
//$doc = new_doc('', $docid);
if ($doc->isAlive()) {
    print get_class($doc) . ":" . $doc->getTitle() . "\n";
    //print_r($doc);
} else {
    print sprintf("Document <%s> is not alive\n", $docid);
}

?>