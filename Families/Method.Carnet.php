<?php
/**
 * Carnet comportment
 *
 * @author Anakeen 2010
 * @version $Id: Method.Carnet.php,v 1.4 2010-04-20 07:55:44 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package freedom-zoo
 */
/**
 */

/**
 * @begin-method-ignore
 *
 * @property Layout lay
 * this part will be deleted when construct document class until end-method-ignore
 */
Class _CARNET extends Doc {
    /*
     * @end-method-ignore
     */
    public $cviews=array("ZOO:VIEWORDONNANCE");

    //test de code
    function getTime()
    {
        return strftime("%T",time());
    }


    /**
     * carnet de sante
     * display new ordonnance
     * @param string $target
     * @param bool $ulink
     * @param bool $abstract
     */
    function viewordonnance($target="_self",$ulink=true,$abstract=false) {
        $idveto=Action::getArgument('ca_idveterinaire');
        $idanimal=Action::getArgument('ca_idnom');
        $dateveto=Action::getArgument('ca_date');
        $desc=Action::getArgument('ca_description');

        $doc=new_Doc($this->dbaccess,$idveto);
        $this->lay->set("us_lname",$doc->getValue("us_lname"));
        $this->lay->set("us_fname",$doc->getValue("us_fname"));

        $this->lay->set("dateveto",$dateveto);
        $this->lay->set("v_desc",$desc);
        $docAnimal=new_Doc($this->dbaccess,$idanimal);
        $this->lay->set("an_nom",$docAnimal->getValue("an_nom"));

    }

    /**
     * carnet de sante
     * view resume of diseases
     * @param string $target
     * @param bool $ulink
     * @param bool $abstract
     */
    function maladie($target="_self",$ulink=true,$abstract=false) {

        $this->lay->set("today",$this->getDate());
        $animal=new_doc($this->dbaccess,$this->getValue("ca_idnom"));
        if ($animal->isAlive()) {
            $this->lay->set("animal_name",$animal->getValue("an_nom"));
            $this->lay->set("espece",$animal->getHTMLAttrValue("an_espece"),$target,$ulink);
            $this->lay->set("classe",$animal->getHTMLAttrValue("an_classe"),$target,$ulink);
            $this->lay->set("tatouage",$animal->getValue("an_tatouage"));
            $this->lay->set("n",count($this->getTValue("ca_date")));
        } else {
            addWarningMsg(_("zoo:the animal document is not found"));
        }

        $vetos=$this->getTValue("ca_idveterinaire");
        if (count($vetos) > 0) {
            $vetoid=$vetos[0];
            $veto=new_doc($this->dbaccess,$vetoid);
            if ($veto->isAlive()) {
                $this->lay->set("vetoname",$veto->getValue("us_fname").' '.$veto->getValue("us_lname"));
            } else {
                addWarningMsg(_("zoo:the veto document is not found"));
            }
        }
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
