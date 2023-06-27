<?php
/**
*   Fa Pickup
*
*  @author    Faddons
*  @copyright Faddons 2021
*  @license   Single domain
*/

class PickupZone extends ObjectModel
{
    public $id;
    public $zone_id;
    public $country_id;
    public $name;

    public static $definition = array(
        'table' => 'pickup_zone',
        'primary' => 'id',
        'fields' => array(
            'zone_id'           => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'country_id'        => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'name'              => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 32),
        )
    );


    public function add($autodate = true, $null_values = false)
    {

        $name = Tools::getValue("name");
        Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'zone` (`name`,`active`) VALUES ("'. $name .'", 1)');
        $this->zone_id = Db::getInstance()->Insert_ID();

        if (!parent::add($autodate, $null_values)) {
            return false;
        }
        return true;
    }


    public function update($null_values = false)
    {
        $name = Tools::getValue("name");
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'zone` SET `name`="'. $name .'"
		WHERE id_zone = '. (int)$this->zone_id);

        return parent::update($null_values);
    }


    public function delete()
    {

        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'zone` WHERE id_zone = '. (int)$this->zone_id);

        return parent::delete();
    }
}
