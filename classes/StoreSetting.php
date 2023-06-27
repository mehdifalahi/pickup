<?php
/**
*   Fa Pickup
*
*  @author    Faddons
*  @copyright Faddons 2021
*  @license   Single domain
*/

class StoreSetting extends ObjectModel
{
    public $id = 1;
    public $default_lat;
    public $default_lng;
    public $google_api;
    public $map_zoom_level;
    public $distance_unit;
    public $display_phone;
    public $display_footerlink;
    public $footerlink_text;
    public $display_headerlink;
    public $headerlink_text;
    public $geo_location;
    public $enable_my_location;
    public $radius_values;
    public $radius_unit;
    public $default_radius;
    public $storelocator_url;
    public $store_url;
    public $redirect_old_url;


    public static $definition = array(
        'table' => 'pickup_storesetting',
        'primary' => 'id',
        'fields' => array(
            'default_lat'       => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'default_lng'       => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'google_api'        => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'map_zoom_level'    => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'distance_unit'     => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'display_phone'     => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'display_footerlink'=> array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'footerlink_text'   => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'display_headerlink'=> array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'headerlink_text'   => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'geo_location'      => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'enable_my_location'=> array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'radius_values'     => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'radius_unit'       => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'default_radius'    => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'storelocator_url'  => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'store_url'         => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'redirect_old_url'  => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
        )
    );
    
    
    public function __construct($id = null, $idLang = null, $idShop = null)
    {
        $this->id = 1;
        parent::__construct($id, $idLang, $idShop);
        $this->banner_dir = _PS_IMG_DIR_.'t/';
        $this->banner = (file_exists($this->banner_dir . '1.jpg')) ? $this->id : false;
    }
}
