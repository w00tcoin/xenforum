<?php
/**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

class AdminXenForumUserController extends AdminController
{
    public $id_xenforum_user;

    public function __construct()
    {
        $this->table = 'xenforum_user';
        $this->className = 'XenForumUser';
        $this->lang = false;
        $this->bootstrap = true;
        $this->context = Context::getContext();
        parent::__construct();

        $this->fields_list = array(
                'id_xenforum_user' => array(
                    'title' => $this->l('Id'),
                    'width' => 100,
                    'type' => 'text',
                ),
                'nickname' => array(
                    'title' => $this->l('NickName'),
                    'width' => 200,
                    'type' => 'text',
                ),
                'email' => array(
                    'title' => $this->l('Email'),
                    'width' => 150,
                    'type' => 'text',
                ),
                'group_title' => array(
                    'title' => $this->l('Group'),
                    'width' => 150,
                    'type' => 'text',
                ),
                'is_staff' => array(
                    'title' => $this->l('Is Staff'),
                    'width' => 60,
                    'align' => 'center',
                    'active' => 'is_staff',
                    'type' => 'bool',
                    'orderby' => false
                ),
                'approved' => array(
                    'title' => $this->l('Approved'),
                    'width' => 60,
                    'align' => 'center',
                    'active' => 'approved',
                    'type' => 'bool',
                    'orderby' => false
                ),
                'date_upd' => array(
                        'title' => $this->l('Last visit'),
                        'width' => 80,
                        'type' => 'datetime'
                ),
        );
    }

    public function renderList()
    {
        $this->addRowAction('edit');

        $this->toolbar_btn = array();

        $this->_select = 'g.title AS group_title, c.*';
        $this->_join = 'LEFT JOIN '._DB_PREFIX_.'xenforum_group g ON (a.id_xenforum_group = g.id_xenforum_group)
                        LEFT JOIN '._DB_PREFIX_.'customer c ON (a.id_xenforum_user = c.id_customer)';

        return parent::renderList();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('is_staff'.$this->table)) {
            $id = (int)Tools::getValue('id_'.$this->table);
            $object = new $this->className($id);
            $object->is_staff = !$object->is_staff;
            if ($object->update()) {
                Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
            } else {
                $this->errors[] = Tools::displayError('An error occurred while update this selection.');
            }
        } elseif (Tools::isSubmit('approved'.$this->table)) {
            $id = (int)Tools::getValue('id_'.$this->table);
            $object = new $this->className($id);
            $object->approved = !$object->approved;
            if ($object->update()) {
                Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
            } else {
                $this->errors[] = Tools::displayError('An error occurred while update this selection.');
            }
        } else {
            parent::postProcess();
        }
    }

    public function renderForm()
    {
        if (!$this->loadObject(true)) {
            return;
        }

        $groups = XenForumUser::getGroupList();

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Customer'),
                'icon' => 'icon-plus'
            ),

            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Nickname'),
                    'name' => 'nickname',
                    'size' => 60,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Quotes'),
                    'name' => 'signature',
                    'size' => 60
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('City'),
                    'name' => 'city',
                    'size' => 60
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Country'),
                    'name' => 'id_country',
                    'default_value' => (int)$this->context->country->id,
                    'options' => array(
                        'query' => Country::getCountries((int)$this->context->language->id),
                        'id' => 'id_country',
                        'name' => 'name',
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Group'),
                    'name' => 'id_xenforum_group',
                    'required' => true,
                    'options' => array(
                        'query' => $groups,
                        'id' => 'id_xenforum_group',
                        'name' => 'title'
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Get notifications via email'),
                    'name' => 'notification',
                    'values' => array(
                                array(
                                    'id' => 'notification_on',
                                    'value' => 1,
                                    'label' => $this->l('Enabled')
                                ),
                                array(
                                    'id' => 'notification_off',
                                    'value' => 0,
                                    'label' => $this->l('Disabled')
                                )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Is Staff'),
                    'name' => 'is_staff',
                    'values' => array(
                                array(
                                    'id' => 'staff_on',
                                    'value' => 1,
                                    'label' => $this->l('Enabled')
                                ),
                                array(
                                    'id' => 'staff_off',
                                    'value' => 0,
                                    'label' => $this->l('Disabled')
                                )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Approved'),
                    'name' => 'approved',
                    'values' => array(
                                array(
                                    'id' => 'approved_on',
                                    'value' => 1,
                                    'label' => $this->l('Enabled')
                                ),
                                array(
                                    'id' => 'approved_off',
                                    'value' => 0,
                                    'label' => $this->l('Disabled')
                                )
                    ),
                )
            ),
            'submit' => array(
                'title' => $this->l('Save')
            )
        );

        // generate Form
        return parent::renderForm();
    }
}
