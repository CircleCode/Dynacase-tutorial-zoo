<?php
/**
 * Animal comportment
 *
 * @author Anakeen 2010
 * @version $Id: Method.Animal.php,v 1.9 2011-02-01 16:40:08 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package dynacase-zoo
 */

/**
 * @begin-method-ignore
 *
 * @property Layout lay
 * this part will be deleted when construct document class until end-method-ignore
 */
Class _ZOO_ANIMAL extends Doc {
    /*
     * @end-method-ignore
     */

    /**
     * return id of its health card
     *
     * @return int
     */
    function getHealthCardId() {
        $s = new SearchDoc($this->dbaccess,"ZOO_CARNETSANTE");
        $s->addFilter("ca_idnom = %d", $this->getProperty('initid'));
        $s->noViewControl(); // no test view acl
        $s->slice=3;
        $tdoc=$s->search();

        if (count($tdoc)==1) return $tdoc[0]["initid"];
        return 0;
    }

    /**
     * create its health card
     *
     * @return string
     */
    function createHealthCard() {
        $err = '';
        $hc=createDoc($this->dbaccess,"ZOO_CARNETSANTE");
        if ($hc) {
            $err = $hc->setValue("ca_idnom",$this->initid);
            if($err == ''){
                $err = $hc->store();
            }

        }

        return $err;
    }

    /**
     * view to notify veterinary
     * @param string $target
     * @param bool $ulink
     * @param bool $abstract
     */
    public function de_mail_sick($target="_self",$ulink=true,$abstract=false) {
        $this->viewdefaultcard($target,$ulink,$abstract);
        $idcarnet=$this->getHealthCardId();
        $this->lay->set("idcarnet",$idcarnet);
    }

    public function validatePastDate(){}
    /**
     * @begin-method-ignore
     * this part will be deleted when construct document class until end-method-ignore
     */
}
/*
 * @end-method-ignore
 */
?>


