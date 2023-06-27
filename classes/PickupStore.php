<?php
/**
*   Fa Pickup
*
*  @author    Faddons
*  @copyright Faddons 2021
*  @license   Single domain
*/

class PickupStore extends ObjectModel
{
    public $id;
    public $name;
    public $slug;
    public $code;
    public $description;
    //public $image = 'default';
    public $zone_id;
    public $country_id;
    public $state_id;
    public $city;
    public $address;
    public $lat;
    public $lng;
    public $zipcode;
    public $phone;
    public $email;
    public $website;
    public $facebook;
    public $skype;
    public $instagram;
    public $whatsapp;
    public $twitter;
    public $landmark;
    public $working_hours;
    public $everyday;
    public $perdayofweek;
    public $deliverytime_start;
    public $deliverytime_end;
    public $meta_title;
    public $meta_description;
    public $meta_keyword;
    public $ordering;
    public $active;
    public $date_add;
    public $date_upd;
    public $paymentmethod;

    public static $definition = array(
        'table' => 'pickup_store',
        'primary' => 'id',
        'fields' => array(
            'name'              => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 32),
            'slug'              => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'code'              => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 32),
            //'image'           => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'description'       => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
            'zone_id'           => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'country_id'        => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'state_id'          => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'city'              => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'address'           => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'lat'               => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'lng'               => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'zipcode'           => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'phone'             => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'email'             => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'website'           => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'facebook'          => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'skype'             => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'instagram'         => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'whatsapp'          => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'twitter'           => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'landmark'          => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'working_hours'     => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'everyday'          => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'perdayofweek'      => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'deliverytime_start'=> array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'deliverytime_end'  => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'meta_title'        => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'meta_description'  => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'meta_keyword'      => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'ordering'          => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            //'published'       => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId'),
            'active'            => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add'          => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd'          => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        )
    );


    public function __construct($id = null, $idLang = null, $idShop = null)
    {
        parent::__construct($id, $idLang, $idShop);
        $this->image_dir = _PS_STORE_IMG_DIR_;
        $this->image = ($this->id && file_exists($this->image_dir . (int) $this->id . '.jpg')) ?
        (int) $this->id : false;

        $this->paymentmethod = array();
        if ($this->id) {
            $results = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_ .'pickup_store_paymentmethod
            WHERE store_id='.(int)$this->id);
            foreach ($results as $result) {
                $this->paymentmethod[] = $result['paymentmethod'];
            }
        }
    }


    public function add($autodate = true, $null_values = false)
    {

        $paymentmethod = Tools::getValue("paymentmethod");
        $everyday_start = Tools::getValue("everyday_start");
        $everyday_end = Tools::getValue("everyday_end");
        $this->everyday = json_encode(array($everyday_start, $everyday_end));
        $this->slug = $this->createSlug(Tools::getValue("slug"));

        if (!parent::add($autodate, $null_values)) {
            return false;
        }

        // paymentmethod
        $store_id = $this->id;
        Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_ .'pickup_store_paymentmethod
        WHERE store_id='.(int)$store_id);
        foreach ($paymentmethod as $payname) {
            Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_ .'pickup_store_paymentmethod
            (`store_id`,`paymentmethod`) VALUES ('.(int)$store_id.',"'.$payname.'")');
        }

        return true;
    }



    public function update($null_values = false)
    {
        $paymentmethod = Tools::getValue("paymentmethod");
        $everyday_start = Tools::getValue("everyday_start");
        $everyday_end = Tools::getValue("everyday_end");
        if (Tools::getValue("slug")) {
            $this->slug = $this->createSlug(Tools::getValue("slug"));
        }
        if ($everyday_start) {
            $this->everyday = json_encode(array($everyday_start, $everyday_end));
        }

        // paymentmethod
        $store_id = $this->id;
        Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_ .'pickup_store_paymentmethod
        WHERE store_id='.(int)$store_id);
        foreach ($paymentmethod as $payname) {
            Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_ .'pickup_store_paymentmethod
            (`store_id`,`paymentmethod`) VALUES ('.(int)$store_id.',"'.$payname.'")');
        }

        return parent::update($null_values);
    }



    public static function createSlug($str, $delimiter = '-')
    {
        $slug = Tools::strtolower(trim(
            preg_replace(
                '/[\s-]+/',
                $delimiter,
                preg_replace(
                    '/[^A-Za-z0-9-]+/',
                    $delimiter,
                    preg_replace(
                        '/[&]/',
                        'and',
                        preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str))
                    )
                )
            ),
            $delimiter
        ));
        return $slug;
    }
}
