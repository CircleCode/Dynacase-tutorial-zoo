<?php
/**
 * Display colors table
 *
 * @author Anakeen 2008
 * @version $Id: zoo_color.php,v 1.2 2010-04-02 14:49:04 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package freedom-zoo
 * 
 *
 */


 /**
  * Display colors table
  * @global red Http var : 0-255 red intensity
  */   
function zoo_color(&$action) {
  $red=$action->getArgument("red",128);
  $quality=$action->getArgument("quality",20);

  if ($quality>255) $quality=255;
  if ($red>255) $red=255;

  for ($i=0;$i<$quality;$i++) {
    $green=$i*(255/$quality);
    $tcolor[]=array("cells"=>"green$i");
    $tcells=array();
    for ($j=0;$j<$quality;$j++) {
      $blue=$j*(255/$quality);
      $tcells[]=array("color"=>sprintf("#%02X%02X%02X",$red,$green,$blue));      
    }
    $action->lay->setBlockData("green$i",$tcells);
  }
  

  $action->lay->set("red",$red);
  $action->lay->set("quality",$quality);
  $action->lay->setBlockData("ROW",$tcolor);
}