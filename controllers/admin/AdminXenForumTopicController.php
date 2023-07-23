<?php
/**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

class AdminXenForumTopicController extends ModuleAdminController
{
    public $bootstrap = true;
    public $id_xenforum;

    public function __construct()
    {
        $this->table = 'xenforum';
        $this->className = 'XenForumTopic';
        $this->lang = false;
        $this->bootstrap = true;
        $this->need_instance = 0;
        $this->list_id = 'xenforum';
        $this->identifier = 'id_xenforum';
        $this->_defaultOrderBy = 'id_xenforum';
        $this->_defaultOrderWay = 'DESC';
        $this->context = Context::getContext();
//        if (Shop::isFeatureActive()) {
//            Shop::addTableAssociation($this->table, array('type' => 'shop'));
//        }
        parent::__construct();

        $this->fields_list = array(
                'id_xenforum' => array(
                    'title' => $this->l('ID'),
                    'align' => 'center',
                    'class' => 'fixed-width-xs'
                ),
                'meta_title' => array(
                    'title' => $this->l('Title'),
                    'width' => 'auto',
                    'filter_key' => 'b!meta_title'
                ),
                'category_title' => array(
                    'title' => $this->l('Category'),
                    'width' => 'auto'
                ),
                'nickname' => array(
                    'title' => $this->l('Author'),
                    'align' => 'center',
                    'class' => 'fixed-width-md'
                ),
                'position' => array(
                    'title' => $this->l('Position'),
                    'align' => 'center',
                    'class' => 'fixed-width-md'
                ),
                'active' => array(
                    'title' => $this->l('Approved'),
                    'align' => 'center',
                    'active' => 'status',
                    'type' => 'bool',
                    'class' => 'fixed-width-xs',
                ),
                'created' => array(
                    'title' => $this->l('Date created'),
                    'width' => 150,
                    'type' => 'datetime',
                    'class' => 'fixed-width-xs'
                )
            );

        parent::__construct();
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->_select = '(SELECT pcl.`meta_title`
                        FROM `'._DB_PREFIX_.'xenforum_cat_lang` pcl
                        WHERE pcl.`id_xenforum_cat` = a.`id_xenforum_cat`
                                AND pcl.`id_lang` = '.(int)$this->context->language->id.') AS `category_title`, u.*';
        $this->_join = 'LEFT JOIN `'._DB_PREFIX_.'xenforum_user` u ON (u.`id_xenforum_user` = a.`id_author`)';
        //$this->_groupBy = ' a.id_xenforum';

        return parent::renderList();
    }

    public function initContent()
    {
        // toolbar (save, cancel, new, ..)
        //$this->initTabModuleList();
        //$this->initToolbar();
        if (version_compare(_PS_VERSION_, '1.6.0.0', '>')) {
            $this->initPageHeaderToolbar();
        }

        if ($this->display == 'edit' || $this->display == 'add') {
            if (!($this->object = $this->loadObject(true))) {
                return;
            }

            $this->content .= $this->renderForm();
        } elseif ($this->display != 'view' && !$this->ajax) {
            $this->content .= $this->displayInfos();
            $this->content .= $this->renderList();
        }

        $this->context->smarty->assign(array(
            'table' => $this->table,
            'current' => self::$currentIndex,
            'token' => $this->token,
            'content' => $this->content,
            'url_post' => self::$currentIndex.'&token='.$this->token
        ));
        if (version_compare(_PS_VERSION_, '1.6.0.0', '>')) {
            $this->context->smarty->assign(array(
                'show_page_header_toolbar' => $this->show_page_header_toolbar,
                'page_header_toolbar_title' => $this->page_header_toolbar_title,
                'page_header_toolbar_btn' => $this->page_header_toolbar_btn
            ));
        }
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
            // AdvancedFAQCat::cleanPositions();
        } elseif (Tools::isSubmit('submitAdd'.$this->table)) {
            //Hook::exec('actionObjectPollAddBefore');
            $id_xenforum = (int)Tools::getValue('id_xenforum');
            // Adding last position to the poll if not exist
            if ($id_xenforum <= 0) {
                $_POST['created'] = Date('Y-m-d H:i:s');
                $_POST['id_author'] = $this->context->employee->id;

                $sql = 'SELECT `position`+1
                        FROM `'._DB_PREFIX_.'xenforum`
                        ORDER BY position DESC';
                $_POST['position'] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            } else {
                $_POST['modified'] = Date('Y-m-d H:i:s');
            }

            // clean \n\r characters
            foreach ($_POST as $key => $value) {
                if (preg_match('/^name_/Ui', $key)) {
                    $_POST[$key] = str_replace('\n', '', str_replace('\r', '', $value));
                }
            }
            parent::postProcess();
        } else {
            parent::postProcess();
        }
    }

    public function processAdd()
    {
        $object = parent::processAdd();
        return ($this->updateTags($object->id));
    }

    public function processUpdate()
    {
        $object = parent::processUpdate();
        return ($this->updateTags($object->id));
        //return $object;
    }

    private function displayInfos()
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0 ', '>=')) {
            $module_topmenu = 'ps_mainmenu';
        } else {
            $module_topmenu = 'blocktopmenu';
        }
        $this->context->smarty->assign(array(
            'istree' => 1,
            'link_to_setting' => Context::getContext()->link->getAdminLink('AdminModules').'&configure='.$this->module->name,
            'link_to_blocktopmenu' => Context::getContext()->link->getAdminLink('AdminModules').'&configure='.$module_topmenu,
            'settings_url' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->module->name,
            'tree_url' => $this->context->link->getAdminLink('AdminXenForumCategory'),
            'topic_url' => $this->context->link->getAdminLink('AdminXenForumTopic'),
            'comment_url' => $this->context->link->getAdminLink('AdminXenForumPost'),
            'user_url' => $this->context->link->getAdminLink('AdminXenForumUser'),
            'permission_url' => $this->context->link->getAdminLink('AdminXenForumGroup'),
            'position_url' => $this->context->link->getAdminLink('AdminXenForumPosition'),
        ));

        return $this->context->smarty->fetch(dirname(__FILE__).'/../../views/templates/admin/infos.tpl');
    }

    public function renderForm()
    {
        $categories = XenForumCat::getCategoriesSelection($this->context->language->id);
        $id = (int)Tools::getvalue('id_xenforum');

        $type_radio = 'radio';
        if (version_compare(_PS_VERSION_, '1.6.0.0 ', '>=')) {
            $type_radio = 'switch';
        }

        $class_copy2url = 'copyTitle2friendlyURL';

        $this->show_form_cancel_button = true;
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Add/Edit Topic'),
                'icon' => 'icon-info-sign'
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Categories'),
                    'name' => 'id_xenforum_cat',
                    'options' => array(
                        'query' => $categories,
                        'id' => 'id_xenforum_cat',
                        'name' => 'meta_title'
                    ),
                    'hint' => $this->l('Choose the catagory for this post.')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Title'),
                    'name' => 'meta_title',
                    'id' => 'meta_title', // for copyMeta2friendlyURL compatibility
                    'class' => $class_copy2url,
                    'size' => 50,
                    'required' => true,
                    'hint' => $this->l('Invalid characters:').' <>;=#{}'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Friendly URL'),
                    'name' => 'link_rewrite',
                    'size' => 50,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Author ID'),
                    'name' => 'id_author',
                    'size' => 20
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Position'),
                    'name' => 'position',
                    'size' => 20
                ),
                array(
                    'type' => $type_radio,
                    'label' => $this->l('Highlight'),
                    'name' => 'highlight',
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'highlight_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'highlight_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => $type_radio,
                    'label' => $this->l('Close this topic'),
                    'name' => 'closed',
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'closed_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'closed_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => $type_radio,
                    'label' => $this->l('Displayed'),
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
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Tags'),
                    'name' => 'tags',
                    'size' => 50
                ),
            )
        );

        $this->fields_form['submit'] = array(
            'title' => $this->l('Save'),
        );

        if (!($this->loadObject(true))) {
            return;
        }

        $this->tpl_form_vars = array(
            'active' => $this->object->active,
            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL')
        );
        if ($id) {
            $result = '';
            $tags = array();
            $tags = XenForumTags::getTags($id);
            if ($tags) {
                $result = implode(', ', $tags);
            }

            $this->fields_value['tags'] = $result;
        }
        return parent::renderForm();
    }

    public function updateTags($id_post)
    {
        $tag_success = true;
        if (!XenForumTags::deleteTags($id_post)) {
            $this->errors[] = Tools::displayError('An error occurred while attempting to delete previous tags.');
        }

        if ($value = Tools::getValue('tags')) {
            $tag_success &= XenForumTags::addTags($id_post, $value);
        }

        if (!$tag_success) {
            $this->errors[] = Tools::displayError('An error occurred while adding tags.');
        }

        return $tag_success;
    }
}
