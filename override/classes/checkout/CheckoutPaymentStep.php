<?php
/**
*   Fa Pickup
*
*  @author    Faddons
*  @copyright Faddons 2021
*  @license   Single domain
*/

use Symfony\Component\Translation\TranslatorInterface;

class CheckoutPaymentStep extends CheckoutPaymentStepCore
{

    private $selected_payment_option;


    public function render(array $extraParams = array())
    {
        $isFree = 0 == (float) $this->getCheckoutSession()->getCart()->getOrderTotal(true, Cart::BOTH);
        $paymentOptions = $this->paymentOptionsFinder->present($isFree);
        $conditionsToApprove = $this->conditionsToApproveFinder->getConditionsToApproveForTemplate();
        $deliveryOptions = $this->getCheckoutSession()->getDeliveryOptions();
        $deliveryOptionKey = $this->getCheckoutSession()->getSelectedDeliveryOption();

        if (isset($deliveryOptions[$deliveryOptionKey])) {
            $selectedDeliveryOption = $deliveryOptions[$deliveryOptionKey];
        } else {
            $selectedDeliveryOption = 0;
        }
        unset($selectedDeliveryOption['product_list']);


        // faddons
        $cart = $this->getCheckoutSession()->getCart();
        $carrier = new Carrier((int) $cart->id_carrier);
        $external_module_name = $carrier->external_module_name;
        if ($external_module_name == 'pickup') {
            $store = $this->getStoreOrder($cart->id);
            $pyms = $this->getStorePaymentMethods($store['id']);
            if (count($pyms) > 0) {
                $methods = array();
                foreach ($pyms as $pm) {
                    $methods[] = $pm['paymentmethod'];
                }
                foreach ($paymentOptions as $pname => $payment) {
                    if (!in_array($pname, $methods)) {
                        $payment;
                        unset($paymentOptions[$pname]);
                    }
                }
            }
        }

        $assignedVars = array(
            'is_free' => $isFree,
            'payment_options' => $paymentOptions,
            'conditions_to_approve' => $conditionsToApprove,
            'selected_payment_option' => $this->selected_payment_option,
            'selected_delivery_option' => $selectedDeliveryOption,
            'show_final_summary' => Configuration::get('PS_FINAL_SUMMARY_ENABLED'),
        );

        return $this->renderTemplate($this->getTemplate(), $extraParams, $assignedVars);
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

    public function getStorePaymentMethods($store_id)
    {
        $sql = 'SELECT a.*
        FROM '. _DB_PREFIX_ .'pickup_store_paymentmethod as a
        WHERE a.store_id = ' . (int)$store_id;
        $results = Db::getInstance()->ExecuteS($sql);
        return $results;
    }
}
