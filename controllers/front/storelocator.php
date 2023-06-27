<?php
/**
*   Fa Pickup
*
*  @author    Faddons
*  @copyright Faddons 2021
*  @license   Single domain
*/

class PickupStorelocatorModuleFrontController extends ModuleFrontController
{


    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->context->controller->addJquery();
        $this->addCSS(_PS_MODULE_DIR_ . 'pickup/views/css/storelocator.css');
        $this->addCSS(_PS_MODULE_DIR_ . 'pickup/views/css/style.css');
        $this->addJs(_PS_MODULE_DIR_ . 'pickup/views/js/handlebars.min.js');
        $this->addJs(_PS_MODULE_DIR_ . 'pickup/views/js/jquery.storelocator.js');
        $this->addJs(_PS_MODULE_DIR_ . 'pickup/views/js/storelocator.js');
    }



    public function initContent()
    {

        $setting        = $this->module->getSetting();
        $countries      = $this->module->getStoreCountries();
        $states         = $this->module->getStoreStates();
        $cities         = $this->module->getCities();
        $stores         = $this->module->getStores();

        $radius_values  = explode(',', $setting['radius_values']);

        foreach ($stores as &$store) {
            $store['url'] = Context::getContext()->link->getModuleLink(
                'pickup',
                'store',
                array('id' => $store['id'], 'slug' => $store['slug'])
            );
        }

        Media::addJsDef(array(
            'url' => Tools::getHttpHost(true).__PS_BASE_URI__,
            'states'        => $states,
            'cities'        => $cities,
            'stores'        => $stores,
            'distance_unit' => ($setting['distance_unit'] == 'kilometer')?'km':'m',
            'map_zoom_level'=> ($setting['map_zoom_level']) ? $setting['map_zoom_level'] : 12,
            'default_lat'   => $setting['default_lat'],
            'default_lng'   => $setting['default_lng'],
        ));

        $this->context->smarty->assign(
            array(
                'setting'       => $setting,
                'countries'     => $countries,
                'states'        => $states,
                'cities'        => $cities,
                'radius'        => $radius_values,
                'banner'        => (file_exists(_PS_IMG_DIR_ .'t/1.jpg'))?
                _PS_BASE_URL_.__PS_BASE_URI__ .'img/t/1.jpg':false,
                'static_token'  => Tools::getToken(false),
            )
        );

        parent::initContent();
        $this->setTemplate('module:pickup/views/templates/front/storelocator.tpl');
    }
}
