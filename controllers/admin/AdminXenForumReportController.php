<?php
/**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

class AdminXenForumReportController extends ModuleAdminController
{
    public $id_xenforum_report;

    public function __construct()
    {
        $this->table = 'xenforum_report';
        $this->className = 'XenForumReport';
        $this->lang = false;
        $this->bootstrap = true;
        $this->context = Context::getContext();
        parent::__construct();

        $this->fields_list = array(
                'topic_title' => array(
                    'title' => $this->l('Post in topic:'),
                    'width' => 200,
                    'type' => 'text',
                ),
                'id_post' => array(
                    'title' => $this->l('Id #post'),
                    'width' => 100,
                    'type' => 'text',
                ),
                'reason' => array(
                    'title' => $this->l('Reason'),
                    'width' => 300,
                    'type' => 'text',
                ),
                'nickname' => array(
                    'title' => $this->l('Reported by'),
                    'width' => 100,
                    'type' => 'text',
                ),
                'date_add' => array(
                    'title' => $this->l('Date'),
                    'width' => 100,
                    'type' => 'datetime'
                )
        );
    }

    public function setMedia($isNewTheme = false)
    {
        $this->addCSS(array(_MODULE_DIR_.$this->module->name.'/views/css/admin.css'));
        return parent::setMedia();
    }

    public function renderList()
    {
        $this->addRowAction('preview');
        $this->addRowAction('delete');
        $this->toolbar_btn = array();

        $this->_select = 't.meta_title AS topic_title, c.nickname as nickname';
        $this->_join = 'LEFT JOIN '._DB_PREFIX_.'xenforum_post p ON (a.id_post = p.id_xenforum_post)
                        LEFT JOIN '._DB_PREFIX_.'xenforum t ON (p.id_xenforum = t.id_xenforum)
                        LEFT JOIN '._DB_PREFIX_.'xenforum_user c ON (a.id_user = c.id_xenforum_user)';

        return parent::renderList();
    }

    public function displayPreviewLink($token = null, $id = 0, $name = null)
    {
        $tpl = $this->createTemplate('helpers/list/list_action_preview.tpl');
        if (!array_key_exists('Bad SQL query', self::$cache_lang)) {
            self::$cache_lang['Preview'] = $this->l('Preview', 'Helper');
        }

        $tpl->assign(array(
            'href' => $this->getPreviewUrl((int)$id),
            'action' => self::$cache_lang['Preview'],
        ));

        return $tpl->fetch();
    }

    public function getPreviewUrl($id_report)
    {
        $reporter = new XenForumReport($id_report);
        $id_post = $reporter->id_post;
        $comment = new XenForumPost($id_post);
        $id_topic = $comment->id_xenforum;
        $topic = new XenForumTopic($id_topic);

        $options = array(
                'id_xenforum' => $topic->id_xenforum,
                'slug' => $topic->link_rewrite
        );

        $preview_url = xenforum::getLink('xenforum_viewdetails', $options).'#post-'.$id_post;
        return $preview_url;
    }

    /* public function renderView()
    {
        $rules = new XenForumGroup();
        $id_group = (int)Tools::getValue('id_xenforum_report');
        $group_name = $rules->getGroupName($id_group);
        $rules_list = $rules->getList($id_group);
        $categories = XenForumCat::getCategories($this->context->language->id);
        $user = new XenForumUser();
        $permissions = $user->permissions;
        $create_topic_validation = array();
        $is_admin = $id_group == 2;

        if (null !== $key = array_search('_create_topic_', array_column($permissions, 'rule'))) {
            $id_rule_topic = $permissions[$key]['id_rule'];
        }
        
        if (Tools::isSubmit('submitXenForum') && !$is_admin) {
            $forums = Tools::getValue("forums");
            if (!empty($forums)) {
                $forums = array_keys($forums);
                $forums = implode(",", $forums);
            }

            $res = true;
            $rules->deleteOnGroup($id_group);
            foreach ($rules_list as $list) {
                $validation = null;
                $new_rule = Tools::getValue($list['rule']);
                if ($new_rule == 1) {
                    if ($list['id_rule'] == $id_rule_topic) {
                        $validation = $forums;
                    }
                    $res &= $rules->addNewRule((int)$id_group, (int)$list['id_rule'], $validation);
                }
            }
            $this->context->smarty->assign(array(
                    'on_submit' => $res
            ));
        }

        $create_topic = $rules->getRule($id_group, $id_rule_topic);
        if (!empty($create_topic)) {
            $create_topic_validation = $create_topic['validation'];
            if (is_string($create_topic_validation)) {
                $create_topic_validation = explode(",", $create_topic_validation);
            }
        }

        $rules_list = $rules->getList($id_group);
        $this->context->smarty->assign(array(
                'validations'   => $create_topic_validation,
                'categories'    => $categories,
                'group_name'    => $group_name,
                'id_group'      => $id_group,
                'is_admin'      => $is_admin,
                'rules_list'    => $rules_list,
                'link_back'     => Context::getContext()->link->getAdminLink('AdminXenForumGroup')
        ));

        return $this->context->smarty->fetch(dirname(__FILE__).'/../../views/templates/admin/permissions.tpl');
    } */

    public function renderForm()
    {
        // generate Form
        return parent::renderForm();
    }
}
