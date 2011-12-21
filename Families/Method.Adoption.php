<?php
/**
 * Adoption comportment
 *
 * @author Anakeen 2010
 * @version $Id: Method.Adoption.php,v 1.4 2011-02-01 16:40:08 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package dynacase-zoo
 */

/**
 * @begin-method-ignore
 *
 * @property Layout lay
 * this part will be deleted when construct document class until end-method-ignore
 */
Class _ADOPTION extends Doc {
    /*
     * @end-method-ignore
     */
    function postCreated() {
        $err = parent::PostCreated();
        if ($this->revision==0) {
            $err .= ($err ? "\n" : '') . $this->setReference();
        }
        return $err;
    }

    /**
    * set unique reference
     * @return string
     */
    function setReference() {
        $err = $this->setValue("de_reference",$this->getCurSequence());
        if($err == ''){
            $err = $this->store();
        }
        return $err;
    }

    /**
     * constraint to verify entrance date and birth date
     * @param $date
     * @return array
     */
    function verifyDate($date) {
        $err = '';
        $sug=array();

        $t1=stringDateToJD($date);
        if ($t1 > stringDateToJD($this->getDate())) {
            $err=_("birthday date must be set before today");
        }
        if ($err!="") {
            $sug[]=$this->getDate();
        }
        return array(
            "err"=>$err,
            "sug"=>$sug
        );
    }


    function de_mail_transmitted() {
        $s = new SearchDoc($this->dbaccess,"ZOO_ANIMAL");
        $s->addFilter(sprintf("an_espece = '%d'", $this->getValue("de_idespece")));
        $t=$s->search();

        $this->lay->setBlockData("ANIMALS",$t);
        $this->viewdefaultcard();
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