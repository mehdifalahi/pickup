<?php
/**
*   Fa Pickup
*
*  @author    Faddons
*  @copyright Faddons 2021
*  @license   Single domain
*/

class AdminPStoresController extends ModuleAdminController
{

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'pickup_store';
        $this->className = 'PickupStore';
        $this->identifier = 'id';
        $this->lang = false;

        parent::__construct();

        $this->addRowAction('new');
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->allow_export = true;
        $this->_select = 'a.*, c.name as country, s.name as `state`';
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'country_lang` c ON (a.country_id = c.id_country)';
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'state` s ON (a.state_id = s.id_state)';

        $this->fieldImageSettings = array(
            'name' => 'image',
            'dir' => 'st',
        );

        $this->fields_list = array(
            'id' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'name' => array(
                'title' => $this->module->l('Name'),
                'align' => 'text-center',
                'class' => 'fixed-width',
            ),
            'code' => array(
                'title' => $this->module->l('Code'),
                'align' => 'text-center',
                'class' => 'fixed-width',
            ),
            'country' => array(
                'title' => $this->module->l('Country'),
                'align' => 'text-center',
                'class' => 'fixed-width',
                'filter_key' => 'c!name',
            ),
            'state' => array(
                'title' => $this->module->l('State'),
                'align' => 'text-center',
                'class' => 'fixed-width',
                'filter_key' => 's!name',
            ),
            'city' => array(
                'title' => $this->module->l('City'),
                'align' => 'text-center',
                'class' => 'fixed-width',
            ),
            'address' => array(
                'title' => $this->module->l('Address'),
                'align' => 'text-center',
                'class' => 'fixed-width',
            ),
            'zipcode' => array(
                'title' => $this->module->l('Zipcode'),
                'align' => 'text-center',
                'class' => 'fixed-width',
            ),
            'ordering' => array(
                'title' => $this->module->l('Ordering'),
                'align' => 'text-center',
                'class' => 'fixed-width',
            ),
            'date_upd' => array(
                'title' => $this->module->l('Modified'),
                'align' => 'text-center',
                'class' => 'fixed-width',
                'type' => 'datetime',
                'filter_key' => 'a!date_add'
            ),
            'date_add' => array(
                'title' => $this->module->l('Created'),
                'align' => 'text-center',
                'class' => 'fixed-width',
                'type' => 'datetime',
                'filter_key' => 'a!date_add'
            ),
            'active' => array(
                'title' => $this->trans('Enabled', array(), 'Admin.Global'),
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
                'class' => 'fixed-width-sm',
            ),
        );
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', array(), 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash',
            ),
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




    public function renderForm()
    {

        if (!($store = $this->loadObject(true))) {
            return;
        }

        $setting = $this->module->getSetting();

        $image = _PS_STORE_IMG_DIR_ . $store->id . '.jpg';
        $image_url = ImageManager::thumbnail(
            $image,
            $this->table . '_' . (int) $store->id . '.' . $this->imageType,
            70,
            $this->imageType,
            true,
            true
        );
        $image_size = file_exists($image) ? filesize($image) / 1000 : false;

        Media::addJsDef(array(
            'token_static'  => Tools::getAdminTokenLite('AdminPStores'),
            'state_id'      => (int)$store->state_id,
            'perdayofweek'  => ($store->perdayofweek)?$store->perdayofweek:false
        ));
        $this->context->smarty->assign(
            array(
                'setting' => $setting,
            )
        );

        $this->fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->module->l('Store'),
            ),
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->trans('Enable', array(), 'Admin.Global'),
                    'name' => 'active',
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->trans('Enabled', array(), 'Admin.Global'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->trans('Disabled', array(), 'Admin.Global'),
                        ),
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Name'),
                    'name' => 'name',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Friendly URL'),
                    'name' => 'slug',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Code'),
                    'name' => 'code',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Sort Order'),
                    'name' => 'ordering',
                    'required' => false,
                ),
                array(
                    'type' => 'file',
                    'label' => $this->module->l('Image'),
                    'name' => 'image',
                    'image' => $image_url ? $image_url : false,
                    'size' => $image_size,
                    'display_image' => true,
                    'required' => false,
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->module->l('Description'),
                    'name' => 'description',
                    'required' => false,
                    'autoload_rte' =>true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Delivery time start'),
                    'name' => 'deliverytime_start',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Delivery time end'),
                    'name' => 'deliverytime_end',
                    'required' => true,
                ),
            ),
            'submit' => array('title' => $this->module->l('Save'), 'name'=>'add'),
            'buttons' => array(
                'save-and-stay' => array(
                    'title' => $this->module->l('Save and Stay'),
                    'name' => 'submitAddpickup_storeAndStay_btn',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save',
                ),
            ),
        );



        $this->fields_form[1]['form'] = array(
            'legend' => array(
                'title' => $this->module->l('Address'),
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->module->l('Zone'),
                    'name' => 'zone_id',
                    'options' => array(
                        'id' => 'id_zone',
                        'name' => 'name',
                        'query' => $this->module->getZones(),
                    ),
                    'required' => true,
                    'class' => 'chosen fixed-width-xxl'
                ),
                array(
                    'type' => 'select',
                    'label' => $this->module->l('Country'),
                    'name' => 'country_id',
                    'options' => array(
                        'id' => 'id_country',
                        'name' => 'name',
                        'query' => $this->module->getCountries(),
                    ),
                    'required' => true,
                    'class' => 'chosen fixed-width-xxl'
                ),
                array(
                    'type' => 'select',
                    'label' => $this->module->l('State'),
                    'name' => 'state_id',
                    'options' => array(
                        'id' => 'id_state',
                        'name' => 'name',
                        'query' => [],
                    ),
                    'required' => true,
                    'class' => 'chosen fixed-width-xxl'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('City'),
                    'name' => 'city',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Address'),
                    'name' => 'address',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Zip Code'),
                    'name' => 'zipcode',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Phone'),
                    'name' => 'phone',
                    'required' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Email'),
                    'name' => 'email',
                    'required' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Website'),
                    'name' => 'website',
                    'required' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Facebook'),
                    'name' => 'facebook',
                    'required' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Skype'),
                    'name' => 'skype',
                    'required' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Instagram'),
                    'name' => 'instagram',
                    'required' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('WhatsApp'),
                    'name' => 'whatsapp',
                    'required' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Twitter'),
                    'name' => 'twitter',
                    'required' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Landmark'),
                    'name' => 'landmark',
                    'required' => false,
                ),
                array(
                    'type' => 'html',
                    'label' => $this->module->l('Set location By address'),
                    'name' => 'setlocation',
                    'html_content' => '<a id="current_loc_address" 
                    onclick="setLocationAddress()" class="btn btn-default">'
                    .$this->module->l('Set location').'</a>',
                ),
                array(
                    'type' => 'map',
                    'label' => $this->module->l('Map'),
                    'name' => 'map',
                    'required' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Ltitude'),
                    'name' => 'lat',
                    'required' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Longitude'),
                    'name' => 'lng',
                    'required' => false,
                ),
            ),
            'submit' => array('title' => $this->module->l('Save'), 'name'=>'add'),
            'buttons' => array(
                'save-and-stay' => array(
                    'title' => $this->module->l('Save and Stay'),
                    'name' => 'submitAddpickup_storeAndStay_btn',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save',
                ),
            ),
        );


        $this->fields_form[2]['form'] = array(
            'legend' => array(
                'title' => $this->module->l('Working hours'),
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->module->l('Working Hours'),
                    'name' => 'working_hours',
                    'options' => array(
                        'id' => 'id',
                        'name' => 'name',
                        'query' => array(
                            array(
                                'id' => 1,
                                'name' => $this->module->l('Everyday'),
                            ),
                            array(
                                'id' => 2,
                                'name' => $this->module->l('Per Day Of Week'),
                            ),
                        ),
                    ),
                    'required' => false,
                ),
                array(
                    'type' => 'everyday',
                    'label' => $this->module->l('EveryDay'),
                    'name' => 'everyday',
                    'required' => false,
                ),
                array(
                    'type' => 'perdayofweek',
                    'label' => $this->module->l('Days'),
                    'name' => 'perdayofweek',
                    'required' => false,
                ),
            ),
            'submit' => array('title' => $this->module->l('Save'), 'name'=>'add'),
            'buttons' => array(
                'save-and-stay' => array(
                    'title' => $this->module->l('Save and Stay'),
                    'name' => 'submitAddpickup_storeAndStay_btn',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save',
                ),
            ),
        );


        $this->fields_form[3]['form'] = array(
            'legend' => array(
                'title' => $this->module->l('Page Setting'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Meta Title'),
                    'name' => 'meta_title',
                    'required' => false,
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->module->l('Meta Description'),
                    'name' => 'meta_description',
                    'required' => false,
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->module->l('Meta Keyword'),
                    'name' => 'meta_keyword',
                    'required' => false,
                ),
            ),
            'submit' => array('title' => $this->module->l('Save'), 'name'=>'add'),
            'buttons' => array(
                'save-and-stay' => array(
                    'title' => $this->module->l('Save and Stay'),
                    'name' => 'submitAddpickup_storeAndStay_btn',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save',
                ),
            ),
        );

        $this->fields_form[4]['form'] = array(
            'legend' => array(
                'title' => $this->module->l('Payment'),
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->module->l('Payment Method'),
                    'name' => 'paymentmethod[]',
                    'options' => array(
                        'id' => 'name',
                        'name' => 'name',
                        'query' => Module::getPaymentModules(),
                    ),
                    'required' => false,
                    'multiple' => true,
                    'class' => 'chosen fixed-width-xxl'
                ),
            ),
            'submit' => array('title' => $this->module->l('Save'), 'name'=>'add'),
            'buttons' => array(
                'save-and-stay' => array(
                    'title' => $this->module->l('Save and Stay'),
                    'name' => 'submitAddpickup_storeAndStay_btn',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save',
                ),
            ),
        );

        $this->object->everyday = ($this->object->everyday)?json_decode($this->object->everyday):false;
        $this->fields_value['paymentmethod[]'] = $this->object->paymentmethod;

        $helper = new HelperForm($this);
        $this->setHelperDisplay($helper);
        $helper->fields_value = $this->getFieldsValue($this->object);
        $helper->tpl_vars = $this->tpl_form_vars;
        $helper->module = $this;
        $helper->override_folder = 'pstores/';
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


    public function ajaxProcessGetStates()
    {
        $country_id = (int)Tools::getValue('country_id');
        $sql = 'SELECT * FROM '. _DB_PREFIX_ .'state WHERE id_country = "'.$country_id.'" AND `active` = 1
        ORDER BY id_state ASC ';
        $results = Db::getInstance()->ExecuteS($sql);
        echo json_encode($results);
        exit;
    }
}
