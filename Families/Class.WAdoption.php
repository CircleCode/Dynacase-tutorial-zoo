<?php

class WAdoption extends WDoc
{
    /* Required: used as a db prefix for generated attributes */
    public $attrPrefix = "WAD";

    /* states */
    const initialised = "zoo_initialised"; # _("zoo_initialised")
    const transmited = "zoo_transmited"; # _("zoo_transmited")
    const accepted = "zoo_accepted"; # _("zoo_accepted")
    const refused = "zoo_refused"; # _("zoo_refused")
    const realised = "zoo_realised"; # _("zoo_realised")

    /* transitions */
    const Ttransmited = "zoo_Ttransmited"; # _("zoo_Ttransmited")
    const Taccepted = "zoo_Taccepted"; # _("zoo_Taccepted")
    const Trefused = "zoo_Trefused"; # _("zoo_Trefused")
    const Tretry = "zoo_Tretry"; # _("zoo_Tretry")
    const Trealised = "zoo_Trealised"; # _("zoo_Trealised")
    

    public $firstState = self::initialised;

    /* do not allow to change state from edit mode */
    public $viewlist = "none";
    
    public $transitions = array(
        self::Ttransmited => array(
            "m1" => "verifyValidatorMail",
            "m2" => "sendTransmitedMail"
        ),
        self::Taccepted => array(
            "m1" => "verifyEnclosDispo",
            "m2" => "createAnimal"
        ),
        self::Trefused => array(
            "m1" => "notifyReject",
            "m2" => "sendRefusedMail",
            "nr" => true,
            "ask" => array(
                "wad_refus"
            )
        ),
        self::Trealised => array(
            "m1" => "verifyEnclosDispo",
            "m2" => "createAnimal"
        ),
        self::Tretry => array(
            "m1" => "",
            "m2" => "sendRetryMail"
        )
    );
    
    public $cycle = array(
        array(
            "e1" => self::initialised,
            "e2" => self::transmited,
            "t" => self::Ttransmited
        ),
        array(
            "e1" => self::transmited,
            "e2" => self::accepted,
            "t" => self::Taccepted
        ),
        array(
            "e1" => self::transmited,
            "e2" => self::refused,
            "t" => self::Trefused
        ),
        array(
            "e1" => self::accepted,
            "e2" => self::realised,
            "t" => self::Trealised
        ),
        array(
            "e1" => self::transmited,
            "e2" => self::initialised,
            "t" => self::Tretry
        )
    );
    
    public $stateactivity = array(
        self::initialised => "zoo_adoption writting",
        self::accepted => "zoo_adoption accepted",
        self::transmited => "zoo_adoption verification"
    ); # _("zoo_adoption writting") # _("zoo_adoption accepted") _("zoo_adoption verification")

    /* @var _ANIMAL $nouvelAnimal */
    protected $nouvelAnimal = null;

    /**
     * verify that the validator has a mail
     *
     * @return string error if no validator or no mail
     */
    public function verifyValidatorMail()
    {
        $idval = $this->doc->getValue("DE_IDVAL");
        if (!$idval) {
            return sprintf(_("zoo:no validator defined"));
        }
        $to = $this->doc->getRValue("DE_IDVAL:US_MAIL");
        if (!$to) {
            return sprintf(_("zoo:no mail for validator"));
        }
        return "";
    }

    /**
     * @param string $newstate
     *
     * @return string
     */
    public function sendTransmitedMail($newstate)
    {
        $tkeys = array();
        if ($this->doc->getRValue("de_idespece:es_protegee") == "1") {
            // get others animals
            $otherAnimals = array();

            $s = new SearchDoc($this->dbaccess, "ZOO_ANIMAL");
            $s->addFilter("an_espece = '%d'", $this->doc->getValue("de_idespece"));
            $t = $s->search();

            foreach ($t as $animal) {
                $otherAnimals[] = $this->getDocAnchor($animal["initid"], "mail");
            }
            // add the key to be inserted in mail template
            $tkeys["animals"] = implode(", ", $otherAnimals);

            // choose the template for protected animals
            $choosenMailTemplateId = $this->getParamValue("WAD_MAILSECURE");
        } else {
            // choose the template for not protected animals
            $choosenMailTemplateId = $this->getParamValue("WAD_MAILCURRENT");
        }

        $choosenMailTemplate = new_doc($this->dbaccess, $choosenMailTemplateId);
        /* @var _MAILTEMPLATE $choosenMailTemplate */
        if ($choosenMailTemplate->isAlive()) {
            $err = $choosenMailTemplate->sendDocument($this->doc, $tkeys);
        } else {
            $err = _("no mail template");
        }
        return $err;
    }
    
