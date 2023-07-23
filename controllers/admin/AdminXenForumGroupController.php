<?php
/**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

class AdminXenForumGroupController extends ModuleAdminController
{
    public $id_xenforum_user;

    public function __construct()
    {
        $this->table = 'xenforum_group';
        $this->className = 'XenForumGroup';
        $this->lang = false;
        $this->bootstrap = true;
        $this->context = Context::getContext();
        parent::__construct();

        $this->fields_list = array(
                'id_xenforum_group' => array(
                    'title' => $this->l('Id'),
                    'width' => 100,
                    'type' => 'text',
                ),
                'title' => array(
                    'title' => $this->l('Name'),
                    'width' => 200,
                    'type' => 'text',
                ),
                'override_title' => array(
                    'title' => $this->l('Override'),
                    'width' => 150,
                    'type' => 'text',
                )
        );
    }

    public function setMedia($isNewTheme = false)
    {
        $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/admin.css', 'all');
        // Execute Hook AdminController SetMedia
        Hook::exec('actionAdminControllerSetMedia');
        
        return parent::setMedia();
    }

    public function renderList()
    {
        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function renderView()
    {
        $rules = new XenForumGroup();
        $id_group = (int)Tools::getValue('id_xenforum_group');
        $group_name = $rules->getGroupName($id_group);
        $rules_list = $rules->getList($id_group);
        $categories = XenForumCat::getCategories($this->context->language->id);
        $user = new XenForumUser();
        //$permissions = $user->permissions;
        $is_admin = ($id_group == XenForumUser::ID_ADMIN_GROUP);

        // if (null !== $key = array_search('_create_topic_', array_column($permissions, 'rule'))) {
        //     $id_rule_topic = $permissions[$key]['id_rule'];
        // }
        
        if (Tools::isSubmit('submitXenForum') && !$is_admin) {
            $res = true;
            $rules->deleteOnGroup($id_group);
            foreach ($rules_list as $list) {
                $new_rule = Tools::getValue($list['rule']);
                $validation = Tools::getValue($list['rule'].'_validation', null);
                if ($new_rule == 1) {
                    $res &= $rules->addNewRule((int)$id_group, (int)$list['id_rule'], $validation);
                }
            }
            $this->context->smarty->assign(array(
                'on_submit' => $res
            ));
        }

        $rules_list = $rules->getList($id_group);
        $this->context->smarty->assign(array(
                'categories'    => $categories,
                'group_name'    => $group_name,
                'id_group'      => $id_group,
                'is_admin'      => $is_admin,
                'rules_list'    => $rules_list,
                'link_back'     => Context::getContext()->link->getAdminLink('AdminXenForumGroup')
        ));

        return $this->context->smarty->fetch(dirname(__FILE__).'/../../views/templates/admin/permissions.tpl');
    }

    public function renderForm()
    {
        if (!$this->loadObject(true)) {
            return;
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Group'),
                'icon' => 'icon-plus'
            ),

            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'title',
                    'size' => 60,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Override name'),
                    'name' => 'override_title',
                    'size' => 60
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('Color'),
                    'name' => 'style'
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
