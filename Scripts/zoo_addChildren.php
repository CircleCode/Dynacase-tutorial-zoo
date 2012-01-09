<?php

/**
 * Add child to an Animal
 *
 * @author Anakeen 2008
 * @version $Id: zoo_addchild.php,v 1.6 2011-03-21 11:14:44 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package freedom-zoo
 *
 * @global Action $action
 */

require_once "FDL/freedom_util.php";

$usage = new ApiUsage();
$usage->setText("Add children to an animal");
$docid = $usage->addNeeded("docid", "animal docid");
$nbChildren = $usage->addNeeded("nbChildren", "number of children to create");
$usage->verify();

// Check database access
$dbaccess = $action->dbaccess;
if ($dbaccess == "") {
    $action->exitError("Database not found");
}

$parent = new_doc($dbaccess, $docid, true);

if ($parent->isAlive()) {

    //ensure this doc is really an animal
    if ($parent->fromname != 'ZOO_ANIMAL') {
        //This is not an animal!
        // get its family and then alert user
        $fdoc = $parent->getFamDoc();
        $action->exitError(
            sprintf("%s [%d] document is not an animal (it is a %s)",
                $parent->getTitle(),
                $parent->id,
                $fdoc->getTitle()
            )
        );
    }

    // This is an animal
    $children = $parent->getTValue("an_enfant");
    $nbCurrentChildren = count($children);

    $err = "";

    for ($i = 0; $i < $nbChildren; $i++) {
        $newChild = createDoc($dbaccess, "ZOO_ANIMAL");
        /* @var _ZOO_ANIMAL $newChild */
        if ($newChild === false) {
            $action->exitError("cannot create ANIMAL");
        }

        // init new animal values
        $err .= $newChild->setValue(
            "an_nom",
            sprintf("%s Junior %d",
                $parent->getValue("an_nom"),
                ($nbCurrentChildren + $i + 1)
            )
        );
        $err .= $newChild->setValue("an_espece", $parent->getValue("an_espece"));
        $err .= $newChild->setValue("an_naissance", $parent->getDate());
        $err .= $newChild->setValue("an_entree", $parent->getDate());

        if (!$err) {
            //save this animal
            $err = $newChild->store();
        }

        if ($err) {
            $action->exitError($err);
        }

        //everything is OK for thios child, report it to the user
        printf("%s [%d] created\n", $newChild->getTitle(), $newChild->id);

        // Append this child to the list of children
        $children[] = $newChild->getProperty('initid');
    }

    //store the new list of children
    $err = $parent->setValue("an_enfant", $children);

    if (!$err) {
        $err = $parent->store();
    }

    if ($err) {
        $action->exitError($err);
    }

    printf("Document <%s [%d]> has new children\n", $parent->getTitle(), $parent->id);
} else {
    printf("Document <%s> is not alive\n", $docid);
}


?>
