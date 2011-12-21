<?php
/**
 * function use for specialised searches
 *
 * @author Anakeen 2006
 * @version $Id: zoosearch.php,v 1.3 2010-04-02 14:49:04 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package dynacase-zoo
 */

/**
 * @param int $start
 * @param int $slice
 * @param int $userid which user is executing the search
 *
 * @return array the results
 */
function mylockedocument($start, $slice, $userid = 0)
{
    $dbaccess = getParam("FREEDOM_DB");
    $s = new SearchDoc($dbaccess);
    $s->slice = $slice;
    $s->start = $start;
    if ($userid == 1) {
        $s->addFilter(sprintf("locked=%d", $userid));
    } else {
        $s->addFilter(sprintf("abs(locked)=%d", $userid));
    }
    $tdoc = $s->search();

    return $tdoc;

}

?>