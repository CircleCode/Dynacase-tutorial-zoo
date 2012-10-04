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
            "m1" => "",
            "m2" => ""
        ),
        self::Taccepted => array(
            "m1" => "",
            "m2" => ""
        ),
        self::Trefused => array(
            "m1" => "",
            "m2" => "",
            "nr" => true
        ),
        self::Trealised => array(
            "m1" => "",
            "m2" => ""
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
}
?>
