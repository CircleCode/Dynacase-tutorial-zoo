<?php

/**
 * @property _ZOO_ANIMAL doc
 */
class WFL_ZOO_ANIMAL extends WDoc
{
    var $attrPrefix = "WAN";

    const alive = "e1"; # N_("alive")
    const dead = "dead"; # N_("dead")
    const transfered = "transfered"; # N_("transfered")
    const sick = "sick"; # N_("sick")
    const convalescent = "convalescent"; # N_("convalescent")

    const T1 = "T1"; # N_("T1")
    const Tsick = "Tsick"; # N_("Tsick")
    const Tconvalescent = "Tconvalescent"; # N_("Tconvalescent")
    const T3 = "T3"; # N_("T3")

    var $firstState = self::alive;

    var $transitions = array(
        self::T1           => array(),
        self::Tsick        => array(
            "m1" => "SendMailToVeto",
            "ask"=> array("wan_veto"),
            "nr" => true
        ),
        self::Tconvalescent=> array(
            "m1"=> "toHealthCard"
        ),
        self::T3           => array(
            "m1"=> "A2"
        )
    );

    var $cycle = array(
        array(
            "e1"=> self::alive,
            "e2"=> self::sick,
            "t" => self::Tsick
        ),
        array(
            "e1"=> self::alive,
            "e2"=> self::transfered,
            "t" => self::T1
        ),
        array(
            "e1"=> self::convalescent,
            "e2"=> self::dead,
            "t" => self::T1
        ),
        array(
            "e1"=> self::sick,
            "e2"=> self::convalescent,
            "t" => self::Tconvalescent
        ),
        array(
            "e1"=> self::convalescent,
            "e2"=> self::alive,
            "t" => self::T1
        ),
        array(
            "e1"=> self::sick,
            "e2"=> self::dead,
            "t" => "T3"
        )
    );


    function SendMailToVeto($newstate)
    {
        $err = '';

        $subject = sprintf(_("Disease of %s "), $this->doc->title);
        $zone = "ZOO:DE_MAIL_SICK:S";
        $to = $this->GetRValue("WAN_IDVETO:US_MAIL");
        $from = "";

        if (!$this->doc->getHealthCardId()){
            $err = $this->doc->createHealthCard();
        }

        if ($err != ""){
            return $err;
        }

        SetHttpVar("redirect_app", "FDL");
        SetHttpVar("redirect_act", "CONFIRMMAIL&state=$newstate&ulink=Y&mzone=$zone&mail_from=$from&mail_to=$to&mail_format=html&mail_subject=$subject&mid=" . $this->doc->id);

        return "->";
    }

    function toHealthCard()
    {
        $idcarnet = $this->doc->getHealthCardId();

        $err = $this->doc->canEdit();
        if ($err == "") {
            SetHttpVar("redirect_app", "GENERIC");
            SetHttpVar("redirect_act", "GENERIC_EDIT&id=" . $idcarnet);
        } else {
            SetHttpVar("redirect_app", "FDL");
            SetHttpVar("redirect_act", "FDL_CARD&id=" . $idcarnet);
        }
        return "";
    }

    function A2($newstate)
    {
        SetHttpVar("redirect_app", "TICKET");
        SetHttpVar("redirect_act", "DMEANDIED&state=$newstate&id=" . $this->doc->id);
        return "->";
    }

}

?>
