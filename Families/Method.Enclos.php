<?php
/**
 * Enclos family
 *
 * @author Anakeen 2010
 * @version $Id: Method.Enclos.php,v 1.5 2011-02-01 16:40:08 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package dynacase-zoo
 */

/**
 * @begin-method-ignore
 *
 * @property Layout lay
 * this part will be deleted when construct document class until end-method-ignore
 */
Class _ENCLOS extends Doc
{
    /*
      * @end-method-ignore
      */
    public $defaultview = "ZOO:VIEWENCLOS";


    public function specRefresh()
    {
        $msg = parent::SpecRefresh();
        $msg .= ($msg ? "\n" : '') .  $this->detectMaxCapacity();
        return $msg;
    }

    /**
     * verify capacity
     * @return string error message if maximum capacity reached
     */
    public function detectMaxCapacity()
    {
        $err = "";
        $nbAnimaux = $this->getNbAnimaux();
        if ($nbAnimaux == intval($this->getValue("en_capacite"))) {
            $err =  _("zoo:Full Area");
        } elseif ($nbAnimaux > intval($this->getValue("en_capacite"))) {
            $err = (sprintf(_("zoo:Maximum Capacity reached %d > %d"),
                $nbAnimaux,
                intval($this->getValue("en_capacite")))
            );
        }
        return $err;
    }

    /**
     * return count of animals
     * @return int
     */
    public function getNbAnimaux()
    {
        return count($this->getTValue("en_animaux"));
    }

    /**
     * default view for enclos to see animal's photo
     *
     * @param string $target hyperlinks target
     * @param bool $ulink enable hyperlinks
     * @param bool $abstract limit attributes to abstract
     *
     * @return string warning message
     */
    function viewenclos($target = "_self", $ulink = true, $abstract = false)
    {
        $this->viewdefaultcard($target, $ulink, $abstract);

        $anidT = $this->getTValue("en_animaux");
        $anid = array();
        foreach ($anidT as $val) {
            $anid[] = array("anid"=> $val);
        }

        $this->lay->setBlockData("PHOTO", $anid);
    }
    /**
     * @begin-method-ignore
     * this part will be deleted when construct document class until end-method-ignore
     */
}

/*
* @end-method-ignore
*/

?>
