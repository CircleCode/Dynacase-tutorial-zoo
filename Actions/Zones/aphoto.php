<?php

/**
 * Zone View animal photo
 *
 * @author Anakeen 2008
 * @version $Id: aphoto.php,v 1.1 2010-01-15 15:15:39 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package FREEDOM
 * @subpackage ZOO
 *
 */

 /**
  * View animal photo
  * @global id Http var : document identificator
  */   
function aphoto(&$action) {
  $idanimal=getHTTPVars("id");
  $dbaccess=$action->getParam("FREEDOM_DB");
  $doc= new_Doc($dbaccess,$idanimal);
  $photo=$doc->getHtmlAttrValue("AN_PHOTO");

  $action->lay->set("anid",$idanimal);
  $action->lay->set("photo",$photo);
  $action->lay->set("aname",$doc->title);
  }

?>



