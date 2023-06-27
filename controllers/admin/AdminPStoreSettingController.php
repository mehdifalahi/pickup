<?php
/**
*   Fa Pickup
*
*  @author    Faddons
*  @copyright Faddons 2021
*  @license   Single domain
*/

class AdminPStoreSettingController extends ModuleAdminController
{

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'pickup_storesetting';
        $this->className = 'StoreSetting';
        $this->identifier = 'id';
        $this->lang = false;
        $this->display = 'edit';
        parent::__construct();

        $this->toolbar_title = $this->module->l('Store Setting');

        $this->fieldImageSettings = array(
            'name' => 'banner',
            'dir' => 't',
        );
    }


    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->context->controller->addJquery();
        $this->addCSS(_PS_MODULE_DIR_ . 'pickup/views/css/admin.css');
        $this->addCSS(_PS_MODULE_DIR_ . 'pickup/views/css/fontawesome.min.css');
        $this->addCSS(_PS_MODULE_DIR_ . 'pickup/views/css/jquery.businessHours.css');
        $this->addCSS(_PS_MODULE_DIR_ . 'pickup/views/css/jquery.timepicker.min.css');
        $this->addJs(_PS_MODULE_DIR_ . 'pickup/views/js/jquery.timepicker.min.js');
        $this->addJs(_PS_MODULE_DIR_ . 'pickup/views/js/jquery.businessHours.js');
        $this->addJs(_PS_MODULE_DIR_ . 'pickup/views/js/admin.js');
    }


    public function getContent()
    {
        $output = null;
        if (Tools::isSubmit('submit'.$this->name)) {
            $output .= $this->displayConfirmation($this->l('Settings updated'));
        }
        return $output.$this->displayForm();
    }


    public function renderForm()
    {

        $this->object = new $this->className(1);

        $banner = _PS_IMG_DIR_ .'t/1.jpg';
        $banner_url = ImageManager::thumbnail(
            $banner,
            $this->table . '_1.' . $this->imageType,
            150,
            $this->imageType,
            true,
            true
        );
        $banner_size = file_exists($banner) ? filesize($banner) / 1000 : false;

        $this->fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->module->l('Setting'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Default latitude'),
                    'name' => 'default_lat',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Default longitude'),
                    'name' => 'default_lng',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Google map api key'),
                    'name' => 'google_api',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Map zoom level'),
                    'name' => 'map_zoom_level',
                    'required' => false,
                ),
                array(
                    'type' => 'select',
                    'label' => $this->module->l('Distance Unit'),
                    'name' => 'distance_unit',
                    'options' => array(
                        'id' => 'id',
                        'name' => 'name',
                        'query' => array(
                            array(
                                'id' => 'kilometer',
                                'name' => $this->module->l('Kilometers'),
                            ),
                            array(
                                'id' => 'mile',
                                'name' => $this->module->l('Miles'),
                            ),
                        ),
                    ),
                    'required' => false,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Display phone number'),
                    'name' => 'display_phone',
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->trans('Yes', array(), 'Admin.Global'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->trans('No', array(), 'Admin.Global'),
                        ),
                    )
                ),
                array(
                    'type' => 'file',
                    'label' => $this->module->l('Landing page image'),
                    'name' => 'banner',
                    'image' => $banner_url ? $banner_url : false,
                    'size' => $banner_size,
                    'display_image' => true,
                    'required' => false,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Enable "use my current location"'),
                    'name' => 'enable_my_location',
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->trans('Yes', array(), 'Admin.Global'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->trans('No', array(), 'Admin.Global'),
                        ),
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Predefined radius values'),
                    'name' => 'radius_values',
                    'required' => false,
                ),
                array(
                    'type' => 'select',
                    'label' => $this->module->l('Radius Unit'),
                    'name' => 'radius_unit',
                    'options' => array(
                        'id' => 'id',
                        'name' => 'name',
                        'query' => array(
                            array(
                                'id' => 'kilometer',
                                'name' => $this->module->l('Kilometers'),
                            ),
                            array(
                                'id' => 'mile',
                                'name' => $this->module->l('Miles'),
                            ),
                        ),
                    ),
                    'required' => false,
                ),
               array(
                    'type' => 'text',
                    'label' => $this->module->l('Default radius'),
                    'name' => 'default_radius',
                    'required' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Store Locator Page URL'),
                    'name' => 'storelocator_url',
                    'required' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Store Page URL'),
                    'name' => 'store_url',
                    'required' => false,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Redirect From Old Urls'),
                    'name' => 'redirect_old_url',
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->trans('Yes', array(), 'Admin.Global'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->trans('No', array(), 'Admin.Global'),
                        ),
                    )
                ),
            ),
            'submit' => array('title' => $this->module->l('Save'), 'name'=>'update')
        );

        $helper = new HelperForm($this);
        $this->setHelperDisplay($helper);
        $helper->fields_value = $this->getFieldsValue($this->object);
        $helper->tpl_vars = $this->tpl_form_vars;
        $helper->module = $this;
        $helper->override_folder = 'pstoresetting/';
        !is_null($this->base_tpl_form) ? $helper->base_tpl = $this->base_tpl_form : '';
        if ($this->tabAccess['view']) {
            if (Tools::getValue('back')) {
                $helper->tpl_vars['back'] = Tools::safeOutput(Tools::getValue('back'));
            } else {
                $helper->tpl_vars['back'] = Tools::safeOutput(
                    Tools::getValue(self::$currentIndex.'&token='.$this->token)
                );
            }
        }
        $form = $helper->generateForm($this->fields_form);
        return $form;
    }


    public function postProcess()
    {

        $return = parent::postProcess();
        return $return;
    }
}
