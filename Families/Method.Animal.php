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
Class _ANIMAL extends Doc {
    /*
     * @end-method-ignore
     */

    /**
     * return document identificator of ascendant
     *
     * @param string $sexeVar the sexe of ascendant M or F
     *
     * @return int|string
     */
    public function getAscendant($sexeVar) {
        $s = new SearchDoc($this->dbaccess,$this->getProperty('fromid')); //search in same family
        $s->addFilter("an_enfant ~ '\\\\y%d\\\\y'",$this->getProperty('initid'));
        $s->slice=3; //limit to 3 results
        // since we don't need objects (setObjectReturn is not invoked),
        // we get the result as an array of tdoc
        $tdocs = $s->search();

        //init blank value so that field is cleared if no result matches
        $resultat = " ";
        //if count == 0, does not enter the foreach.
        foreach($tdocs as $tdoc) {
            $sexe = getv($tdoc,"an_sexe");
            if ($sexe == $sexeVar){
                $resultat = $tdoc["initid"];
            }
        }
        return  $resultat;
    }

    /**
     * return document identificator of ascendant
     *
     * @param string $sexeVar the sexe of ascendant M or F
     *
     * @return int|string
     */
    public function getAscendant2($sexeVar) {
        $s=new SearchDoc($this->dbaccess,$this->getProperty('fromid')); //search in same family

        // search animal that contains our id in its children list
        $s->addFilter("an_enfant ~ '\\\\y%d\\\\y'",$this->getProperty('initid'));
        // search animal whose sex is correct
        $s->addFilter("an_sexe = '%s'",$sexeVar);

        $s->setObjectReturn();
        $s->slice=1; //limit results to 1
        $s->search();

        if ($s->count()==0){
            return " ";
        }

        $ascendant=$s->nextDoc();
        return  $ascendant->getProperty('initid');
    }

    public function postModify() {
        $err = parent::PostModify();
        $err .= ($err ? "\n" : '') . $this->refreshChildren();
        return $err;
    }

    public function preCreated() {
        $err = parent::PreCreated();
        if ($this->revision == 0){
            $err .= ($err ? "\n" : '') . $this->verifyCapacity();
        }
        return $err;
    }

    public function postCreated() {
        $err = parent::PostCreated();
        if ($this->revision == 0) {
            $err .= ($err ? "\n" : '') . $this->addToEnclos();
        }
        return $err;
    }

    /**
     * add this animal to the first compatible enclos found
     *
     * @return string
     */
    public function addToEnclos() {
        $enclosId = $this->getFreeEnclos();
        if(intval($enclosId)) {
            $enclos = new_doc($this->dbaccess, $enclosId);
            if ($enclos->isAlive()) {
                $animals = $enclos->getTValue("en_animaux");
                //add this animal to the list for this enclos
                array_push($animals, $this->id);
                // save the list back
                $err = $enclos->setValue("en_animaux",$animals);
                if ($err==""){
                    $err=$enclos->store();
                }
            } else {
                $err = _("zoo:enclos %s is not available");
            }
        } else {
            $err = $enclosId;
        }
        return $err;
    }

    /**
     * return first free gate compatible with species
     *
     * @return string the gate identificator or an error message
     */
    public function getFreeEnclos()
    {
        $idespece = $this->getValue("an_espece");

        $s = new SearchDoc($this->dbaccess, "ZOO_ENCLOS");
        $s->addFilter("en_espece ~ '\\\\y%d\\\\y'", $idespece);
        $s->addFilter("en_nbre < en_capacite");
        $s->noViewControl(); // no test view acl
        $s->setObjectReturn();
        $s->setSlice(1); //only 1 free gate is required
        $s->search();

        if ($s->count() == 0){
            return _("zoo:no enclos");
        } else {
            $enclos = $s->nextDoc();
            /* @var _ENCLOS $enclos */
            return $enclos->getProperty('initid');
        }
    }

    /**
     * constraint to verify entrance date and birth date
     *
     * @param string $date (format is ISO8601 or dd/mm/yyyy)
     *
     * @return string[]
     */
    public function validatePastDate($date) {
        if (is_array($date)){
            return true;
        }

        $sug = array();
        $err = "";

        $dateTS = StringDateToUnixTs($date);
        if ($dateTS > time()){
            $err = _("zoo:birthday date must be set before today");
            $sug[] = $this->getDate();
        }

        return array(
            "err" => $err,
            "sug" => $sug
        );
    }


    public function verifyCapacity() {
        // search enclos
        $s = new SearchDoc($this->dbaccess,"ZOO_ENCLOS");
        // filter only those that can accept this specie
        $s->addFilter("en_espece ~ '\\\\y%d\\\\y'",$this->getValue("an_espece"));

        $s->noViewControl(); // find enclos user can not see
        $s->setObjectReturn();
        $s->search();

        $nbEnclos=$s->count();

        if($nbEnclos > 0) {
            foreach ($s->getDocumentList() as $enclos) {
                /* @var _ENCLOS $enclos */
                if ($enclos->detectMaxCapacity() == "") {
                    return ""; // at least 1 place found, OK
                }
            }
            // there were enclos and no one had free space
            $err = _("zoo:all enclos are full");
        } else {
            $err = _("zoo:no enclos for this species");
        }
        return $err;
    }



    /**
     * refresh all children to recompute father and mother
     *
     * @return string error message
     */
    public function refreshChildren() {
        $err = "";

        // get current children
        $currentChildrenIds = $this->getTValue("an_enfant");
        //get previous children
        $oldChildrenIds = trim($this->getOldValue("an_enfant"));
        // convert it to an array (getOldValue return raw value)
        $oldChildrenIds = $this->_val2array($oldChildrenIds);

        // union unique of old and new values
        $childrenIds = array_unique(array_merge($currentChildrenIds, $oldChildrenIds));

        if (count($childrenIds) > 0) {
            $s = new SearchDoc($this->dbaccess, "ZOO_ANIMAL");
            // use special searchDoc::sqlCond() filter constructor
            // to obtain a 'in' condition
            /* @see SearchDoc::sqlcond */
            $s->addFilter($s->sqlCond($childrenIds, "initid", true));
            $s->noViewControl(); // no test view acl
            $s->setObjectReturn();

            foreach ($s->getDocumentList() as $animal) {
                /* @var _ANIMAL $animal */
                $err .= ($err ? "\n" : '') . $animal->refresh();
                $err .= ($err ? "\n" : '') . $animal->store();
            }
        }

        return $err;
    }

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
    /**
     * @begin-method-ignore
     * this part will be deleted when construct document class until end-method-ignore
     */
}

/*
 * @end-method-ignore
 */
?>