    public function sendTransmitedMail_detail($newstate)
    {
        require_once ("FDL/mailcard.php");
        global $action;
        $cc = "";
        $to = $this->doc->GetRValue("DE_IDVAL:US_MAIL");
        if ($to == ""){
            return sprintf(_("no mail for validator"));
        }
        $subject = sprintf(_("adoption %s to validate"), $this->doc->title);
        sendCard($action, $this->doc->id, $to, $cc, $subject, "ZOO:DE_MAIL_TRANSMITED:S", true);
        return "";
    }

    public function sendRetryMail($newstate)
    {
        global $action;
        require_once ("FDL/mailcard.php");
        $to = $this->doc->GetRValue("DE_IDREDAC:US_MAIL");
        $cc = "";
        if ($to == "") {
            return sprintf(_("no mail for redactor"));
        }
        $subject = sprintf(_("adoption %s to modify"), $this->doc->title);
        sendCard($action, $this->doc->id, $to, $cc, $subject, "ZOO:DE_MAIL_RETRY:S", true);
        return "";
    }
    
    public function notifyReject()
    {
        $reason = $this->getValue("wad_refus");
        
        $this->doc->disableEditControl(); // no control here
        $err = $this->doc->setValue("de_motif", $reason);
        if($err == ''){
            $err = $this->doc->store();
        }
        $this->doc->enableEditControl();
        return $err;
    }
    
    public function sendRefusedMail($newstate)
    {
        $to = $this->doc->GetRValue("DE_IDDEMAND:US_MAIL");
        $subject = sprintf(_("adoption %s refused"), $this->doc->title);

        SetHttpVar("redirect_app", "FDL");
        SetHttpVar("redirect_act", "EDITMAIL&mail_to=$to&mzone=ZOO:DE_MAIL_REFUSED:S&mail_subject=$subject&mid=" . $this->doc->id);
        
        return "";
    }
    
    public function sendRealisedMail($newstate)
    {
        global $action;
        require_once ("FDL/mailcard.php");
        $to = $this->doc->GetRValue("DE_IDVAL:US_MAIL");
        $cc = "";
        if ($to == ""){
            return sprintf(_("no mail for validator"));
        }
        $subject = sprintf(_("adoption %s realised"), $this->doc->title);
        sendCard($action, $this->doc->id, $to, $cc, $subject, "ZOO:DE_MAIL_REALISED:S", true);
        return "";
    }
    
    public function verifyEnclosDispo()
    {
        // create the new animal even if user has no sufficient rights
        $this->nouvelAnimal = createDoc($this->dbaccess, "ZOO_ANIMAL", false);
        if ($this->nouvelAnimal) {
            $this->nouvelAnimal->setValue("an_espece", $this->doc->getValue("de_idespece"));
            // ensure there is available gate for this animal
            $err = $this->nouvelAnimal->verifyCapacity();
        } else {
            $err =  _("zoo:Cannot create animal");
        }
        return $err;
    }
    
    public function createAnimal()
    {
        $nouvelAnimal = $this->nouvelAnimal;
        if ($nouvelAnimal) {
            $nouvelAnimal->setValue("an_nom", $this->doc->getValue("de_nom"));
            $nouvelAnimal->setValue("an_espece", $this->doc->getValue("de_idespece"));
            $nouvelAnimal->setValue("an_naissance", $this->doc->getValue("de_naissance"));
            $nouvelAnimal->setValue("an_photo", $this->doc->getValue("de_photo"));
            $err = $nouvelAnimal->store();
            if ($err == "") {
                //once the animal is created, redirect the user to the animal
                SetHttpVar("redirect_app", "FDL");
                SetHttpVar("redirect_act", "OPENDOC&mode=view&id=" . $nouvelAnimal->id);
            }
            return $err;
        }
        return "";
    }
}
?>
