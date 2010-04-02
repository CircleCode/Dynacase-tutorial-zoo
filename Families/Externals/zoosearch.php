<?php
/**
 * function use for specialised searches
 *
 * @author Anakeen 2006
 * @version $Id: zoosearch.php,v 1.3 2010-04-02 14:49:04 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package freedom-zoo
 */
 /**
 */
function mylockedocument($start,$slice,$userid=0) {
  include_once("FDL/Class.SearchDoc.php");
  $dbaccess=getParam("FREEDOM_DB");
  $s=new SearchDoc($dbaccess);
  $s->slice=$slice;
  $s->start=$start;
  if ($userid == 1) $s->addFilter(sprintf("locked=%d",$userid));
  else $s->addFilter(sprintf("abs(locked)=%d",$userid));
  
  $tdoc= $s->search();
  
  return $tdoc;

}
?>