<?php
/**
*   Fa Pickup
*
*  @author    Faddons
*  @copyright Faddons 2021
*  @license   Single domain
*/

use PrestaShop\PrestaShop\Adapter\ServiceLocator;

class Carrier extends CarrierCore
{

    public static function getCarriers(
        $id_lang,
        $active = false,
        $delete = false,
        $id_zone = false,
        $ids_group = null,
        $modules_filters = self::PS_CARRIERS_ONLY
    ) {
        if ($ids_group && (!is_array($ids_group) || !count($ids_group))) {
            return array();
        }
        $sql = '
        SELECT c.*, cl.delay
        FROM `' . _DB_PREFIX_ . 'carrier` c
        LEFT JOIN `' . _DB_PREFIX_ . 'carrier_lang` cl ON
        (c.`id_carrier` = cl.`id_carrier` and
        cl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('cl') . ')
        LEFT JOIN `' . _DB_PREFIX_ . 'carrier_zone` cz ON (cz.`id_carrier` = c.`id_carrier`)' .
        ($id_zone ? 'LEFT JOIN `' . _DB_PREFIX_ . 'zone` z ON (z.`id_zone` = ' . (int) $id_zone . ')' : '') . '
        ' . Shop::addSqlAssociation('carrier', 'c') . '
        WHERE c.`deleted` = ' . ($delete ? '1' : '0');
        if ($active) {
            $sql .= ' and c.`active` = 1 ';
        }
        if ($id_zone) {
            $sql .= ' and ((c.external_module_name = "pickup") OR
            (cz.`id_zone` = ' . (int) $id_zone . ' and z.`active` = 1)) ';
        }
        if ($ids_group) {
            $sql .= ' and EXISTS (SELECT 1 FROM ' . _DB_PREFIX_ . 'carrier_group
                    WHERE ' . _DB_PREFIX_ . 'carrier_group.id_carrier = c.id_carrier
                    and id_group IN (' . implode(',', array_map('intval', $ids_group)) . ')) ';
        }
        switch ($modules_filters) {
            case 1:
                $sql .= ' and c.is_module = 0 ';
                break;
            case 2:
                $sql .= ' and c.is_module = 1 ';
                break;
            case 3:
                $sql .= ' and c.is_module = 1 and c.need_range = 1 ';
                break;
            case 4:
                $sql .= ' and (c.is_module = 0 OR c.need_range = 1) ';
                break;
        }
        $sql .= ' GROUP BY c.`id_carrier` ORDER BY c.`position` ASC';
        $cache_id = 'Carrier::getCarriers_' . md5($sql);
        if (!Cache::isStored($cache_id)) {
            $carriers = Db::getInstance()->executeS($sql);
            Cache::store($cache_id, $carriers);
        } else {
            $carriers = Cache::retrieve($cache_id);
        }
        foreach ($carriers as $key => $carrier) {
            if ($carrier['name'] == '0') {
                $carriers[$key]['name'] = Carrier::getCarrierNameFromShopName();
            }
        }
        return $carriers;
    }



    public static function getCarriersForOrder($id_zone, $groups = null, $cart = null, &$error = array())
    {
        $context = Context::getContext();
        $id_lang = $context->language->id;
        if (null === $cart) {
            $cart = $context->cart;
        }
        if (isset($context->currency)) {
            $id_currency = $context->currency->id;
        }
        if (is_array($groups) && !empty($groups)) {
            $result = Carrier::getCarriers(
                $id_lang,
                true,
                false,
                (int) $id_zone,
                $groups,
                self::PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE
            );
        } else {
            $result = Carrier::getCarriers(
                $id_lang,
                true,
                false,
                (int) $id_zone,
                array(Configuration::get('PS_UNIDENTIFIED_GROUP')),
                self::PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE
            );
        }
        $results_array = array();
        foreach ($result as $k => $row) {
            $carrier = new Carrier((int) $row['id_carrier']);
            $shipping_method = $carrier->getShippingMethod();
            if ($shipping_method != Carrier::SHIPPING_METHOD_FREE) {
                if ($carrier->external_module_name != "pickup") {
                    if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT &&
                    $carrier->getMaxDeliveryPriceByWeight($id_zone) === false)) {
                        $error[$carrier->id] = Carrier::SHIPPING_WEIGHT_EXCEPTION;
                        unset($result[$k]);
                        continue;
                    }
                    if (($shipping_method == Carrier::SHIPPING_METHOD_PRICE &&
                    $carrier->getMaxDeliveryPriceByPrice($id_zone) === false)) {
                        $error[$carrier->id] = Carrier::SHIPPING_PRICE_EXCEPTION;
                        unset($result[$k]);
                        continue;
                    }
                    if ($row['range_behavior']) {
                        if (!$id_zone) {
                            $id_zone = (int) Country::getIdZone(Country::getDefaultCountryId());
                        }
                        if ($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT
                            && (!Carrier::checkDeliveryPriceByWeight(
                                $row['id_carrier'],
                                $cart->getTotalWeight(),
                                $id_zone
                            )
                                )
                            ) {
                            $error[$carrier->id] = Carrier::SHIPPING_WEIGHT_EXCEPTION;
                            unset($result[$k]);
                            continue;
                        }
                        if ($shipping_method == Carrier::SHIPPING_METHOD_PRICE
                            && (!Carrier::checkDeliveryPriceByPrice(
                                $row['id_carrier'],
                                $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING),
                                $id_zone,
                                $id_currency
                            )
                            )
                            ) {
                            $error[$carrier->id] = Carrier::SHIPPING_PRICE_EXCEPTION;
                            unset($result[$k]);
                            continue;
                        }
                    }
                }
            }
            $row['name'] = ((string) ($row['name']) != '0' ? $row['name'] : Carrier::getCarrierNameFromShopName());
            $row['price'] = (($shipping_method == Carrier::SHIPPING_METHOD_FREE) ?
            0 : $cart->getPackageShippingCost((int) $row['id_carrier'], true, null, null, $id_zone));
            $row['price_tax_exc'] = (($shipping_method == Carrier::SHIPPING_METHOD_FREE) ?
            0 : $cart->getPackageShippingCost((int) $row['id_carrier'], false, null, null, $id_zone));
            $row['img'] = file_exists(_PS_SHIP_IMG_DIR_ . (int) $row['id_carrier'] . '.jpg') ?
            _THEME_SHIP_DIR_ . (int) $row['id_carrier'] . '.jpg' : '';
            if ($carrier->external_module_name != "pickup") {
                if ($row['price'] === false) {
                    unset($result[$k]);
                    continue;
                }
            }
            $results_array[] = $row;
        }
        $prices = array();
        if (Configuration::get('PS_CARRIER_DEFAULT_SORT') == Carrier::SORT_BY_PRICE) {
            foreach ($results_array as $r) {
                $prices[] = $r['price'];
            }
            if (Configuration::get('PS_CARRIER_DEFAULT_ORDER') == Carrier::SORT_BY_ASC) {
                array_multisort($prices, SORT_ASC, SORT_NUMERIC, $results_array);
            } else {
                array_multisort($prices, SORT_DESC, SORT_NUMERIC, $results_array);
            }
        }
        return $results_array;
    }
}
