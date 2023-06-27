<?php
/**
*   Fa Pickup
*
*  @author    Faddons
*  @copyright Faddons 2021
*  @license   Single domain
*/

class AdminPImportController extends ModuleAdminController
{

    public function __construct()
    {
        $this->bootstrap = true;
        $this->lang = false;
        $this->display = 'edit';
        parent::__construct();
        $this->toolbar_title = $this->module->l('Import / Export');
    }


    public function initContent()
    {
        $this->content = $this->renderForm();
        $this->context->smarty->assign(array(
            'content' => $this->content,
        ));
    }

    public function renderForm()
    {
        $url = Tools::getHttpHost(true).__PS_BASE_URI__;

        $this->fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->module->l('Import / Export'),
            ),
            'input' => array(
                    array(
                        'type' => 'file',
                        'label' => $this->module->l('File'),
                        'name' => 'file',
                        'required' => true,
                    ),
                    array(
                        'type' => 'html',
                        'name' => 'export',
                        'label' => $this->module->l('Export'),
                        'html_content' => '
                        <div style="margin:0px 0 0 20px;">
                            <button name="export" class="btn btn-primary" value="1" type="submit">'
                            .$this->module->l('Export').
                            '</button>
                        </div>
                        ',
                    ),
                    array(
                        'type' => 'html',
                        'name' => 'help',
                        'label' => $this->module->l('Download Sample'),
                        'html_content' => '
                        <div style="margin:10px 0 0 20px;">
                            <div style="margin-bottom:5px;">
                                <a href="'.$url.'/modules/pickup/views/sample.csv" target="_blank">'
                                .$this->module->l('Download Sample CSV').
                                '</a>
                            </div>
                        </div>
                        ',
                    ),
            ),
            'submit' => array('title' => $this->module->l('Import'), 'name'=>'import'),
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

