<?php
/**
*   Fa Pickup
*
*  @author    Faddons
*  @copyright Faddons 2021
*  @license   Single domain
*/

class AdminPZonesController extends ModuleAdminController
{

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'pickup_zone';
        $this->className = 'PickupZone';
        $this->identifier = 'id';
        $this->lang = false;

        parent::__construct();

        $this->addRowAction('new');
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->allow_export = true;
        $this->_select = 'a.*, c.name as country';
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'country_lang` c ON (a.country_id = c.id_country)';


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
            'country' => array(
                'title' => $this->module->l('Country'),
                'align' => 'text-center',
                'class' => 'fixed-width',
                'filter_key' => 'c!name',
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



    public function renderForm()
    {

        $this->fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->module->l('Zone registration'),
            ),
            'input' => array(
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
                    'type' => 'text',
                    'label' => $this->module->l('Name'),
                    'name' => 'name',
                    'required' => true,
                ),
            ),
            'submit' => array('title' => $this->module->l('Save'), 'name'=>'add')
        );


        $helper = new HelperForm($this);
        $this->setHelperDisplay($helper);
        $helper->fields_value = $this->getFieldsValue($this->object);
        $helper->tpl_vars = $this->tpl_form_vars;
        $helper->module = $this;
        //$helper->override_folder = 'pstores/';
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
