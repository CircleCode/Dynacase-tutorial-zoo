<?php

/**
 * @property _ZOO_ANIMAL doc
 */
class WFL_ZOO_ANIMAL extends WDoc
{
    var $attrPrefix = "WAN";

    //region States
    const alive = "zoo_wan_e1"; // /* _ alive */ N_("zoo_wan_e1")
    const dead = "zoo_wan_e2"; // /* _ dead */ N_("zoo_wan_e2")
    const transfered = "zoo_wan_e3"; // /* _ transfered */ N_("zoo_wan_e3")
    const sick = "zoo_wan_e4"; // /* _ sick */ N_("zoo_wan_e4")
    const convalescent = "zoo_wan_e5"; // /* _ convalescent */ N_("zoo_wan_e5")
    //endregion

    //region Transitions
    const T1 = "zoo_wan_t1"; // /* _ T1 */ N_("zoo_wan_t1")
    const Tsick = "zoo_wan_t2"; // /* _ Tsick */ N_("zoo_wan_t2")
    const Tconvalescent = "zoo_wan_t3"; // /* _ Tconvalescent */ N_("zoo_wan_t3")
    const T3 = "zoo_wan_t4"; // /* _ T3 */ N_("zoo_wan_t4")
    //endregion

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
