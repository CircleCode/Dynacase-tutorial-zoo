<?php

/**
 * Rename Animal
 *
 * @author Anakeen 2008
 * @version $Id: zoo_animalrename.php,v 1.5 2010-09-03 07:07:12 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package dynacase-zoo
 *
 * @global Action $action
 */

require_once "FDL/freedom_util.php";

$usage = new ApiUsage();
$usage->setText("Rename an animal");
$docid = $usage->addNeeded("docid", "animal docid");
$newName = $usage->addNeeded("newName", "animal new name");
$usage->verify();

// Check database access
$dbaccess = $action->dbaccess;
if ($dbaccess == "") {
    $action->exitError("Database not found");
}

$animal = new_Doc($dbaccess, $docid, true);

if ($animal->isAlive()) {

    //ensure this animal is really an animal
    if ($animal->fromname != 'ZOO_ANIMAL') {
        // This is not an animal!
        // get its family and then alert user
        $famDoc = $animal->getFamDoc();
        $action->exitError(
            sprintf("%s [%d] document is not an animal (it is a %s)",
                $animal->getTitle(),
                $animal->id,
                $famDoc->getTitle()
            )
        );
    }

    // This is an animal
    /* @var _ZOO_ANIMAL $animal */
    $oldtitle = $animal->getTitle();

    $err = $animal->setValue("an_nom", $newName);
    if ($err != "") {
        $action->exitError($err);
    }

    $err = $animal->store();
    if ($err != "") {
        $action->exitError($err);
    }

    //Everything is OK, tell it
    printf(
        "Document [%d] has been renamed from %s to %s\n",
        $animal->id,
        $oldtitle,
        $animal->getTitle()
    );
} else {
    printf("Document [%s] is not alive\n", $docid);
}


?>
