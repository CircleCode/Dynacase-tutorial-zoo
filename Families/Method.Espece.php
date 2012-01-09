<?php
/**
 * Species comportment
 *
 * @author Anakeen 2010
 * @version $Id: Method.Espece.php,v 1.1 2010-01-15 15:16:38 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package freedom-zoo
 */
/**
 */

/**
 * @begin-method-ignore
 * this part will be deleted when construct document class until end-method-ignore
 */
Class _ESPECE extends Doc
{
    /*
      * @end-method-ignore
      */

    /**
     * special edit view
     * @return void
     */
    function editcontinent()
    {
        $this->editAttr();
    }

    public function verifyOrder($order, $classeId)
    {
        ;
        $err = "";
        $sug = null;
        $orders = array(
            "Mammalia"       => array("Rongeur", "Artiodactyles", "Cétacés"),
            "Aves"           => array("Falconiforme", "Procellariiforme", "Struthioniforme"),
            "Lissamphibia"   => array("Urodèles", "Batracines", "Anoures"),
            "Reptilia"       => array("Crocodiliens", "Saurophidiens", "Squamates"),
            "Actinopterygii" => array("Hypotrème", "Anguilliformes", "Perciforme")
        );

        $classe = new_Doc($this->dbaccess, $classeId, true);
        if ($classe->isAlive()) {
            $latinClass = $classe->getValue('cl_nomscientifique');
            if (!in_array($order, $orders[$latinClass])) {
                $err = sprintf(_("zoo:%s is not a valid order for classe %s"), $order, $classe->getTitle());
                $sug = $orders[$latinClass];
            }
        } else {
            $err = _("zoo:unknown class");
        }

        return array(
            "err" => $err,
            "sug" => $sug
        );
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