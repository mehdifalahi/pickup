<?php
/**
*   Fa Pickup
*
*  @author    Faddons
*  @copyright Faddons 2021
*  @license   Single domain
*/

class PickupAjaxModuleFrontController extends ModuleFrontController
{

    public function initContent()
    {
        parent::initContent();
        $this->ajax = true;
    }


    public function displayAjax()
    {
        $task = Tools::getValue('task');
        $setting = $this->module->getSetting();

        if ($this->isTokenValid()) {
            if ($task == 'getStores') {
                $cart = Context::getContext()->cart;
                $products = $cart->getProducts();
                $id_carrier = $this->module->getIdCarrier();
                $paymentOptions = new PaymentOptionsFinder();
                $carrier = new Carrier((int) $id_carrier);

                $id_country = (int)Tools::getValue('id_country');
                $id_state = (int)Tools::getValue('id_state');
                $city = Tools::getValue('city');
                $radius = (int)Tools::getValue('radius');
                $latitude = Tools::getValue('latitude');
                $longitude = Tools::getValue('longitude');

                if ($setting['radius_unit'] == 'kilometer') {
                    $radius = $radius * 1.609;
                }

                $sql = 'SELECT a.*, a.zipcode as postal, s.name as `state`, c.name as country ';
                if (($latitude or $longitude) and $radius) {
                    $sql .= ' , SQRT(
                    POW(69.1 * (a.lat - '. $latitude .'), 2) +
                    POW(69.1 * ('. $longitude .' - a.lng) * COS(a.lat / 57.3), 2)) AS distance ';
                }
                $sql .= 'FROM '. _DB_PREFIX_ .'pickup_store as a
                LEFT JOIN '. _DB_PREFIX_ .'state as s ON s.id_state = a.state_id
                LEFT JOIN '. _DB_PREFIX_ .'country_lang as c ON c.id_country = a.country_id
                WHERE a.active = 1 ';
                if ($id_country) {
                    $sql .= ' and a.country_id = "'. $id_country .'" ';
                }
                if ($id_state) {
                    $sql .= ' and a.state_id = "'. $id_state .'" ';
                }
                if ($city) {
                    $sql .= ' and a.city LIKE "'. $city .'" ';
                }
                if (($latitude or $longitude) and $radius) {
                    $sql .= ' HAVING distance <  ' . (int)$radius;
                }
                $sql .= ' ORDER BY a.ordering ASC';
                $items = Db::getInstance()->ExecuteS($sql);
                $html = '';

                $configuration = Configuration::getMultiple(array(
                    'PS_SHIPPING_FREE_PRICE',
                    'PS_SHIPPING_HandLING',
                    'PS_SHIPPING_METHOD',
                    'PS_SHIPPING_FREE_WEIGHT',
                ));

                foreach ($items as &$item) {
                    $item['shipping_cost'] = $carrier->getDeliveryPriceByWeight(
                        $cart->getTotalWeight($products),
                        (int) $item['zone_id']
                    );
                    if ($carrier->shipping_handling) {
                        $item['shipping_cost'] += (float) $configuration['PS_SHIPPING_HandLING'];
                    }
                    foreach ($products as $product) {
                        if (!$product['is_virtual']) {
                            $item['shipping_cost'] += $product['additional_shipping_cost'] * $product['cart_quantity'];
                        }
                    }

                    $item['shipping_cost'] = Tools::displayPrice($item['shipping_cost']);

                    $deliverytime_start = $item['deliverytime_start'];
                    $deliverytime_end = $item['deliverytime_end'];
                    $item['deliverydate_start'] = date('l d M', strtotime(date('Y-m-d H:i:s') . ' +'.$deliverytime_start.' day'));
                    $item['deliverydate_end'] = date('l d M', strtotime(date('Y-m-d H:i:s') . ' +'.$deliverytime_end.' day'));

                    $paymentmethods = array();
                    $pyms = $this->module->getStorePaymentMethods($item['id']);
                    if (count($pyms) > 0) {
                        foreach ($pyms as $pym) {
                            $paymentmethods[] = $paymentOptions->present()[$pym['paymentmethod']][0]['call_to_action_text'];
                        }
                    } else {
                        foreach ($paymentOptions->present() as $option) {
                            $paymentmethods[] = $option[0]['call_to_action_text'];
                        }
                    }
                    $item['paymentmethod'] = implode(' - ', $paymentmethods);


                    $timing = [];
                    if ($item['working_hours'] == 1) {
                        $timing = json_decode($item['everyday']);
                    } elseif ($item['working_hours'] == 2) {
                        $timing = json_decode($item['perdayofweek']);
                    }
                    $item['timing'] = $timing;


                    $this->context->smarty->assign(array('store' => $item));
                    $local_path = _PS_MODULE_DIR_.$this->module->name.'/'.$this->module->name.'.php';
                    $html .= $this->module->display($local_path, 'pickup_item.tpl');
                }

                echo json_encode(array(
                    'html' => $html
                ));
                die();
            }

            if ($task == 'setOrder') {
                $cart_id = (int)Tools::getValue('cart_id');
                $store_id = (int)Tools::getValue('store_id');

                $sql = 'SELECT id FROM '._DB_PREFIX_ .'pickup_order
                WHERE cart_id = "'. $cart_id  .'"';
                $id = Db::getInstance()->getValue($sql);

                if ($id) {
                    Db::getInstance()->execute('UPDATE '._DB_PREFIX_ .'pickup_order
                    SET store_id = "'.(int)$store_id.'" WHERE id = '. (int)$id);
                } else {
                    Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_ .'pickup_order
                    (cart_id, store_id) VALUES ("'.(int)$cart_id.'","'.(int)$store_id.'") ');
                }
            }
        } else {
            die('Token is not valid');
        }
    }
}
