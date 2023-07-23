<?php
/**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

class AdminXenForumPositionController extends ModuleAdminController
{
    public $id_xenforum_position;
    protected $blocks;
    protected $positions;

    public function __construct()
    {
        $this->table = 'xenforum_position';
        $this->className = 'XenForumPosition';
        $this->lang = false;
        $this->bootstrap = true;
        $this->_defaultOrderBy = 'block';
        $this->_defaultOrderWay = 'ASC';
        $this->context = Context::getContext();

        parent::__construct();

        $this->fields_list = array(
                'id_xenforum_position' => array(
                    'title' => $this->l('Id'),
                    'width' => 50,
                    'type' => 'text',
                ),
                'block' => array(
                    'title' => $this->l('Block'),
                    'width' => 150,
                    'type' => 'text',
                ),
                'position' => array(
                    'title' => $this->l('Position'),
                    'width' => 150,
                    'type' => 'text',
                ),
                'excepts' => array(
                    'title' => $this->l('Excepts'),
                    'width' => 250,
                    'type' => 'text',
                )
        );

        $this->blocks = array(
            array(
                'key' => 'search',
                'name' => $this->l('Search')
            ),
            array(
                'key' => 'account',
                'name' => $this->l('My Account')
            ),
            array(
                'key' => 'latest',
                'name' => $this->l('Latest post')
            ),
            array(
                'key' => 'statistics',
                'name' => $this->l('Statistics')
            ),
            array(
                'key' => 'addtopic',
                'name' => $this->l('Create New Topic')
            )
        );
        
        $this->positions = array(
                array(
                    'key' => 'hidden',
                    'name' => $this->l('(Hidden)')
                ),
                array(
                    'key' => 'left',
                    'name' => $this->l('Left')
                ),
                array(
                    'key' => 'right',
                    'name' => $this->l('Right')
                )
        );
    }

    public function setMedia($isNewTheme = false)
    {
        $this->addJqueryUI('ui.sortable');
        $this->addCSS(array(_MODULE_DIR_.$this->module->name.'/views/css/sortable.css'));
        return parent::setMedia();
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_xenforum_position'] = array(
                'href' => self::$currentIndex.'&addxenforum_position&token='.$this->token,
                'desc' => $this->l('Add new block', null, null, false),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function initContent()
    {
        if (version_compare(_PS_VERSION_, '1.6.0.0', '>')) {
            $this->initPageHeaderToolbar();
        }

        if ($this->display == 'edit' || $this->display == 'add') {
            if (!($this->object = $this->loadObject(true))) {
                return;
            }

            $this->content .= $this->renderForm();
        } else if ($this->display != 'view' && !$this->ajax) {
            $this->content .= $this->displayPositions();
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
        // parent::initContent();
    }

    private function displayPositions()
    {
        $position = new XenForumPosition();
        $left_blocks = $position->getBlocks('left');
        $right_blocks = $position->getBlocks('right');
        $hidden_blocks = $position->getBlocks('hidden');
        $this->context->smarty->assign(array(
            'left_blocks' => $left_blocks,
            'right_blocks' => $right_blocks,
            'hidden_blocks' => $hidden_blocks,
            'url_update' => self::$currentIndex.'&updatexenforum_position&token='.$this->token,
        ));

        return $this->context->smarty->fetch(dirname(__FILE__).'/../../views/templates/admin/position.tpl');
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->toolbar_title = $this->l('Positions');
        $this->toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add New Block', null, null, false)
        );
        return parent::renderList();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitReOrderPositions')) {
            // Submit re-order Tasks
            $left_blocks = Tools::jsonDecode(Tools::getValue('sortable1'));
            $right_blocks = Tools::jsonDecode(Tools::getValue('sortable3'));
            $unallocated_blocks = Tools::jsonDecode(Tools::getValue('sortable2'));
            if ($left_blocks && !empty($left_blocks)) {
                foreach ($left_blocks as $key => $value) {
                    $model = new XenForumPosition((int)$value);
                    if ($model) {
                        $model->position = 'left';
                        $model->sort_order = $key;
                        $model->update();
                    }
                }
            }
            if ($right_blocks && !empty($right_blocks)) {
                foreach ($right_blocks as $key => $value) {
                    $model = new XenForumPosition((int)$value);
                    if ($model) {
                        $model->position = 'right';
                        $model->sort_order = $key;
                        $model->update();
                    }
                }
            }
            if ($unallocated_blocks && !empty($unallocated_blocks)) {
                foreach ($unallocated_blocks as $key => $value) {
                    $model = new XenForumPosition((int)$value);
                    if ($model) {
                        $model->position = 'hidden';
                        $model->sort_order = $key;
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
        if (!$this->loadObject(true)) {
            return;
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Positions'),
                'icon' => 'icon-plus'
            ),

            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Block'),
                    'name' => 'block',
                    'options' => array(
                            'query' => $this->blocks,
                            'id' => 'key',
                            'name' => 'name'
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Positions'),
                    'name' => 'position',
                    'options' => array(
                            'query' => $this->positions,
                            'id' => 'key',
                            'name' => 'name'
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Except pages'),
                    'name' => 'excepts',
                    'size' => 60,
                    'desc' => $this->l('Use commas to separate items. Allowed values:')
                            .' home, category, member, tags, search, details, topicedit, topiccreate, editpost, term, help'
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
