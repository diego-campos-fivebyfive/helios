<?php
/**
 * Created by PhpStorm.
 * User: Fabio
 * Date: 20/06/2017
 * Time: 17:40
 */

namespace AppBundle\Util\KitGenerator;


interface ItemInterface
{

    public function setCellNumber($cellNumber);
    
    public function getCellNumber();
    
    public function setLength($length);
    
    public function getLength();
    
    public function setWidth($width);
    
    public function getWidth();
    
    public function setLines($lines);
    
    public function getLines();

    public function setQuantity($quantity);

    public function getQuantity();

    public function setPosition($position);

    public function getPosition();

    public function getMaxProfileSize();
    
    public function getMaxNumberPerLine();
}