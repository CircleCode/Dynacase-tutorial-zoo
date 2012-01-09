<?php

/**
 * @property _ZOO_DEMANDEADOPTION doc
 */
class WFL_ZOO_DEMANDEADOPTION extends WDoc
{
    /* Required: used as a db prefix for generated attributes */
    public $attrPrefix = "WAD";

    //region States
    const initialised = "zoo_wad_e1"; // /* _ initialised */ _("zoo_wad_e1")
    const transmited = "zoo_wad_e2"; // /* _ transmited */ _("zoo_wad_e2")
    const accepted = "zoo_wad_e3"; // /* _ accepted */ _("zoo_wad_e3")
    const refused = "zoo_wad_e4"; // /* _ refused */ _("zoo_wad_e4")
    const realised = "zoo_wad_e5"; // /* _ realised */ _("zoo_wad_e5")
    //endregion

    //region Transitions
    const Ttransmited = "zoo_wad_t1"; // /* _ Ttransmited */ _("zoo_wad_t1")
    const Taccepted = "zoo_wad_t2"; // /* _ Taccepted */ _("zoo_wad_t2")
    const Trefused = "zoo_wad_t3"; // /* _ Trefused */ _("zoo_wad_t3")
    const Tretry = "zoo_wad_t4"; // /* _ Tretry */ _("zoo_wad_t4")
    const Trealised = "zoo_wad_t5"; // /* _ Trealised */ _("zoo_wad_t5")
    //endregion

    //region Activities
    public $stateactivity = array(
        self::initialised => "zoo_adoption writting", // _("zoo_adoption writting")
        self::accepted    => "zoo_adoption accepted", // _("zoo_adoption accepted")
        self::transmited  => "zoo_adoption verification" // _("zoo_adoption verification")
    );
    //endregion

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

    /* @var _ZOO_ANIMAL $nouvelAnimal */
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
