<?php
/**
 * Create or update animal folder
 *
 * @author Anakeen 2008
 * @version $Id: zoo_animalfolder.php,v 1.6 2011-02-01 16:40:08 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package freedom-zoo
 *
 *
 */

include_once("FDL/Class.Doc.php");
include_once("FDL/Lib.Dir.php");
/**
 * create or update animal folder
 * @global id Http var : animal document Identificator
 */
function zoo_animalfolder(Action &$action) {
    $id=$action->getArgument("id");
    $dbaccess=$action->getParam("FREEDOM_DB");

    $doc=new_Doc($dbaccess, $id, true);
    if ($doc->isAlive()) {

        if ($doc->fromname != "ZOO_ANIMAL") {
            $err=sprintf(_("document %s (%d) is not an animal"),$doc->title, $doc->id);
            $action->exitError($err);
        }
        $name=sprintf("AnimalFolder%d",$doc->initid);
        $fld=new_doc($dbaccess,$name);
        if (! $fld->isAlive()) {
            $fld=createDoc($dbaccess, "PORTFOLIO");
            $err=$fld->Add();
            if (! $err) $err=$fld->setLogicalIdentificator($name);
            if ($err) $action->exitError($err);
        }
	//	if ($doc->getValue("an_photo")) $fld->icon=$doc->getValue("an_photo");
        $fld->setValue("ba_title",sprintf(_("Folder of %s"),$doc->getTitle()));
        $fld->setValue("ba_desc",sprintf(_("Information about [ADOC %d]"),$doc->id));
        $err=$fld->modify();
        if ($err=="") $err=$fld->addFile($doc->initid);
        if ($err=="") {
            $err=$fld->addFile($doc->getValue("an_espece"));
            $err.=$fld->addFile($doc->getValue("an_classe"));
        }

        // first guide card parents
        $fatherid=$doc->getValue("an_pere");
        $motherid=$doc->getValue("an_mere");
        if ($fatherid || $motherid) {
            $namefolio1=sprintf("AnimalParents%d",$doc->initid);
            $folio=new_doc($dbaccess,$namefolio1);
            if (! $folio->isAlive()) {
                $folio=createDoc($dbaccess, "GUIDECARD");
                $err=$folio->Add();
                 if (! $err) $err=$folio->setLogicalIdentificator($namefolio1);
                if ($err) $action->exitError($err);
            }
            $folio->setValue("ba_title",sprintf(_("Parents of %s"),$doc->title));
            $folio->setValue("ba_desc",sprintf(_("Father [ADOC %d]\nMother [ADOC %d]"),
            $doc->getValue("an_pere"),
            $doc->getValue("an_mere") ));
            $err=$folio->modify();
            if ($err=="") $err=$fld->addFile($folio->initid);
            if ($err=="") {
                if ($fatherid) $err=$folio->addFile($fatherid);
                if ($motherid) $err.=$folio->addFile($motherid);
            }
        }
        // second guide card childs
        if ($err=="") {
            $childids=$doc->getTValue("an_enfant");
            if (count($childids)>0) {
                $namefolio2=sprintf("AnimalChilds%d",$doc->initid);
                $folio=new_doc($dbaccess,$namefolio2);
                if (! $folio->isAlive()) {
                    $folio=createDoc($dbaccess, "GUIDECARD");
                    $err=$folio->Add();
                    if (! $err) $err=$folio->setLogicalIdentificator($namefolio2);
                    if ($err) $action->exitError($err);
                }
                $folio->setValue("ba_title",sprintf(_("Childs of %s"),$doc->title));
                $folio->setValue("ba_desc",sprintf(_("All childs of [ADOC %d]"),$doc->id));
                 
                $err=$folio->modify();
                $fld->addFile($folio->initid);
                //    $folio->QuickInsertMSDocId(($doc->getTValue("an_enfant")));

                foreach ($childids as $k=>$v) {
                    $folio->addFile($v);
                }
            }
        }
    }

    if ($err) $action->addWarningMsg($err);
    redirect($action,"FREEDOM","OPENFOLIO&id=".$fld->id);

}

?>