<?php
/**
 * Animal comportment
 *
 * @author Anakeen 2010
 * @version $Id: Method.Animal.php,v 1.9 2011-02-01 16:40:08 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package freedom-zoo
 */
/**
 */

/**
 * @begin-method-ignore
 * this part will be deleted when construct document class until end-method-ignore
 */
Class _ANIMAL extends Doc {
    /*
     * @end-method-ignore
     */
    /**
     * return document identificator of ascendant
     * @param string $sexeVar the sexe of ascendant M or F
     * @return int
     */
    public function getAscendant($sexeVar) {
        include_once("FDL/Class.SearchDoc.php");
	$resultat=" ";
        $s=new SearchDoc($this->dbaccess,$this->getProperty('fromid'));
        $s->addFilter("an_enfant ~ '\\\\y%d\\\\y'",$this->getProperty('initid'));
        $s->slice=3;
        $tdoc=$s->search();
        if (count($tdoc)==0) return " ";
        foreach($tdoc as $k=>$v) {
            $sexe=getv($v,"an_sexe");
            if ($sexe==$sexeVar) $resultat= $v["initid"];
        }

        return  $resultat;
    }

    public function getAscendant2($sexeVar) {
        include_once("FDL/Class.SearchDoc.php");

        $s=new SearchDoc($this->dbaccess,$this->getProperty('fromid'));
        $s->addFilter("an_enfant ~ '\\\\y%d\\\\y'",$this->getProperty('initid'));
        $s->addFilter("an_sexe = '%s'",$sexeVar);
	$s->setObjectReturn();
        $s->slice=1;
        $s->search();
        if ($s->count()==0) return " ";
	$ani=$s->nextDoc();	       
        return  $ani->getProperty('initid');
    }
    public function postModify() {
        return $this->refreshChilds();
    }
    public function preCreated() {
        if ($this->revision == 0) return $this->verifyCapacity();
    }
    public function postCreated() {
        $err="";
        if ($this->revision == 0) {
            $err=$this->addToEnclos();
        }
        return $err;
    }

 

    public function addToEnclos() {
        $enclosId=$this->getFreeEnclos();
        $err="";
        if ($enclosId>0) {
            $enclos=new_doc($this->dbaccess, $enclosId);
            if ($enclos->isAlive()) {
                $animals=$enclos->getTValue("en_animaux");
                array_push($animals, $this->id);
                $err=$enclos->setValue("en_animaux",$animals);
                if ($err=="") $err=$enclos->modify();
            }
        }
        return $err;
    }
   /**
     * constraint to verify entrance date and birth date
     */
    public function validatePastDate($date) {
        if (is_array($date)) return true;
        $t1=StringDateToUnixTs($date);
        $sug=array();
        $err="";
        if ($t1 > time()) $err=_("zoo:birthday date must be set before today");
        if ($err!="") $sug[]=$this->getDate();
        return array("err"=>$err,
                   "sug"=>$sug);

    }
    /**
     * return first free gate compatible with species
     * @return int the gate identificator
     */
    public function getFreeEnclos() {
        include_once("FDL/Class.SearchDoc.php");

        $s=new SearchDoc($this->dbaccess,"ZOO_ENCLOS");
        $idespece=$this->getValue("an_espece");
        $s->addFilter("en_espece ~ '\\\\y%d\\\\y'",$idespece);
        $s->addFilter("en_nbre < en_capacite");
        $s->noViewControl(); // no test view acl
        $s->setObjectReturn();
        $s->search();

        $nbdoc=$s->count();

        if ($nbdoc==0) $err=_("zoo:no enclos");
        else {
            while ($enclos=$s->nextDoc()) {
                return $enclos->getProperty('initid'); // first found
            }
        }
        return 0;
    }


    public function verifyCapacity() {
        include_once("FDL/Class.SearchDoc.php");

        $err="";
        $s=new SearchDoc($this->dbaccess,"ZOO_ENCLOS");
        $s->addFilter("en_espece ~ '\\\\y%d\\\\y'",$this->getValue("an_espece"));
        $s->noViewControl(); // no test view acl
        $s->setObjectReturn();
        $s->search();

        $nbdoc=$s->count();

        if ($nbdoc==0) $err=_("zoo:no enclos for this species");
        else {
            while ($enclos=$s->nextDoc()) {
                $err=$enclos->detectMaxCapacity();
                if ($err=="") {
                    break; // first found
                }
            }
            if ($err!="") $err=sprintf(_("zoo:each enclos are full : %s"),$err);
        }
        return $err;
    }



    /**
     * refresh all childs to recompute father and mother
     */
    public function refreshChilds() {
        $err="";
        $idchild=$this->getTValue("an_enfant");
        $oldidchild=$this->_val2array($this->getOldValue("an_enfant"));
        // union unique of old and new values
        $childs=array();
        foreach ($idchild as $child) if ($child) $childs[$child]=$child;
        foreach ($oldidchild as $child) if ($child) $childs[$child]=$child;

        if (count($childs) >0) {
            include_once("FDL/Class.SearchDoc.php");
            $s=new SearchDoc($this->dbaccess,"ZOO_ANIMAL");
            $s->addFilter($s->sqlCond($childs,"initid",true)); // get all animals from ids
            $s->noViewControl(); // no test view acl
            $s->setObjectReturn();
            $s->search();

            while ($doc=$s->nextDoc()) {
                $doc->refresh();
            }
        }

        return $err;
    }

    /**
     * return id of its health card
     */
    function getHealthCardId() {
        include_once("FDL/Class.SearchDoc.php");

        $s=new SearchDoc($this->dbaccess,"ZOO_CARNETSANTE");
        $s->addFilter("ca_idnom =".$this->initid);
        $s->noViewControl(); // no test view acl
        $s->slice=3;
        $tdoc=$s->search();

        if (count($tdoc)==1) return $tdoc[0]["id"];
        return 0;
    }

    /**
     * create its health card
     */
    function createHealthCard() {
        $hc=createDoc($this->dbaccess,"ZOO_CARNETSANTE");
        if ($hc) {
            $hc->setValue("ca_idnom",$this->initid);
            $err=$hc->Add();
            $hc->refresh();

        }

        return $err;
    }

    /**
     * view to notify veterinary
     */
    public function de_mail_sick($target="_self",$ulink=true,$abstract=false) {
        $this->viewdefaultcard($target,$ulink,$abstract);
        $idcarnet=$this->getHealthCardId();
        $this->lay->set("idcarnet",$idcarnet);
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


