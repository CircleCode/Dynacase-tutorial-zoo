<?php
/**
 * function use for specialised searches
 *
 * @author Anakeen 2006
 * @version $Id: zoosearch.php,v 1.2 2010-01-25 13:45:18 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package freedom-zoo
 */
 /**
 */
function mylockedocument($start,$slice,$userid=0) {
  include_once("FDL/Class.SearchDoc.php");
  $dbaccess=getParam("FREEDOM_DB");
  $s=new SearchDoc($dbaccess);
  $s->addFilter("locked=$userid");
  
  $tdoc= $s->search();
  
  return $tdoc;

}
?>