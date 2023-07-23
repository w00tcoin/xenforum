<?php
/**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

class AdminXenForumPostController extends ModuleAdminController
{
    public $bootstrap = true;
    public $id_xenforum_post;
    public $asso_type = 'shop';

    public function __construct()
    {
        $this->table = 'xenforum_post';
        $this->className = 'XenForumPost';
        $this->module = 'xenforum';
        $this->bootstrap = true;
        $this->lang = false;
        $this->need_instance = 0;
        $this->context = Context::getContext();

        parent::__construct();

        $this->fields_list = array(
                'id_xenforum_post' => array(
                        'title' => $this->l('Id'),
                        'width' => 40,
                        'type' => 'text',
                ),
                'meta_title' => array(
                        'title' => $this->l('Topic title'),
                        'width' => 200,
                        'type' => 'text'
                ),
                'nickname' => array(
                        'title' => $this->l('Author'),
                        'width' => 100,
                        'type' => 'text'
                ),
                'created' => array(
                        'title' => $this->l('Date'),
                        'width' => 80,
                        'type' => 'datetime',
                        'lang' => true
                ),
                'active' => array(
                        'title' => $this->l('Status'),
                        'width' => 70,
                        'align' => 'center',
                        'active' => 'status',
                        'type' => 'bool',
                        'orderby' => false
                )
        );
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->_select = 'x.meta_title AS meta_title, u.*';
        $this->_join = 'LEFT JOIN `'._DB_PREFIX_.'xenforum` x ON (a.`id_xenforum` = x.`id_xenforum`)
                        LEFT JOIN `'._DB_PREFIX_.'xenforum_user` u ON (u.`id_xenforum_user` = a.`id_author`)';
        $this->_defaultOrderBy = 'a.id_xenforum_post';
        $this->_defaultOrderWay = 'DESC';

        return parent::renderList();
    }

    public function postProcess()
    {
        if (Tools::getValue('submitDel'.$this->table)) {
            if ($this->tabAccess['delete'] === '1') {
                if (Tools::getIsset(Tools::getValue($this->table.'Box'))) {
                    $object = new $this->className();
                    if ($object->deleteSelection(Tools::getValue($this->table.'Box'))) {
                        Tools::redirectAdmin(self::$currentIndex.'&conf=2&token='.$this->token);
                    }

                    $this->errors[] = Tools::displayError('An error occurred while deleting this selection.');
                } else {
                    $this->errors[] = Tools::displayError('You must select at least one element to delete.');
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to delete this.');
            }
                // clean position after delete
        } else if (Tools::isSubmit('submitAdd'.$this->table)) {
            //Hook::exec('actionObjectPollAddBefore');
            $id_xenforum = (int)Tools::getValue('id_xenforum');
            if ($id_xenforum <= 0) {
                $_POST['created'] = Date('Y-m-d H:i:s');
            }

                // clean \n\r characters
            foreach ($_POST as $key => $value) {
                if (preg_match('/^name_/Ui', $key)) {
                    $_POST[$key] = str_replace('\n', '', str_replace('\r', '', $value));
                }
            }

            $_POST['title'] = Tools::substr(trim(preg_replace('/\s\s+/', ' ', strip_tags(Tools::getValue('comment')))), 0, 50).'...';

            parent::postProcess();
        } else {
            parent::postProcess();
        }
    }

    public function renderForm()
    {
        $type_radio = 'radio';
        if (version_compare(_PS_VERSION_, '1.6.0.0 ', '>=')) {
            $type_radio = 'switch';
        }

        $this->fields_form = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('Comment'),
                'icon' => 'icon-plus'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Id Topic'),
                    'name' => 'id_xenforum',
                    'size' => 50,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Author Id'),
                    'name' => 'id_author',
                    'size' => 50,
                    'required' => true
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Comment'),
                    'name' => 'comment',
                    'required' => true,
                    'rows' => 10,
                    'cols' => 62,
                    'autoload_rte' => true
                ),
                array(
                    'type' => $type_radio,
                    'label' => $this->l('Status'),
                    'name' => 'active',
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                )
            )
        );

        $this->fields_form['submit'] = array(
            'title' => $this->l('Save'),
        );

        if (!($this->loadObject(true))) {
            return;
        }

        return parent::renderForm();
    }
}