        $import = Tools::getValue('import');
        $export = Tools::getValue('export');
        if ($import) {
            return $this->import();
        } elseif ($export) {
            return $this->export();
        }
    }



    private function import()
    {
        $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        if (isset($_FILES['file']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
            $allowed = array('csv');
            $filename = $_FILES['file']['name'];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if (in_array($ext, $allowed)) {
                $filename = time().".csv";
                $file_path = _PS_ROOT_DIR_.'/modules/pickup/upload/'.$filename;
                move_uploaded_file($_FILES["file"]["tmp_name"], $file_path);

                $fileHandle = fopen($file_path, "r");
                $i = 0;
                while (($row = fgetcsv($fileHandle, 0, ";")) !== false) {
                    if ($i == 0) {
                        $i++;
                        continue;
                    }

                    $name           = $row[0];
                    $slug           = $row[1];
                    $code           = $row[2];
                    $desc           = $row[3];
                    $zone           = str_replace('?', "'", utf8_decode($row[4]));
                    $country        = str_replace('?', "'", utf8_decode($row[5]));
                    $state          = str_replace('?', "'", utf8_decode($row[6]));
                    $city           = str_replace('?', "'", utf8_decode($row[7]));
                    $address        = $row[8];
                    $lat            = $row[9];
                    $lng            = $row[10];
                    $zipcode        = $row[11];
                    $phone          = $row[12];
                    $email          = $row[13];
                    $website        = $row[14];
                    $facebook       = $row[15];
                    $skype          = $row[16];
                    $instagram      = $row[17];
                    $whatsapp       = $row[18];
                    $working_hours  = $row[19];
                    $everyday       = $row[20];
                    $perdayofweek   = $row[21];
                    $deliverytime_start = $row[22];
                    $deliverytime_end   = $row[23];
                    $meta_title     = $row[24];
                    $meta_description   = $row[26];
                    $meta_keyword   = $row[25];
                    $active         = $row[27];

                    $zone_id = 0;
                    $country_id = 0;
                    $state_id = 0;
                    if ($zone) {
                        $query = 'SELECT id_zone FROM `' . _DB_PREFIX_ . 'zone`
                        WHERE name LIKE "'.pSQL($zone).'" ';
                        $zone_id = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
                        if (!$zone_id) {
                            $query = 'INSERT INTO `' . _DB_PREFIX_ . 'zone`
                            (`name`,`active`) VALUES
                            ("'.$zone.'", 1)';
                            Db::getInstance()->execute($query);
                            $zone_id = Db::getInstance()->Insert_ID();
                        }
                    }
                    if ($country) {
                        $query = 'SELECT id_country FROM `' . _DB_PREFIX_ . 'country_lang`
                        WHERE name LIKE "'.pSQL($country).'" AND id_lang = '. (int)$id_lang;
                        $country_id = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
                        if (!$country_id) {
                            $query = 'INSERT INTO `' . _DB_PREFIX_ . 'country` (`id_zone`,`active`)
                            VALUES
                            ("'.$zone_id.'", 1)';
                            Db::getInstance()->execute($query);
                            $country_id = Db::getInstance()->Insert_ID();

                            $query = 'INSERT INTO `'. _DB_PREFIX_ . 'country_lang` (id_country,id_lang,`name`)
                            VALUES
                            ('.(int)$country_id.','.(int)$id_lang.',"'.$country.'")';
                            Db::getInstance()->execute($query);
                        }
                    }
                    if ($state) {
                        $query = 'SELECT id_zone FROM `' . _DB_PREFIX_ . 'state`
                        WHERE name LIKE "'.pSQL($state).'" AND id_country = '.(int)$country_id;
                        $state_id = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
                        if (!$state_id) {
                            $query = 'INSERT INTO `' . _DB_PREFIX_ . 'state` (id_country,id_zone,`name`,`active`)
                            VALUES
                            ('.(int)$country_id.','.(int)$zone_id.',"'.$state.'", 1)';
                            Db::getInstance()->execute($query);
                            $state_id = Db::getInstance()->Insert_ID();
                        }
                    }

                    $now = date('Y-m-d H:i:s');
                    if ($name && $code) {
                        Db::getInstance()->insert(
                            'pickup_store',
                            [
                                'name' => pSQL($name),
                                'slug' => pSQL($slug),
                                'code' => pSQL($code),
                                'description' => pSQL($desc),
                                'zone_id' => (int)$zone_id,
                                'country_id' => (int)$country_id,
                                'state_id' => (int)$state_id,
                                'city' => pSQL($city),
                                'address' => pSQL($address),
                                'lat' => pSQL($lat),
                                'lng' => pSQL($lng),
                                'zipcode' => pSQL($zipcode),
                                'phone' => pSQL($phone),
                                'email' => pSQL($email),
                                'website' => pSQL($website),
                                'facebook' => pSQL($facebook),
                                'skype' => pSQL($skype),
                                'instagram' => pSQL($instagram),
                                'whatsapp' => pSQL($whatsapp),
                                'working_hours' => pSQL($working_hours),
                                'everyday' => pSQL($everyday),
                                'perdayofweek' => pSQL($perdayofweek),
                                'deliverytime_start' => pSQL($deliverytime_start),
                                'deliverytime_end' => pSQL($deliverytime_end),
                                'meta_title' => pSQL($meta_title),
                                'meta_description' => pSQL($meta_description),
                                'meta_keyword' => pSQL($meta_keyword),
                                'active' => pSQL($active),
                                'date_add' => $now,
                                'date_upd' => $now
                            ]
                        );
                    }
                }
                $this->confirmations[] = $this->module->l("Import successful");
                return true;
            } else {
                $this->errors[] = $this->module->l('Invalid format file');
            }
        } else {
            $this->errors[] = $this->module->l('You must choose the csv file');
        }
        return false;
    }


    private function export()
    {
        $sql = 'SELECT a.*, s.name as `state`, c.name as country, z.name as `zone`
        FROM '. _DB_PREFIX_ .'pickup_store as a
        LEFT JOIN '. _DB_PREFIX_ .'zone as z ON z.id_zone = a.zone_id
        LEFT JOIN '. _DB_PREFIX_ .'state as s ON s.id_state = a.state_id
        LEFT JOIN '. _DB_PREFIX_ .'country_lang as c ON c.id_country = a.country_id
        ORDER BY a.id DESC';
        $results = Db::getInstance()->ExecuteS($sql);

        $export = array();
        foreach ($results as $result) {
            $export[] = array(
                $result['name'],
                $result['slug'],
                $result['code'],
                '',
                $result['zone'],
                $result['country'],
                $result['state'],
                $result['city'],
                $result['address'],
                $result['lat'],
                $result['lng'],
                $result['zipcode'],
                $result['phone'],
                $result['email'],
                $result['website'],
                $result['facebook'],
                $result['skype'],
                $result['instagram'],
                $result['whatsapp'],
                $result['working_hours'],
                $result['everyday'],
                $result['perdayofweek'],
                $result['deliverytime_start'],
                $result['deliverytime_end'],
                $result['meta_title'],
                $result['meta_description'],
                $result['meta_keyword'],
                $result['active']
            );
        }

        $this->arrayToCsvDownload($export, "exports.csv");

        parent::__construct();
        exit();
    }


    public function arrayToCsvDownload($array, $filename = "export.csv", $delimiter = ";")
    {
        // open raw memory as file so no temp files needed, you might run out of memory though
        $f = fopen('php://memory', 'w');
        // loop over the input array
        foreach ($array as $line) {
            // generate csv lines from the inner arrays
            fputcsv($f, $line, $delimiter);
        }
        // reset the file pointer to the start of the file
        fseek($f, 0);
        // tell the browser it's going to be a csv file
        header('Content-Type: application/csv');
        // tell the browser we want to save it instead of displaying it
        header('Content-Disposition: attachment; filename="'.$filename.'";');
        // make php send the generated csv lines to the browser
        fpassthru($f);
    }
}
