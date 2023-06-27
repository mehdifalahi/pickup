<?php
/**
*   Fa Pickup
*
*  @author    Faddons
*  @copyright Faddons 2021
*  @license   Single domain
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once('classes/PickupStore.php');
require_once('classes/PickupZone.php');
require_once('classes/StoreSetting.php');


class Pickup extends CarrierModule
{


    const PREFIX = 'pickup_';
    public $shipping_cost = 0;
    public $setting;
    protected $this_file = __FILE__;
    protected $carriers = array(
        'pickup' => 'main',
    );


    public function __construct()
    {
        $this->name = 'pickup';
        $this->tab = 'administration';
        $this->version = '1.0.1';
        $this->module_key = 'e7cf605275ee904a108419bbf0346f39';

        $this->author = 'Faddons';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.7.0', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Pickup and Drop Off Station/Store');
        $this->description = $this->l('Display an alternative of shipping method or pickup to your customers');
        $this->module_dir = '../../../../modules/'.$this->name.'/';
    }


    // install module
    public function install()
    {
        if (parent::install()) {
            if (!$this->createCarriers()) {
                return false;
            }

            return $this->installDB()
            && $this->registerHook('header')
            && $this->registerHook('actionCarrierUpdate')
            && $this->registerHook('displayBeforeCarrier')
            && $this->registerHook('displayHeader')
            && $this->registerHook('actionValidateOrder')
            && $this->registerHook('displayOrderDetail')
            && $this->registerHook('displayAdminOrderContentShip')
            && $this->registerHook('displayInvoiceLegalFreeText')
            && $this->registerHook('displayPDFDeliverySlip')
            && $this->registerHook('moduleRoutes')
            && $this->installTab();
        }
        return false;
    }


    // unistall module
    public function uninstall()
    {

        if (!$this->deleteCarriers()) {
            return false;
        }

        $this->uninstallDB();

        return $this->uninstallTab()
        && parent::uninstall();
    }


    public function installTab()
    {

        $tab = new Tab();
        $tab->active = 1;
        $tab->name = array();
        $tab->class_name = 'AdminPickup';
        $tab->module = $this->name;
        $tab->id_parent = (int)Tab::getIdFromClassName('IMPROVE');
        $tab->icon='local_shipping';
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Pickup location';
        }
        $tab->add();

        $tab = new Tab();
        $tab->active = 1;
        $tab->name = array();
        $tab->class_name = 'AdminPStores';
        $tab->module = $this->name;
        $tab->id_parent = (int)Tab::getIdFromClassName('AdminPickup');
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Stores';
        }
        $tab->add();

        $tab = new Tab();
        $tab->active = 1;
        $tab->name = array();
        $tab->class_name = 'AdminPStoreSetting';
        $tab->module = $this->name;
        $tab->id_parent = (int)Tab::getIdFromClassName('AdminPickup');
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Store Setting';
        }
        $tab->add();

        $tab = new Tab();
        $tab->active = 1;
        $tab->name = array();
        $tab->class_name = 'AdminPZones';
        $tab->module = $this->name;
        $tab->id_parent = (int)Tab::getIdFromClassName('AdminPickup');
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Zones';
        }
        $tab->add();

        $tab = new Tab();
        $tab->active = 1;
        $tab->name = array();
        $tab->class_name = 'AdminPImport';
        $tab->module = $this->name;
        $tab->id_parent = (int)Tab::getIdFromClassName('AdminPickup');
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Import / Export';
        }
        $tab->add();

        return true;
    }


    public function uninstallTab()
    {
        $id_tabs   = array();
        $id_tabs[] = (int)Tab::getIdFromClassName('AdminPickup');
        $id_tabs[] = (int)Tab::getIdFromClassName('AdminPStores');
        $id_tabs[] = (int)Tab::getIdFromClassName('AdminPStoreSetting');
        $id_tabs[] = (int)Tab::getIdFromClassName('AdminPZones');
        $id_tabs[] = (int)Tab::getIdFromClassName('AdminPImport');
        foreach ($id_tabs as $id_tab) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }

        return true;
    }


    public function installDB()
    {

        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pickup_order` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `cart_id` int(11) NOT NULL,
        `order_id` int(11) NOT NULL,
        `store_id` int(11) NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `cart_id` (`cart_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;';
        Db::getInstance()->execute($sql);

        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pickup_store` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(200) CHARACTER SET utf8 NOT NULL,
        `slug` varchar(250) CHARACTER SET utf8 NOT NULL,
        `code` varchar(50) CHARACTER SET utf8 NOT NULL,
        `description` text CHARACTER SET utf8 NOT NULL,
        `zone_id` int(11) NOT NULL,
        `country_id` int(11) NOT NULL,
        `state_id` int(11) NOT NULL,
        `city` varchar(100) CHARACTER SET utf8 NOT NULL,
        `address` varchar(200) CHARACTER SET utf8 NOT NULL,
        `lat` decimal(11,8) NOT NULL,
        `lng` decimal(11,8) NOT NULL,
        `zipcode` varchar(20) CHARACTER SET utf8 NOT NULL,
        `phone` varchar(20) CHARACTER SET utf8 NOT NULL,
        `email` varchar(100) CHARACTER SET utf8 NOT NULL,
        `website` varchar(100) CHARACTER SET utf8 NOT NULL,
        `facebook` varchar(100) CHARACTER SET utf8 NOT NULL,
        `skype` varchar(100) CHARACTER SET utf8 NOT NULL,
        `instagram` varchar(100) CHARACTER SET utf8 NOT NULL,
        `whatsapp` varchar(100) CHARACTER SET utf8 NOT NULL,
        `twitter` varchar(200) NOT NULL,
        `landmark` varchar(250) NOT NULL,
        `working_hours` tinyint(1) NOT NULL,
        `everyday` varchar(50) CHARACTER SET utf8 NOT NULL,
        `perdayofweek` text CHARACTER SET utf8 NOT NULL,
        `deliverytime_start` int(11) NOT NULL DEFAULT 1,
        `deliverytime_end` int(11) NOT NULL DEFAULT 7,
        `meta_title` varchar(200) CHARACTER SET utf8 NOT NULL,
        `meta_description` text CHARACTER SET utf8 NOT NULL,
        `meta_keyword` text CHARACTER SET utf8 NOT NULL,
        `ordering` int(11) NOT NULL,
        `active` int(1) NOT NULL DEFAULT 1,
        `date_upd` datetime DEFAULT NULL,
        `date_add` datetime DEFAULT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;';
        Db::getInstance()->execute($sql);

        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pickup_storesetting` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `default_lat` decimal(11,8) NOT NULL,
        `default_lng` decimal(11,8) NOT NULL,
        `google_api` varchar(200) NOT NULL,
        `map_zoom_level` int(11) NOT NULL,
        `distance_unit` varchar(50) CHARACTER SET utf8 NOT NULL,
        `display_phone` tinyint(1) NOT NULL,
        `display_footerlink` tinyint(1) NOT NULL,
        `footerlink_text` varchar(200) CHARACTER SET utf8 NOT NULL,
        `display_headerlink` tinyint(1) NOT NULL,
        `headerlink_text` varchar(200) CHARACTER SET utf8 NOT NULL,
        `geo_location` int(11) NOT NULL,
        `enable_my_location` int(11) NOT NULL,
        `radius_values` varchar(200) NOT NULL,
        `radius_unit` varchar(50) NOT NULL,
        `default_radius` float NOT NULL,
        `storelocator_url` varchar(250) CHARACTER SET utf8 NOT NULL,
        `store_url` varchar(250) CHARACTER SET utf8 NOT NULL,
        `redirect_old_url` tinyint(1) NOT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;';
        Db::getInstance()->execute($sql);
        Db::getInstance()->execute('INSERT IGNORE INTO `'._DB_PREFIX_.'pickup_storesetting` SET `id` = 1');

        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pickup_store_paymentmethod` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `store_id` int(11) NOT NULL,
        `paymentmethod` varchar(100) CHARACTER SET utf8 NOT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;';
        Db::getInstance()->execute($sql);


        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pickup_zone` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `zone_id` int(11) NOT NULL,
        `country_id` int(11) NOT NULL,
        `name` varchar(200) CHARACTER SET utf8 NOT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;';
        Db::getInstance()->execute($sql);

        return true;
    }


    public function uninstallDB()
    {
        return true;
    }



    protected function createCarriers()
    {
        foreach ($this->carriers as $key => $value) {
            //Create new carrier
            $carrier = new Carrier();
            $carrier->name = $key;
            $carrier->active = true;
            $carrier->deleted = 0;
            $carrier->shipping_handling = false;
            $carrier->range_behavior = 0;
            $carrier->delay[Configuration::get('PS_LANG_DEFAULT')] = $key;
            $carrier->shipping_external = true;
            $carrier->is_module = true;
            $carrier->external_module_name = $this->name;
            $carrier->need_range = true;

            if ($carrier->add()) {
                $groups = Group::getGroups(true);
                foreach ($groups as $group) {
                    Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_ .'carrier_group
                    (id_carrier, id_group) VALUES ("'.(int)$carrier->id.'","'.(int)$group['id_group'].'") ');
                }

                $rangePrice = new RangePrice();
                $rangePrice->id_carrier = $carrier->id;
                $rangePrice->delimiter1 = '0';
                $rangePrice->delimiter2 = '1000000';
                $rangePrice->add();

                $rangeWeight = new RangeWeight();
                $rangeWeight->id_carrier = $carrier->id;
                $rangeWeight->delimiter1 = '0';
                $rangeWeight->delimiter2 = '1000000';
                $rangeWeight->add();

                $zones = Zone::getZones(true);
                foreach ($zones as $z) {
                    Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_ .'carrier_zone
                    (id_carrier, id_zone) VALUES ("'.(int)$carrier->id.'","'.(int)$z['id_zone'].'") ');

                    Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_ .'delivery
                    (id_carrier, id_range_price, id_range_weight, id_zone, price) VALUES
                    ("'.(int)$carrier->id.'","'.(int)$rangePrice->id.'", 0 ,"'.(int)$z['id_zone'].'", 0) ');

                    Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_ .'delivery
                    (id_carrier, id_range_price, id_range_weight, id_zone, price) VALUES
                    ("'.(int)$carrier->id.'", 0 , "'.(int) $rangeWeight->id.'" ,"'.(int)$z['id_zone'].'", 0) ');
                }
                Configuration::updateValue(self::PREFIX . $value, $carrier->id);
                Configuration::updateValue(self::PREFIX . $value . '_reference', $carrier->id);
            }
        }
        return true;
    }


    protected function deleteCarriers()
    {
        foreach ($this->carriers as $value) {
            $tmp_carrier_id = Configuration::get(self::PREFIX . $value);
            $carrier = new Carrier($tmp_carrier_id);
            $carrier->delete();
        }

        return true;
    }


    public function getOrderShippingCost($cart, $shipping_cost)
    {
        $cart_id = $cart->id;
        $store = $this->getStoreOrder($cart_id);

        if (is_array($store) && count($store)) {
            $configuration = Configuration::getMultiple(array(
                'PS_SHIPPING_FREE_PRICE',
                'PS_SHIPPING_HandLING',
                'PS_SHIPPING_METHOD',
                'PS_SHIPPING_FREE_WEIGHT',
            ));

            $id_carrier = $this->getIdCarrier();
            $carrier = new Carrier((int) $id_carrier);
            $products = $cart->getProducts();
            if ($store['zone_id']) {
                $shipping_cost = $carrier->getDeliveryPriceByWeight(
                    $cart->getTotalWeight($products),
                    (int) $store['zone_id']
                );
                if ($carrier->shipping_handling) {
                    $shipping_cost += (float) $configuration['PS_SHIPPING_HandLING'];
                }
            }

            foreach ($products as $product) {
                if (!$product['is_virtual']) {
                    $shipping_cost += $product['additional_shipping_cost'] * $product['cart_quantity'];
                }
            }
        }

        $this->shipping_cost = $shipping_cost;
        return $shipping_cost;
    }


    public function getOrderShippingCostExternal($params)
    {
        return $this->getOrderShippingCost($params, 0);
    }


    public function hookActionCarrierUpdate($params)
    {
        if ($params['carrier']->id_reference == Configuration::get(self::PREFIX . '_reference')) {
            Configuration::updateValue(self::PREFIX, $params['carrier']->id);
        }
    }


    public function hookDisplayBeforeCarrier($params)
    {
        $cart           = $params['cart'];
        $setting        = $this->getSetting();
        $countries      = $this->getStoreCountries();
        $states         = $this->getStoreStates();
        $cities         = $this->getCities();
        $stores         = $this->getStores();
        $radius_values  = explode(',', $setting['radius_values']);
        $storeOrder     = $this->getStoreOrder($cart->id);

        $products = $cart->getProducts();
        $id_carrier = $this->getIdCarrier();
        $carrier = new Carrier((int) $id_carrier);

        $configuration = Configuration::getMultiple(array(
            'PS_SHIPPING_FREE_PRICE',
            'PS_SHIPPING_HandLING',
            'PS_SHIPPING_METHOD',
            'PS_SHIPPING_FREE_WEIGHT',
        ));
        $paymentOptions = new PaymentOptionsFinder();

        foreach ($stores as &$store) {
            $store['shipping_cost'] = $carrier->getDeliveryPriceByWeight(
                $cart->getTotalWeight($products),
                (int) $store['zone_id']
            );
            if ($carrier->shipping_handling) {
                $store['shipping_cost'] += (float) $configuration['PS_SHIPPING_HandLING'];
            }
            foreach ($products as $product) {
                if (!$product['is_virtual']) {
                    $store['shipping_cost'] += $product['additional_shipping_cost'] * $product['cart_quantity'];
                }
            }

            $store['shipping_cost'] = Tools::displayPrice($store['shipping_cost']);

            $deliverytime_start = $store['deliverytime_start'];
            $deliverytime_end = $store['deliverytime_end'];
            $store['deliverydate_start'] = @date('l d M', strtotime(date() . ' +'.$deliverytime_start.' day'));
            $store['deliverydate_end'] = @date('l d M', strtotime(date() . ' +'.$deliverytime_end.' day'));

            $paymentmethods = array();
            $pyms = $this->getStorePaymentMethods($store['id']);
            if (count($pyms) > 0) {
                foreach ($pyms as $pym) {
                    $paymentmethods[] = $paymentOptions->present()[$pym['paymentmethod']][0]['call_to_action_text'];
                }
            } else {
                foreach ($paymentOptions->present() as $option) {
                    $paymentmethods[] = $option[0]['call_to_action_text'];
                }
            }
            $store['paymentmethod'] = implode(' - ', $paymentmethods);


            $timing = [];
            if ($store['working_hours'] == 1) {
                $timing = json_decode($store['everyday']);
            } elseif ($store['working_hours'] == 2) {
                $timing = json_decode($store['perdayofweek']);
            }
            $store['timing'] = $timing;
        }

        $result = array(
            'token'         => Tools::getToken(false),
            'setting'       => $setting,
            'countries'     => $countries,
            'states'        => $states,
            'cities'        => $cities,
            'radius'        => $radius_values,
            'stores'        => $stores,
            'shipping_cost' => Tools::displayPrice($this->shipping_cost),
            'cart_id'       => $cart->id,
            'setData'       => (is_array($storeOrder) && count($storeOrder))?1:0,
            'data'          => json_encode(array()),
        );

        if (is_array($storeOrder) && count($storeOrder)) {
            $storeItem = $this->getStore($storeOrder['id']);
            $deliverytime_start = $storeItem['deliverytime_start'];
            $deliverytime_end = $storeItem['deliverytime_end'];
            $deliverydate_start = @date('l d M', strtotime(date() . ' +'.$deliverytime_start.' day'));
            $deliverydate_end = @date('l d M', strtotime(date() . ' +'.$deliverytime_end.' day'));

            $result['data'] = json_encode(array(
                'store_id'      => $storeOrder['id'],
                'name'          => $storeOrder['name'],
                'shipping_cost' => Tools::displayPrice($this->shipping_cost),
                'address'       => $storeOrder['address'].' '.$storeOrder['city'] .' '.
                $storeOrder['state'] .' '. $storeOrder['postal'],
                'deliverydate_start' => $deliverydate_start,
                'deliverydate_end'   => $deliverydate_end
            ));
        }

        $this->context->smarty->assign($result);

        return $this->display($this->this_file, 'views/templates/hook/pickup.tpl');
    }



    public function hookDisplayHeader()
    {
        $add = false;
        $controllerId = Tools::getValue('controller');

        if ($controllerId === 'order') {
            $add = true;
        }

        if ($add) {
            $link = new Link;
            $ajax_link = $link->getModuleLink('pickup', 'ajax', array('ajax' => true));
            $setting = $this->getSetting();

            Media::addJsDef(array(
                'url' => $this->context->link->getPageLink('index', true),
                'pickup_carrier_id' => (int)$this->getIdCarrier(),
                'ajax_link_pickup' => $ajax_link,
                'google_api' => $setting['google_api'],
                'token' => Tools::getToken(false),
            ));
            $this->context->controller->addCSS($this->_path.'views/css/jquery.mCustomScrollbar.css');
            $this->context->controller->addCSS($this->_path.'views/css/magnific-popup.css');
            $this->context->controller->addCSS($this->_path.'views/css/pickup.css');
            $this->context->controller->addJs($this->_path.'views/js/jquery.magnific-popup.min.js');
            $this->context->controller->addJs($this->_path.'views/js/jquery.mCustomScrollbar.js');
            $this->context->controller->addJs($this->_path.'views/js/pickup.js');
        }
    }



    public function hookActionValidateOrder($params)
    {
        $cart = $params['cart'];
        $order = $params['order'];
        $id_cart = $cart->id;
        $id_order = $order->id;
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'pickup_order` SET order_id = "'. $id_order .'"
        WHERE cart_id = "'. $id_cart .'"');
    }


    public function hookDisplayAdminOrderContentShip($params)
    {
        $order = $params['order'];
        $order_id = $order->id;

        $carrier = new Carrier((int) $order->id_carrier);

        if ($carrier->external_module_name == 'pickup') {
            $data = $this->getStoreOrderByOrder($order_id);
            if (is_array($data) and count($data)) {
                $this->smarty->assign(array(
                    'data' => $data
                ));
                return $this->fetch('module:pickup/views/templates/hook/adminOrderContentShip.tpl');
            }
        }
    }


    public function hookDisplayOrderDetail($params)
    {
        $order = $params['order'];
        $order_id = $order->id;

        $carrier = new Carrier((int) $order->id_carrier);

        if ($carrier->external_module_name == 'pickup') {
            $data = $this->getStoreOrderByOrder($order_id);
            if (is_array($data) and count($data)) {
                $this->smarty->assign(array(
                    'data' => $data
                ));
                return $this->fetch('module:pickup/views/templates/hook/orderDetail.tpl');
            }
        }
    }


    public function hookDisplayInvoiceLegalFreeText($params)
    {
        $order = $params['order'];
        $order_id = $order->id;

        $carrier = new Carrier((int) $order->id_carrier);

        if ($carrier->external_module_name == 'pickup') {
            $data = $this->getStoreOrderByOrder($order_id);
            if (is_array($data) and count($data)) {
                $this->smarty->assign(array(
                    'data' => $data
                ));
                return $this->fetch('module:pickup/views/templates/hook/orderDetailPDF.tpl');
            }
        }
    }

    public function hookDisplayPDFDeliverySlip($params)
    {
        $object = $params['object'];
        $order_id = $object->id_order;
        $order = new Order((int)$order_id);

        $carrier = new Carrier((int) $order->id_carrier);

        if ($carrier->external_module_name == 'pickup') {
            $data = $this->getStoreOrderByOrder($order_id);
            if (is_array($data) and count($data)) {
                $this->smarty->assign(array(
                    'data' => $data
                ));
                return $this->fetch('module:pickup/views/templates/hook/orderDetailSlipPDF.tpl');
            }
        }
    }


    public function hookModuleRoutes($params)
    {

        $setting = $this->getSetting();
        $storelocator_url = ($setting['storelocator_url']) ? $setting['storelocator_url'] : 'storelocator.html';
        $store_url = ($setting['store_url'])?$setting['store_url']:'store';

        return [
            'module-pickup-storelocator' => [
                'controller' => 'storelocator',
                'rule' => $storelocator_url,
                'keywords' => [],
                'params' => [
                    'module' => 'pickup',
                    'fc' => 'module'
                ]
            ],
            'module-pickup-store' => [
                'controller' => 'store',
                'rule' => $store_url . '/{id}-{slug}',
                'keywords' => [
                    'id' => ['regexp' => '[0-9]+', 'param' => 'id'],
                    'slug' => ['regexp' => '[_a-zA-Z0-9\x{0600}-\x{06FF}\pL\pS-]*', 'param' => 'slug']
                ],
                'params' => [
                    'module' => 'pickup',
                    'fc' => 'module'
                ]
            ],
        ];
    }


    public function getIdCarrier()
    {
        foreach ($this->carriers as $value) {
            $id_reference = Configuration::get(self::PREFIX . $value);
        }
        $sql = 'SELECT id_carrier
        FROM '. _DB_PREFIX_ .'carrier
        WHERE id_reference  = ' . (int)$id_reference.' and `deleted` = 0';
        $sql .= ' ORDER BY id_carrier DESC';
        return Db::getInstance()->getValue($sql);
    }


    public function getZones()
    {
        $sql = 'SELECT a.*
        FROM '. _DB_PREFIX_ .'zone a
        WHERE a.`active` = 1
        ORDER BY a.id_zone ASC ';
        $results = Db::getInstance()->ExecuteS($sql);
        return $results;
    }


    public function getCountries()
    {
        $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');
        $sql = 'SELECT a.*
        FROM '. _DB_PREFIX_ .'country_lang a
        INNER JOIN '. _DB_PREFIX_ .'country c ON a.id_country = c.id_country
        WHERE a.id_lang = "'. $defaultLang .'" and c.`active` = 1
        ORDER BY a.id_country ASC ';
        $results = Db::getInstance()->ExecuteS($sql);
        return $results;
    }


    public function getStoreCountries()
    {
        $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');
        $sql = 'SELECT a.*
        FROM '. _DB_PREFIX_ .'country_lang a
        INNER JOIN '. _DB_PREFIX_ .'country c ON a.id_country = c.id_country
        INNER JOIN '. _DB_PREFIX_ .'pickup_store s ON a.id_country = s.country_id
        WHERE a.id_lang = "'. $defaultLang .'" and c.`active` = 1
        GROUP BY a.id_country
        ORDER BY a.id_country ASC';
        $results = Db::getInstance()->ExecuteS($sql);
        return $results;
    }


    public function getStoreStates()
    {
        $sql = 'SELECT a.*
        FROM '. _DB_PREFIX_ .'state as a
        INNER JOIN '. _DB_PREFIX_ .'pickup_store s ON a.id_state = s.state_id
        WHERE a.`active` = 1
        GROUP BY a.id_state
        ORDER BY a.id_state ASC ';
        $results = Db::getInstance()->ExecuteS($sql);
        return $results;
    }

    public function getAllStates()
    {
        $sql = 'SELECT * FROM '. _DB_PREFIX_ .'state WHERE `active` = 1
        ORDER BY id_state ASC ';
        $results = Db::getInstance()->ExecuteS($sql);
        return $results;
    }


    public function getCities()
    {
        $sql = 'SELECT state_id, city FROM '. _DB_PREFIX_ .'pickup_store
        GROUP BY city ';
        $results = Db::getInstance()->ExecuteS($sql);
        return $results;
    }


    public function getSetting()
    {
        $sql = 'SELECT * FROM '. _DB_PREFIX_ .'pickup_storesetting';
        $result = Db::getInstance()->getRow($sql);
        return $result;
    }


    public function getStores()
    {
        $sql = 'SELECT a.*, a.zipcode as postal, s.name as `state`, c.name as country
        FROM '. _DB_PREFIX_ .'pickup_store as a
        LEFT JOIN '. _DB_PREFIX_ .'state as s ON s.id_state = a.state_id
        LEFT JOIN '. _DB_PREFIX_ .'country_lang as c ON c.id_country = a.country_id
        WHERE a.active = 1
        GROUP BY a.id
        ORDER BY a.ordering,a.id DESC';
        $results = Db::getInstance()->ExecuteS($sql);
        return $results;
    }


    public function getStore($id)
    {
        $sql = 'SELECT a.*, a.zipcode as postal, s.name as `state`, c.name as country
        FROM '. _DB_PREFIX_ .'pickup_store as a
        LEFT JOIN '. _DB_PREFIX_ .'state as s ON s.id_state = a.state_id
        LEFT JOIN '. _DB_PREFIX_ .'country_lang as c ON c.id_country = a.country_id
        WHERE a.id = ' . (int)$id;
        $result = Db::getInstance()->getRow($sql);
        return $result;
    }


    public function getStoreOrder($cart_id)
    {
        $sql = 'SELECT s.*, s.zipcode as postal, st.name as `state`, c.name as country
        FROM '. _DB_PREFIX_ .'pickup_order as a
        INNER JOIN '. _DB_PREFIX_ .'pickup_store as s ON a.store_id = s.id
        LEFT JOIN '. _DB_PREFIX_ .'state as st ON st.id_state = s.state_id
        LEFT JOIN '. _DB_PREFIX_ .'country_lang as c ON c.id_country = s.country_id
        WHERE a.cart_id = ' . (int)$cart_id;
        $result = Db::getInstance()->getRow($sql);
        return $result;
    }


    public function getStoreOrderByOrder($order_id)
    {
        $sql = 'SELECT s.*, s.zipcode as postal, st.name as `state`, c.name as country
        FROM '. _DB_PREFIX_ .'pickup_order as a
        INNER JOIN '. _DB_PREFIX_ .'pickup_store as s ON a.store_id = s.id
        LEFT JOIN '. _DB_PREFIX_ .'state as st ON st.id_state = s.state_id
        LEFT JOIN '. _DB_PREFIX_ .'country_lang as c ON c.id_country = s.country_id
        WHERE a.order_id = ' . (int)$order_id;
        $result = Db::getInstance()->getRow($sql);
        return $result;
    }


    public function getStorePaymentMethods($store_id)
    {
        $sql = 'SELECT a.*
        FROM '. _DB_PREFIX_ .'pickup_store_paymentmethod as a
        WHERE a.store_id = ' . (int)$store_id;
        $results = Db::getInstance()->ExecuteS($sql);
        return $results;
    }
}
