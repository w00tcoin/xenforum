<?php
/**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

class AdminXenForumCategoryController extends ModuleAdminController
{
    public $bootstrap = true;
    public $id_xenforum;

    public function __construct()
    {
        $this->table = 'xenforum_cat';
        $this->className = 'XenForumCat';
        $this->lang = true;
        $this->bootstrap = true;
        $this->need_instance = 0;
        $this->list_id = 'xenforum_cat';
        $this->identifier = 'id_xenforum_cat';
        $this->context = Context::getContext();
//        if (Shop::isFeatureActive()) {
//            Shop::addTableAssociation($this->table, array('type' => 'shop'));
//        }

        parent::__construct();

        $this->fields_list = array(
                'id_xenforum_cat' => array(
                        'title' => $this->l('ID'),
                        'width' => 70,
                        'type' => 'text',
                        'class' => 'fixed-width-xs'
                ),
                'meta_title' => array(
                        'title' => $this->l('Name'),
                        'type' => 'text',
                        'width' => 'auto',
                        'lang' => true
                ),
                'id_parent' => array(
                        'title' => $this->l('Parent ID'),
                        'type' => 'text',
                        'width' => 'auto',
                        'lang' => true
                ),
                'link_rewrite' => array(
                        'title' => $this->l('Link rewrite'),
                        'type' => 'text',
                        'width' => 'auto',
                        'lang' => true
                ),
                'position' => array(
                        'title' => $this->l('Position'),
                        'width' => 120,
                        'align' => 'center'
                ),
                'closed' => array(
                        'title' => $this->l('Closed'),
                        'width' => 100,
                        'align' => 'center',
                        'closed' => 'status',
                        'type' => 'bool',
                        'orderby' => false
                ),
                'active' => array(
                        'title' => $this->l('Status'),
                        'width' => 100,
                        'align' => 'center',
                        'active' => 'status',
                        'type' => 'bool',
                        'orderby' => false
                )
        );

        parent::__construct();
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->toolbar_title = $this->l('Forums');

        $this->toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add New Forum', null, null, false)
        );
        return parent::renderList();
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_xenforum_cat'] = array(
                'href' => self::$currentIndex.'&addxenforum_cat&token='.$this->token,
                'desc' => $this->l('Add new forum', null, null, false),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function setMedia($isNewTheme = false)
    {
        $this->addJqueryUI('ui.sortable');
        $this->addJS(_MODULE_DIR_.$this->module->name.'/libraries/jquery.xenforumSortable.min.js');
        $this->addCSS(array(_MODULE_DIR_.$this->module->name.'/views/css/sortable.css'));
        return parent::setMedia();
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
        } else if ($this->display != 'view' && !$this->ajax) {
            $this->content .= $this->displayInfos();
            $this->content .= $this->displayTree();
            $this->content .= $this->renderList();
        }

        $this->context->smarty->assign(array(
            'table' => $this->table,
            'current' => self::$currentIndex,
            'token' => $this->token,
            'content' => $this->content,
            // 'url_post' => self::$currentIndex.'&token='.$this->token
        ));

        if (version_compare(_PS_VERSION_, '1.6.0.0', '>')) {
            $this->context->smarty->assign(array(
                'show_page_header_toolbar' => $this->show_page_header_toolbar,
                'page_header_toolbar_title' => $this->page_header_toolbar_title,
                'page_header_toolbar_btn' => $this->page_header_toolbar_btn
            ));
        }
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

    private function displayTree()
    {
        $categories = XenForumCat::getCategories($this->context->language->id);
        $this->context->smarty->assign(array(
            'categories' => $categories,
            'url_update' => self::$currentIndex.'&updatexenforum_cat&token='.$this->token,
            'url_delete' => self::$currentIndex.'&deletexenforum_cat&token='.$this->token,
        ));

        return $this->context->smarty->fetch(dirname(__FILE__).'/../../views/templates/admin/tree.tpl');
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
            $id_xenforum_cat = (int)Tools::getValue('id_xenforum_cat');
            // Adding last position to the poll if not exist
            if ($id_xenforum_cat <= 0) {
                $_POST['created'] = Date('Y-m-d H:i:s');

                $sql = 'SELECT `position`+1
                        FROM `'._DB_PREFIX_.'xenforum_cat`
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
        } elseif (Tools::isSubmit('submitReOrderTree')) {
            // Submit re-order Tasks
            $data = Tools::jsonDecode(Tools::getValue('allcategories'));
            foreach ($data as $key => $value) {
                if ($value->item_id) {
                    $model = new XenForumCat((int)$value->item_id);
                    if ($model) {
                        $model->id_parent = $value->parent_id;
                        $model->position = $key;
                        $model->update();
                    }
                }
            }
            Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
        } else {
            parent::postProcess();
        }
    }

    public function renderForm()
    {
        $w = array('id_xenforum_cat' => 0, 'meta_title' => $this->l('None'));
        $categories = XenForumCat::getCategoriesSelection($this->context->language->id);
        $categories = array_merge(array($w), $categories);

        $type_radio = 'radio';
        if (version_compare(_PS_VERSION_, '1.6.0.0 ', '>=')) {
            $type_radio = 'switch';
        }

        $class_copy2url = 'copy2friendlyUrl';
        if (version_compare(_PS_VERSION_, '1.6.0.0 ', '>=')) {
            $class_copy2url = 'copyMeta2friendlyURL';
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Add new/Edit forum'),
                'icon' => 'icon-plus'
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Parent'),
                    'name' => 'id_parent',
                    'options' => array(
                        'query' => $categories,
                        'id' => 'id_xenforum_cat',
                        'name' => 'meta_title'
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Title'),
                    'name' => 'meta_title',
                    'id' => 'name', // for copyMeta2friendlyURL compatibility
                    'class' => $class_copy2url,
                    'size' => 50,
                    'required' => true,
                    'lang' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Link rewrite'),
                    'name' => 'link_rewrite',
                    'size' => 50,
                    'required' => true,
                    'lang' => true
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Description'),
                    'name' => 'description',
                    'lang' => true,
                    'rows' => 10,
                    'cols' => 62
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Position'),
                    'name' => 'position',
                    'size' => 20
                ),
                array(
                    'type' => $type_radio,
                    'label' => $this->l('Private'),
                    'name' => 'private',
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'private_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'private_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    //'desc' =>  $this->l('Can not create new topics')
                ),
                array(
                    'type' => $type_radio,
                    'label' => $this->l('Closed'),
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
                    'desc' =>  $this->l('If true, Cannot create new topics')
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
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            )
        );

        if (!$this->loadObject(true)) {
            return;
        }

        $this->tpl_form_vars = array(
            'active' => $this->object->active,
            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL')
        );
        return parent::renderForm();
    }
}
