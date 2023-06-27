<?php
/**
*   Fa Pickup
*
*  @author    Faddons
*  @copyright Faddons 2021
*  @license   Single domain
*/

class PickupStoreModuleFrontController extends ModuleFrontController
{

    public $store;

    public function __construct()
    {
        parent::__construct();
        $id = Tools::getValue('id');
        $slug = Tools::getValue('slug');
        $this->store = $this->module->getStore($id);
        if ($this->module->setting['redirect_old_url'] and ($slug != $this->store['slug'])) {
            Tools::redirect(
                Context::getContext()->link->getModuleLink(
                    'pickup',
                    'store',
                    array('id' => $this->store['id'], 'slug' => $this->store['slug'])
                )
            );
        }
    }


    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->context->controller->addJquery();
        $this->addCSS(_PS_MODULE_DIR_ . 'pickup/views/css/fontawesome.min.css');
        $this->addCSS(_PS_MODULE_DIR_ . 'pickup/views/css/style.css');
        $this->addJs(_PS_MODULE_DIR_ . 'pickup/views/js/store.js');
    }

    public function init()
    {
        parent::init();
        if ($this->store['active'] == 0) {
            $this->redirect_after = '404';
            $this->redirect();
        }
    }

    public function initContent()
    {
        $setting = $this->module->setting;
        $store = $this->store;
        $url = Tools::getHttpHost(true).__PS_BASE_URI__;
        $image = _PS_STORE_IMG_DIR_ . $store['id'] . '.jpg';
        $image = (file_exists($image))?$url.'img/st/'.$store['id'] . '.jpg': false;

        $timing = [];
        if ($store['working_hours'] == 1) {
            $timing = json_decode($store['everyday']);
        } elseif ($store['working_hours'] == 2) {
            $timing = json_decode($store['perdayofweek']);
        }

        Media::addJsDef(array(
            'store' => $store,
            'url' => $url,
        ));

        $this->context->smarty->assign(
            array(
                'url'           => $url,
                'setting'       => $setting,
                'store'         => $store,
                'image'         => $image,
                'timing'        => $timing,
                'static_token'  => Tools::getToken(false),
            )
        );

        parent::initContent();
        $this->setTemplate('module:pickup/views/templates/front/store.tpl');
    }
}
