<?php

/**
 * Ticket comportment
 *
 * @author Anakeen 2010
 * @version $Id: Method.Entree.php,v 1.4 2010-04-30 13:44:07 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package dynacase-zoo
 */

/**
 * @begin-method-ignore
 *
 * @property Layout lay
 * this part will be deleted when construct document class until end-method-ignore
 */
Class _ENTREE extends Doc {
    /*
     * @end-method-ignore
     */

    /**
     * view tickets one by personn
     * @param string $target
     * @param bool $ulink
     * @param bool $abstract
     */
    function viewtickets($target="_self",$ulink=true,$abstract=false) {

      $nb_adulte=intval($this->getValue("ent_adulte"));
      $nb_enfant=intval($this->getValue("ent_enfant"));
      $t=array();
      for ($i=0;$i<$nb_adulte;$i++) {
        $t[]=array("type"=>_("Adult") ,
               "isAdult"=>true);
      }
      for ($i=0;$i<$nb_enfant;$i++) {
        $t[]=array("type"=>_("Child")  ,
               "isAdult"=>false );
      }

      $this->lay->set("today",$this->getValue("ent_date",$this->getDate()));

      $this->lay->set("n",$nb_adulte+$nb_enfant);
      $this->lay->setBlockData("TICKET",$t);

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