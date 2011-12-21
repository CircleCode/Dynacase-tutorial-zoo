<?php

/**
 * Display an xml report for all animals
 *
 * @author Anakeen 2008
 * @version $Id: zoo_animalreport.php,v 1.5 2010-04-20 07:55:45 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package dynacase-zoo
 * 
 *
 * @global Action $action
 */

require_once "FDL/freedom_util.php";

$usage = new ApiUsage();
$usage->setText("Display an xml report for all animals");
$usage->verify();

// Check database access
$dbaccess = $action->dbaccess;
if ($dbaccess == "") {
    $action->exitError("Database not found");
}

// search animals
$s = new SearchDoc($dbaccess,"ZOO_ANIMAL");
$tdoc = $s->search();

$action->lay = new Layout(getLayoutFile("ZOO","zoo_animalReport.xml"), $action);
// Note that we can directly use the result of searchdoc
// without object return to populate a template
// in this case, the template MUST use attribute names as keys.
$action->lay->setBlockData("ANIMALS",$tdoc);

$action->lay->set("anicount",$s->count());

print $action->lay->gen();

?>