<?php

/**
 * Count Animal by Classe
 *
 * @author Anakeen 2008
 * @version $Id: zoo_countanimal.php,v 1.7 2011-02-01 16:40:08 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package freedom-zoo
 *
 * @global Action $action
 */

require_once "FDL/freedom_util.php";

$usage = new ApiUsage();
$usage->setText("count animals for a given class");
$latinClass = $usage->addNeeded("class", "latin class");
$usage->verify();

// Check database access
$dbaccess = $action->dbaccess;
if ($dbaccess == "") {
    $action->exitError("Database not found");
}

// search the classe document
$s = new SearchDoc($dbaccess, "ZOO_CLASSE");
$s->addFilter("lower(cl_nomscientifique) = '%s'", strtolower($latinClass));
$s->setObjectReturn();
$s->setSlice(1);
$s->search();

if ($s->count() == 0) {
    // we did not found a class with this latin name
    // so we propose all classes to the user
    $s = new SearchDoc($dbaccess, "ZOO_CLASSE");
    $s->setObjectReturn();
    $s->search();

    $availableClasses = "";
    foreach ($s->getDocumentList() as $classe) {
        /* @var Doc $classe */
        $availableClasses .= sprintf(
            "\t%s [%d] - %s\n",
            $classe->getTitle(),
            $classe->getProperty('initid'),
            $classe->getValue('cl_nomscientifique')
        );
    }
    $action->exitError(
        sprintf(
            "class %s not found. available classes are\n%s",
            $latinClass,
            $availableClasses
        )
    );
}

// get first class found
$classe = $s->nextDoc();

// now search animals with this class

$s = new SearchDoc($dbaccess, "ZOO_ANIMAL");
$s->setObjectReturn();
$s->addFilter("an_classe='%d'", $classe->getProperty('initid'));
$s->onlyCount();

printf("Class %s : %d animals found.\n", $classe->getTitle(), $s->count());

?>