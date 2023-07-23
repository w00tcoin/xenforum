<?php
/**
* Prestashop Addons | Module by: <App1Pro>
*
* @author    Chuyen Nguyen [App1Pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*
* NOTICE OF LICENSE
*
* Don't use this module on several shops. The license provided by PrestaShop Addons
* for all its modules is valid only once for a single shop.
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

define('_MODULE_XENFORUM_AVT_DIR_', _PS_MODULE_DIR_.'xenforum/views/img/users/');
define('_MODULE_XENFORUM_AVT_URI_', __PS_BASE_URI__.'modules/xenforum/views/img/users/');
define('_MODULE_XENFORUM_UPLOAD_DIR_', _PS_MODULE_DIR_.'xenforum/uploads/');
define('_MODULE_XENFORUM_UPLOAD_URI_', __PS_BASE_URI__.'modules/xenforum/uploads/');
require_once(dirname(__FILE__).'/classes/XenForumAttachment.php');
require_once(dirname(__FILE__).'/classes/XenForumTopic.php');
require_once(dirname(__FILE__).'/classes/XenForumCat.php');
require_once(dirname(__FILE__).'/classes/XenForumPost.php');
require_once(dirname(__FILE__).'/classes/XenForumUser.php');
require_once(dirname(__FILE__).'/classes/XenForumNotification.php');
require_once(dirname(__FILE__).'/classes/XenForumGroup.php');
require_once(dirname(__FILE__).'/classes/XenForumTags.php');
require_once(dirname(__FILE__).'/classes/XenForumPosition.php');
require_once(dirname(__FILE__).'/classes/XenForumReport.php');
require_once(dirname(__FILE__).'/classes/XenForumRelated.php');

class XenForum extends Module
{
    public $terms = '&lt;p&gt;The providers (&quot;we&quot;, &quot;us&quot;, &quot;our&quot;) of the service provided by this web site (&quot;&lt;strong&gt;Service&lt;/strong&gt;&quot;) are not responsible for any user-generated content and accounts (&quot;&lt;strong&gt;Content&lt;/strong&gt;&quot;). &lt;strong&gt;Content&lt;/strong&gt; submitted express the views of their author only.&lt;/p&gt;
&lt;p&gt;You agree to not use the &lt;strong&gt;Service&lt;/strong&gt; to submit or link to any &lt;strong&gt;Content&lt;/strong&gt; which is defamatory, abusive, hateful, threatening, spam or spam-like, likely to offend, contains adult or objectionable content, contains personal information of others, risks copyright infringement, encourages unlawful activity, or otherwise violates any laws.&lt;/p&gt;
&lt;p&gt;All &lt;strong&gt;Content&lt;/strong&gt; you submit or upload may be reviewed by staff members. All &lt;strong&gt;Content&lt;/strong&gt; you submit or upload may be sent to third-party verification services (including, but not limited to, spam prevention services). Do not submit any &lt;strong&gt;Content&lt;/strong&gt; that you consider to be private or confidential.&lt;/p&gt;
&lt;p&gt;We reserve the rights to remove or modify any &lt;strong&gt;Content&lt;/strong&gt; submitted for any reason without explanation. Requests for &lt;strong&gt;Content&lt;/strong&gt; to be removed or modified will be undertaken only at our discretion. We reserve the right to take action against any account with the &lt;strong&gt;Service&lt;/strong&gt; at any time.&lt;/p&gt;
&lt;p&gt;You are granting us with a non-exclusive, permanent, irrevocable, unlimited license to use, publish, or re-publish your &lt;strong&gt;Content&lt;/strong&gt; in connection with the &lt;strong&gt;Service&lt;/strong&gt;. You retain copyright over the &lt;strong&gt;Content&lt;/strong&gt;.&lt;/p&gt;
&lt;p&gt;These terms may be changed at any time without notice.&lt;/p&gt;
&lt;p&gt;If you do not agree with these terms, please do not register or use this &lt;strong&gt;Service&lt;/strong&gt;. If you wish to close your account, please contact us.&lt;/p&gt;';
    protected $displayBlocks;

    public function __construct()
    {
        $this->name = 'xenforum';
        $this->tab = 'content_management';
        $this->version = '2.7.5';
        $this->author = 'App1pro';
        $this->controllers = array('home', 'topiccreate', 'editpost', 'handleajax', 'postquote', 'postdelete', 'notifdelete',
                            'topicedit', 'topicdelete', 'category', 'member', 'details', 'tags', 'search', 'term', 'help');
        //$this->need_upgrade = true;


        $this->bootstrap = true;

        parent::__construct();
        $this->displayName = $this->l('Xen Forum');
        $this->description = $this->l('A powerful forum optimized for SEO.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');
        
        $this->displayBlocks = array(
            'search' => 'displaySearch',
            'account' => 'displayMyAccount',
            'latest' => 'displayLatest',
            'statistics' => 'displayStats',
            'addtopic' => 'displayAddTopic'
        );
    }

    public function install()
    {
        Configuration::updateValue('XENFORUM_URL', 'xenforum');
        Configuration::updateValue('XENFORUM_PRIVATE', 0);
        Configuration::updateValue('XENFORUM_SHOW_LCOLUMN', 1);
        Configuration::updateValue('XENFORUM_SHOW_RCOLUMN', 1);
        Configuration::updateValue('XENFORUM_USE_DOT_HTML', 1);
        Configuration::updateValue('XENFORUM_ENABLE_COM', 1);
        Configuration::updateValue('XENFORUM_LIKE_MYSELF', 1);
        Configuration::updateValue('XENFORUM_NUM_POST', 10);
        Configuration::updateValue('XENFORUM_NUM_COM', 20);
        Configuration::updateValue('XENFORUM_NUM_SIDEBAR', 10);
        Configuration::updateValue('XENFORUM_NUM_ADMIN_TOP', 5);
        Configuration::updateValue('XENFORUM_REPORTS_MAX', 5);
        Configuration::updateValue('XENFORUM_WAIT_APPROVE', 0);
        Configuration::updateValue('XENFORUM_FB_COMMENT', 0);
        Configuration::updateValue('XENFORUM_NATIVE_COM_OFF', 0);
        Configuration::updateValue('XENFORUM_FB_APP_ID', '');

        $languages = Language::getLanguages(false);
        $title = 'Forums';
        $desc = 'Powered by App1pro';
        $keyw = '';
        $intro = '';
        $help = $this->terms;

        foreach ($languages as $lang) {
            $this->installFixture((int)$lang['id_lang'], $title, $desc, $keyw, $intro, $help);
        }

        if (!parent::install()
        || !$this->createAdminTabs()
        || !$this->insertNewHook()
        || !$this->registerHook('displayHeader')
        || !$this->registerHook('displayBackOfficeHeader')
        || !$this->registerHook('displayBackOfficeTop')
        || !$this->registerHook('displayleftColumn')
        || !$this->registerHook('displayXenForumLeft')
        || !$this->registerHook('displayXenForumRight')
        || !$this->registerHook('displayFooterProduct')
        || !$this->registerHook('displayCustomerAccount')
        || !$this->registerHook('actionCustomerAccountAdd')
        || !$this->registerHook('moduleRoutes')) {
            return false;
        }

        require_once(dirname(__FILE__).'/sql/install.php');
        require_once(dirname(__FILE__).'/sql/sampledata.php');
        return true;
    }

    protected function installFixture($id_lang, $title = null, $desc = null, $keyw = null, $intro = null, $term = null, $help = null)
    {
        $values = array();
        $values['TITLE'][(int)$id_lang] = $title;
        $values['DESC'][(int)$id_lang] = $desc;
        $values['KEYW'][(int)$id_lang] = $keyw;
        $values['HEAD'][(int)$id_lang] = $intro;
        $values['TERM'][(int)$id_lang] = $term;
        $values['HELP'][(int)$id_lang] = $help;
        Configuration::updateValue('XENFORUM_TITLE', $values['TITLE']);
        Configuration::updateValue('XENFORUM_DESC', $values['DESC']);
        Configuration::updateValue('XENFORUM_KEYW', $values['KEYW']);
        Configuration::updateValue('XENFORUM_INTRO', $values['HEAD']);
        Configuration::updateValue('XENFORUM_TERM', $values['TERM']);
        Configuration::updateValue('XENFORUM_HELP', $values['HELP']);
    }

    public function hookActionObjectLanguageAddAfter($params)
    {
        return $this->installFixture(
            (int)$params['object']->id,
            Configuration::get('XENFORUM_TITLE', (int)Configuration::get('PS_LANG_DEFAULT')),
            Configuration::get('XENFORUM_DESC', (int)Configuration::get('PS_LANG_DEFAULT')),
            Configuration::get('XENFORUM_KEYW', (int)Configuration::get('PS_LANG_DEFAULT')),
            Configuration::get('XENFORUM_INTRO', (int)Configuration::get('PS_LANG_DEFAULT')),
            Configuration::get('XENFORUM_TERM', (int)Configuration::get('PS_LANG_DEFAULT')),
            Configuration::get('XENFORUM_HELP', (int)Configuration::get('PS_LANG_DEFAULT'))
        );
    }

    /* Add link tab to admin panel */
    private function createAdminTabs()
    {
        $languages = Language::getLanguages(false);

        $tabvalues = array();
        $protab = new Tab();
        $protab->class_name = 'AdminXenForum';
        $protab->module = 'xenforum';
        $protab->id_parent = 0;
        if (version_compare(_PS_VERSION_, '1.7.0.0 ', '>=')) {
            $protab->id_parent = Tab::getIdFromClassName('IMPROVE');
        }

        foreach ($languages as $lang) {
            $protab->name[$lang['id_lang']] = $this->l('Forum');
        }

        $protab->save();
        $tab_id = $protab->id;

        require_once(dirname(__FILE__).'/sql/install_tab.php');

        foreach ($tabvalues as $tab) {
            $newtab = new Tab();
            $newtab->class_name = $tab['class_name'];
            $newtab->id_parent = $tab_id;
            $newtab->module = $this->name;

            foreach ($languages as $lang) {
                $newtab->name[$lang['id_lang']] = $this->l($tab['name']);
            }

            $newtab->save();
        }

        return true;
    }

    public function insertNewHook()
    {
        $hookvalue = array();
        require_once(dirname(__FILE__).'/sql/newhook.php');

        foreach ($hookvalue as $newhook) {
            $hookid = Hook::getIdByName($newhook['name']);
            if (!(int)$hookid) {
                $add_hook = new Hook();
                $add_hook->name = pSQL($newhook['name']);
                $add_hook->title = pSQL($newhook['title']);
                $add_hook->description = pSQL($newhook['description']);
                $add_hook->position = pSQL($newhook['position']);
                $add_hook->live_edit = $newhook['live_edit'];
                $add_hook->add();
                $hookid = $add_hook->id;
                if (!$hookid) {
                    return false;
                }
            } else {
                $up_hook = new Hook($hookid);
                $up_hook->update();
            }
        }
        return true;
    }

    public function uninstall()
    {
        Configuration::deleteByName('XENFORUM_URL');
        Configuration::deleteByName('XENFORUM_PRIVATE');
        Configuration::deleteByName('XENFORUM_SHOW_LCOLUMN');
        Configuration::deleteByName('XENFORUM_SHOW_RCOLUMN');
        Configuration::deleteByName('XENFORUM_USE_DOT_HTML');
        Configuration::deleteByName('XENFORUM_ENABLE_COM');
        Configuration::deleteByName('XENFORUM_LIKE_MYSELF');
        Configuration::deleteByName('XENFORUM_NUM_POST');
        Configuration::deleteByName('XENFORUM_NUM_COM');
        Configuration::deleteByName('XENFORUM_NUM_SIDEBAR');
        Configuration::deleteByName('XENFORUM_NUM_ADMIN_TOP');
        Configuration::deleteByName('XENFORUM_REPORTS_MAX');
        Configuration::deleteByName('XENFORUM_WAIT_APPROVE');
        Configuration::deleteByName('XENFORUM_FB_COMMENT');
        Configuration::deleteByName('XENFORUM_NATIVE_COM_OFF');
        Configuration::deleteByName('XENFORUM_FB_APP_ID');

        Configuration::deleteByName('XENFORUM_TITLE');
        Configuration::deleteByName('XENFORUM_DESC');
        Configuration::deleteByName('XENFORUM_KEYW');
        Configuration::deleteByName('XENFORUM_TERM');
        Configuration::deleteByName('XENFORUM_HELP');

        //Delete admin Tabs
        require_once(dirname(__FILE__).'/sql/uninstall_tab.php');
        require_once(dirname(__FILE__).'/sql/uninstall.php');

        $this->deleteNewHook();

        return parent::uninstall();
    }

    public function deleteNewHook()
    {
        $hookvalue = array();
        require_once(dirname(__FILE__).'/sql/newhook.php');

        foreach ($hookvalue as $newhook) {
            $hookid = Hook::getIdByName($newhook['name']);
            if ((int)$hookid) {
                $dlt_hook = new Hook($hookid);
                $dlt_hook->delete();
            }
        }
    }

    public function hookdisplayHeader()
    {
        $lang_iso_code = $this->context->language->iso_code;
        $this->context->controller->addJquery();
        $this->context->controller->addCSS(($this->_path).'views/css/main.css', 'all');
        $this->context->controller->addCSS(($this->_path).'views/css/style.css', 'all');
        $this->context->controller->addCSS(($this->_path).'views/css/custom.css', 'all');
        
        $this->context->controller->addJS(($this->_path).'libraries/jquery.timeago.min.js');
        $this->context->controller->addJS(($this->_path).'libraries/locales/jquery.timeago.'.$lang_iso_code.'.js');
        $this->context->controller->addJS(($this->_path).'views/js/timeago.config.js');
    }

    public function hookdisplayBackOfficeHeader()
    {
        $lang_iso_code = $this->context->language->iso_code;
        $this->context->controller->addJquery();

        if (Tools::getValue('controller') == 'AdminXenForumTopic') {
            $this->context->controller->addJS(($this->_path).'views/js/xenforum.min.js');
        }

        if (version_compare(_PS_VERSION_, '1.7.0.0 ', '<')) {
            $this->context->controller->addJS(($this->_path).'libraries/jquery.timeago.min.js');
            $this->context->controller->addJS(($this->_path).'libraries/locales/jquery.timeago.'.$lang_iso_code.'.js');
            $this->context->controller->addJS(($this->_path).'views/js/timeago.config.js');

            return $this->display(__FILE__, 'views/templates/admin/addjs.tpl');
        }
    }

    public function hookdisplayBackOfficeTop()
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0 ', '>=')) {
            return null;
        }

        $blog = new XenForumTopic();
        $blogcom = new XenForumPost();
        $user = new XenForumUser();
        $limit = Configuration::get('XENFORUM_NUM_ADMIN_TOP');
        $all_latest = $blogcom->getLatestCom($limit, false);
        $hidden_com = $blogcom->getAllHidden();
        $hidden_post = $blog->getAllHidden();
        $hidden_user = $user->getAllHidden();

        $this->context->smarty->assign(array(
                'xenforum'          => $this,
                'all_latest'        => $all_latest,
                'hidden_com'        => $hidden_com,
                'hidden_post'       => $hidden_post,
                'hidden_user'       => $hidden_user,
                'total_notif'       => (int)$hidden_com + (int)$hidden_post + (int)$hidden_user,
                'link_to_post'      => $this->context->link->getAdminLink('AdminXenForumPost'),
                'link_to_setting'   => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name,
                'link_to_forum'     => $this->getLink(Configuration::get('XENFORUM_URL'))
        ));

        return $this->display(__FILE__, 'views/templates/admin/top_notif.tpl');
    }

    public function hookDisplayLeftColumn()
    {
        return $this->displayLatest();
    }

    public function hookDisplayRightColumn()
    {
        return $this->displayLatest();
    }

    public function hookDisplayFooterProduct()
    {
        $id_product = (int)Tools::getValue('id_product');
        $topic = new XenForumRelated();
        $topics = $topic->getTopics($id_product); // Get topics match with your conditions
        $this->context->smarty->assign(array(
            'xenforum' => $this,
            'topics' => $topics,
        ));

        return $this->display(__file__, '/views/templates/hook/xenforum_productfooter.tpl');
    }

    public function hookDisplayCustomerAccount()
    {
        $id_online = $this->context->customer->id;
        $notif = new XenForumNotification();
        $notif->id_user = $id_online;
        $unread_notif = $notif->getUnread();
        $this->context->smarty->assign(array(
                    'id_online'     => $id_online,
                    'unreads'       => $unread_notif
        ));

        return $this->display(__FILE__, 'views/templates/hook/customeraccount.tpl');
    }

    public function hookDisplayXenForumLeft()
    {
        $disp = '';
        $controller = Tools::getvalue('controller');
        $position = new XenForumPosition();

        $blocks = $position->getBlocks('left', $controller);
        $display = array_map(function ($value) {
            return $value['block'];
        }, $blocks);

        if ($display && !empty($display)) {
            foreach ($display as $block) {
                if (isset($this->displayBlocks[$block])) {
                    $disp .= $this->{$this->displayBlocks[$block]}();
                }
            }
        }
        return $disp;
    }

    public function hookDisplayXenForumRight()
    {
        $disp = '';
        $controller = Tools::getvalue('controller');
        $position = new XenForumPosition();
        $blocks = $position->getBlocks('right', $controller);
        $display = array_map(function ($value) {
            return $value['block'];
        }, $blocks);

        if ($display && !empty($display)) {
            foreach ($display as $block) {
                if (isset($this->displayBlocks[$block])) {
                    $disp .= $this->{$this->displayBlocks[$block]}();
                }
            }
        }

        return $disp;
    }

    public function hookActionCustomerAccountAdd()
    {
        $values = array();
        $bloguser = new XenForumUser();
        $id_customer = (int)$this->context->customer->id;

        if ($id_customer) {
            $firstname      = $this->context->customer->firstname;
            $lastname       = $this->context->customer->lastname;

            $values['id_user'] = $id_customer;
            $values['nickname'] = $firstname.' '.$lastname;
            $values['id_group'] = 1;
            $values['is_staff'] = 0;

            return $bloguser->addUser($values);
        }
    }

    public function displaySearch()
    {
        $key = Tools::getvalue('key');
        $key = str_replace('+', ' ', $key);

        $this->context->smarty->assign(array(
                'key'   => $key,
        ));

        return $this->display(__FILE__, 'views/templates/hook/search.tpl');
    }

    public function displayMyAccount()
    {
        $id_online = $this->context->customer->id;

        if ($id_online) {
            $bloguser = new XenForumUser($id_online);
            $user = $bloguser->getUser();
            $notif = new XenForumNotification();
            $notif->id_user = $id_online;
            $unread_notif = $notif->getUnread();
            $controller = Tools::getvalue('controller');
            if (!empty($user)) {
                $user['title'] = ($user['override_title']) ? $user['override_title'] : $user['title'];
            } else {
                $user = array(
                    'nickname' => '(Unapproved)',
                    'approved' => 0,
                    'style' => '',
                    'title' => ''
                );
            }

            $this->context->smarty->assign(array(
                    'xenforum'      => $this,
                    'id_online'     => $id_online,
                    'unreads'       => $unread_notif,
                    'controller'    => $controller,
                    'currentuser'   => $user
            ));
        }

        return $this->display(__FILE__, 'views/templates/hook/signup.tpl');
    }

    public function displayAddTopic()
    {
        $id_online = $this->context->customer->id;

        if ($id_online) {
            $xfcat = new XenForumCat();
            $id_lang = $this->context->language->id;
            $new_allforums = $xfcat->getCategories($id_lang);

            $this->context->smarty->assign(array(
                    'id_online'     => $id_online,
                    'new_allforums' => $new_allforums,
            ));
        }

        return $this->display(__FILE__, 'views/templates/hook/addtopic.tpl');
    }

    public function displayStats()
    {
        $blog = new XenForumTopic();
        $blogcom = new XenForumPost();
        $bloguser = new XenForumUser();
        $last_id = false;
        $last_name = null;

        $totaltopic = $blog->getToltal();
        $totalcomment = $blogcom->getToltal();
        $latestuser = $bloguser->getLatest();
        if ($latestuser) {
            $last_id = $latestuser['id_customer'];
            $last_name = $latestuser['nickname'];
        }
        $totaluser = $bloguser->getToltal();

        $this->context->smarty->assign(array(
                'xenforum'          => $this,
                'totaluser'         => $totaluser,
                'totaltopic'        => $totaltopic,
                'totalcomment'      => $totalcomment,
                'last_id'           => $last_id,
                'last_name'         => $last_name
        ));

        return $this->display(__FILE__, 'views/templates/hook/stats.tpl');
    }

    public function displayLatest()
    {
        $blogcom = new XenForumPost();
        $limit = Configuration::get('XENFORUM_NUM_SIDEBAR');
        $all_latest = $blogcom->getLatestCom($limit, $this->context->customer->id);

        $this->context->smarty->assign(array(
                'xenforum'      => $this,
                'all_latest'    => $all_latest
        ));

        return $this->display(__FILE__, 'views/templates/hook/latest.tpl');
    }

    public function hookDisplayHome()
    {
        $blogcom = new XenForumPost();
        $limit = Configuration::get('XENFORUM_NUM_SIDEBAR');
        $all_latest = $blogcom->getLatestCom($limit, $this->context->customer->id);

        $this->context->smarty->assign(array(
                'xenforum'      => $this,
                'all_latest'    => $all_latest
        ));

        return $this->display(__FILE__, 'views/templates/hook/home.tpl');
    }

    public static function checkAvatar($type, $id_customer = 0)
    {
        if (!$id_customer) {
            return false;
        }

        $values = false;
        $log_dir = _MODULE_XENFORUM_AVT_DIR_;
        if (!is_dir($log_dir)) {
            if (!mkdir($log_dir, 0777, true)) {
                return false;
            }
        }

        $values = _MODULE_XENFORUM_AVT_DIR_.$id_customer.'.jpg';

        if (file_exists($values)) {
            return _MODULE_XENFORUM_AVT_URI_.$id_customer.'.jpg';
        } else {
            $user = new customer($id_customer);
            $id_gender = (int)$user->id_gender;
            if ($id_gender == 1) {
                $part = 'male_';
            } elseif ($id_gender == 2) {
                $part = 'female_';
            } else {
                $part = '';
            }

            switch ($type) {
                case 'default-small':
                    return _MODULE_XENFORUM_AVT_URI_.'default/avatar_'.$part.'s.png';
                case 'default-medium':
                    return _MODULE_XENFORUM_AVT_URI_.'default/avatar_'.$part.'m.png';
                default:
                    return _MODULE_XENFORUM_AVT_URI_.'default/avatar_'.$part.'m.png';
            }
        }
    }

    public function hookModuleRoutes()
    {
        $alias = Configuration::get('XENFORUM_URL');
        $usehtml = Configuration::get('XENFORUM_USE_DOT_HTML');

        if ($usehtml != 0) {
            $html = '.html';
        } else {
            $html = '';
        }

        $my_link = array(
            $alias => array(
                    'controller' => 'home',
                    'rule' => $alias,
                    'keywords' => array(),
                    'params' => array(
                        'fc' => 'module',
                        'module' => 'xenforum',
                    ),
            ),
            'xenforum_category' => array(
                    'controller' => 'category',
                    'rule' => $alias.'/{id_xenforum_cat}-{slug}',
                    'keywords' => array(
                            'id_xenforum_cat' => array('regexp' => '[0-9\pL]*', 'param' => 'id_xenforum_cat'),
                            'slug' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    ),
                    'params' => array(
                            'fc' => 'module',
                            'module' => 'xenforum',
                    ),
            ),
            'xenforum_category_pagination' => array(
                    'controller' => 'category',
                    'rule' => $alias.'/{id_xenforum_cat}-{slug}/page-{page}',
                    'keywords' => array(
                            'id_xenforum_cat' => array('regexp' => '[0-9\pL]*', 'param' => 'id_xenforum_cat'),
                            'page' => array('regexp' => '[0-9\pL]*', 'param' => 'page'),
                            'slug' => array('regexp' => '[_a-zA-Z0-9-\pL]*')
                    ),
                    'params' => array(
                        'fc' => 'module',
                        'module' => 'xenforum',
                    ),
            ),
            'xenforum_topic_create' => array(
                    'controller' => 'topiccreate',
                    'rule' => $alias.'/{id_xenforum_cat}-{slug}/create_topic',
                    'keywords' => array(
                            'id_xenforum_cat' => array('regexp' => '[0-9\pL]*', 'param' => 'id_xenforum_cat'),
                            'slug' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    ),
                    'params' => array(
                            'fc' => 'module',
                            'module' => 'xenforum',
                    ),
            ),
            'xenforum_member' => array(
                    'controller' => 'member',
                    'rule' => $alias.'/member{/:id_customer}',
                    'keywords' => array(
                            'id_customer' => array('regexp' => '[a-zA-Z0-9\pL\+]*', 'param' => 'id_customer')
                    ),
                    'params' => array(
                        'fc' => 'module',
                        'module' => 'xenforum',
                    ),
            ),
            'xenforum_tags' => array(
                    'controller' => 'tags',
                    'rule' => $alias.'/tags/{tags}'.$html,
                    'keywords' => array(
                            'tags' => array('regexp' => '.*', 'param' => 'tags')
                    ),
                    'params' => array(
                        'fc' => 'module',
                        'module' => 'xenforum',
                    ),
            ),
            'xenforum_tags_pagination' => array(
                    'controller' => 'tags',
                    'rule' => $alias.'/tags/{tags}/page-{page}'.$html,
                    'keywords' => array(
                            'tags' => array('regexp' => '.*', 'param' => 'tags'),
                            'page' => array('regexp' => '[0-9\pL]*', 'param' => 'page')
                    ),
                    'params' => array(
                        'fc' => 'module',
                        'module' => 'xenforum',
                    ),
            ),
            'xenforum_search' => array(
                    'controller' => 'search',
                    'rule' => $alias.'/search/{key}',
                    'keywords' => array(
                            'key' => array('regexp' => '[_a-zA-Z0-9-\pL\+]*', 'param' => 'key')
                    ),
                    'params' => array(
                        'fc' => 'module',
                        'module' => 'xenforum',
                    ),
            ),
            'xenforum_search_pagination' => array(
                    'controller' => 'search',
                    'rule' => $alias.'/search/{key}/page-{page}'.$html,
                    'keywords' => array(
                            'key' => array('regexp' => '[_a-zA-Z0-9-\pL\+]*', 'param' => 'key'),
                            'page' => array('regexp' => '[0-9\pL]*', 'param' => 'page')
                    ),
                    'params' => array(
                        'fc' => 'module',
                        'module' => 'xenforum',
                    ),
            ),
            'xenforum_viewdetails' => array(
                    'controller' => 'details',
                    'rule' => $alias.'/topic/{id_xenforum}-{slug}'.$html,
                    'keywords' => array(
                            'id_xenforum' => array('regexp' => '[0-9\pL]*', 'param' => 'id_xenforum'),
                            'slug' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    ),
                    'params' => array(
                            'fc' => 'module',
                            'module' => 'xenforum',
                    ),
            ),
            'xenforum_post_pagination' => array(
                    'controller' => 'details',
                    'rule' => $alias.'/topic/{id_xenforum}-{slug}/page-{page}'.$html,
                    'keywords' => array(
                            'id_xenforum' => array('regexp' => '[0-9\pL]*', 'param' => 'id_xenforum'),
                            'page' => array('regexp' => '[0-9\pL]*', 'param' => 'page'),
                            'slug' => array('regexp' => '[_a-zA-Z0-9-\pL]*')
                    ),
                    'params' => array(
                        'fc' => 'module',
                        'module' => 'xenforum',
                    ),
            ),
            'xenforum_topic_edit' => array(
                    'controller' => 'topicedit',
                    'rule' => $alias.'/topic/{id_xenforum}/edit',
                    'keywords' => array(
                            'id_xenforum' => array('regexp' => '[0-9\pL]*', 'param' => 'id_xenforum'),
                    ),
                    'params' => array(
                            'fc' => 'module',
                            'module' => 'xenforum',
                    ),
            ),
            'xenforum_topic_delete' => array(
                    'controller' => 'topicdelete',
                    'rule' => $alias.'/topic/{id_xenforum}/delete',
                    'keywords' => array(
                            'id_xenforum' => array('regexp' => '[0-9\pL]*', 'param' => 'id_xenforum'),
                            'slug' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    ),
                    'params' => array(
                            'fc' => 'module',
                            'module' => 'xenforum',
                    ),
            ),
            'xenforum_editpost' => array(
                    'controller' => 'editpost',
                    'rule' => $alias.'/post/{id}/edit',
                    'keywords' => array(
                            'id' => array('regexp' => '[0-9\pL]*', 'param' => 'id'),
                    ),
                    'params' => array(
                            'fc' => 'module',
                            'module' => 'xenforum',
                    ),
            ),
            'xenforum_ajax_handle' => array(
                    'controller' => 'handleajax',
                    'rule' => $alias.'/post/{id}/handle',
                    'keywords' => array(
                            'id' => array('regexp' => '[0-9\pL]*', 'param' => 'id'),
                    ),
                    'params' => array(
                            'fc' => 'module',
                            'module' => 'xenforum',
                    ),
            ),
            'xenforum_term' => array(
                    'controller' => 'term',
                    'rule' => $alias.'/term',
                    'keywords' => array(),
                    'params' => array(
                            'fc' => 'module',
                            'module' => 'xenforum',
                    ),
            ),
            'xenforum_help' => array(
                    'controller' => 'help',
                    'rule' => $alias.'/help',
                    'keywords' => array(),
                    'params' => array(
                            'fc' => 'module',
                            'module' => 'xenforum',
                    ),
            )
        );
        return $my_link;
    }

    public static function getUrl($id_lang = null)
    {
        $ssl = Configuration::get('PS_SSL_ENABLED');
        if ($id_lang == null) {
            $id_lang = (int)Context::getContext()->language->id;
        }

        $id_shop = (int)Context::getContext()->shop->id;
        $rewrite_set = (int)Configuration::get('PS_REWRITING_SETTINGS');

        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && $id_shop !== null) {
            $shop = new Shop($id_shop);
        } else {
            $shop = Context::getContext()->shop;
        }

        $base = (($ssl) ? 'https://'.$shop->domain_ssl : 'http://'.$shop->domain);
        $lang_url = Language::getIsoById($id_lang).'/';

        if ((!$rewrite_set && in_array($id_shop, array((int)Context::getContext()->shop->id, null)))
        || !Language::isMultiLanguageActivated($id_shop)
        || !(int)Configuration::get('PS_REWRITING_SETTINGS', null, null, $id_shop)) {
            $lang_url = '';
        }

        return $base.$shop->getBaseURI().$lang_url;
    }

    public static function getLink($rewrite = 'new-page', $params = null, $id_lang = null)
    {
        $url = xenforum::GetUrl($id_lang);
        $dispatcher = Dispatcher::getInstance();
        if ($params != null) {
            return $url.$dispatcher->createUrl($rewrite, $id_lang, $params);
        } else {
            return $url.$dispatcher->createUrl($rewrite);
        }
    }

    public static function xfAddSlashes($text)
    {
        $text = addslashes(trim($text));
        return $text;
    }

    public static function xfStripslashes($text)
    {
        $text = Tools::stripslashes($text);
        return $text;
    }

    public static function removeQuote($html, $all = false)
    {
        if ($all) {
            $html = preg_replace('/\[quote(.*?)\](.*?)\[\/quote\]/ism', '', $html);
        } else {
            $html = preg_replace('/\[quote(.*?)\](.*?)\[\/quote\]/ism', '[QUOTE]', $html);
        }

        return $html;
    }

    public function bbcodeParser($bbcode)
    {
        $match = $replace = array();
        /* Replace "special character" with it's unicode equivilant */
        $match['special'] = '/\�/s';
        $replace['special'] = '&#65533;';

        /* Quotes */
        $match['quote'] = '/\[quote\](.*?)\[\/quote\]/ism';
        $replace['quote'] = <<<EOD
        <div class="bbCodeBlock">
        <aside>
        <blockquote class="quoteContainer">
        <div class="quote">$1</div>
        </blockquote>
        </aside>
        </div>
EOD;

        $match['quote2'] = '/\[quote=(.*?)\](.*?)\[\/quote\]/ism';
        $replace['quote2'] = '
            <div class="bbCodeBlock">
            <aside><div class="attribution type">$1 '.$this->l('said').':</div>
            <blockquote class="quoteContainer">
            <div class="quote">$2</div>
            </blockquote>
            </aside>
            </div>';

        /* Parse */
        $bbcode = preg_replace($match, $replace, $bbcode);
        $bbcode = preg_replace_callback('/\[code\](.*?)\[\/code\]/ism', array($this, 'preSpecial'), $bbcode);

        return $bbcode;
    }

    /* Code blocks - Need to specially remove breaks */
    private function preSpecial($matches)
    {
        $prep = preg_replace('/\<br \/\>/', '', $matches[1]);
        return '<pre>'.$prep.'</pre>';
    }

    protected function postProcess()
    {
        if (Tools::isSubmit('submitXFModule')) {
            $languages = Language::getLanguages(false);
            $values = array();

            foreach ($languages as $lang) {
                $values['XENFORUM_TITLE'][$lang['id_lang']] = Tools::getValue('XENFORUM_TITLE_'.$lang['id_lang']);
                $values['XENFORUM_DESC'][$lang['id_lang']] = Tools::getValue('XENFORUM_DESC_'.$lang['id_lang']);
                $values['XENFORUM_KEYW'][$lang['id_lang']] = Tools::getValue('XENFORUM_KEYW_'.$lang['id_lang']);
                $values['XENFORUM_INTRO'][$lang['id_lang']] = htmlentities(Tools::getValue('XENFORUM_INTRO_'.$lang['id_lang']));
                $values['XENFORUM_HELP'][$lang['id_lang']] = htmlentities(Tools::getValue('XENFORUM_HELP_'.$lang['id_lang']));
                $values['XENFORUM_TERM'][$lang['id_lang']] = htmlentities(Tools::getValue('XENFORUM_TERM_'.$lang['id_lang']));
                
                if (!Tools::getValue('XENFORUM_TITLE_'.$lang['id_lang'])) {
                    $this->errors[] = sprintf(Tools::displayError('The %s field in %s is required!'), '"'.$this->l('Page Title').'"', $lang['name']);
                }
            }

            if (!Tools::getValue('XENFORUM_URL')) {
                $this->errors[] = sprintf(Tools::displayError('The %s field is required!'), '"'.$this->l('Main URL').'"');
            }
            if (!Validate::isUnsignedId(Tools::getValue('XENFORUM_NUM_POST')) || Tools::getValue('XENFORUM_NUM_POST') == 0) {
                $this->errors[] = sprintf(Tools::displayError('The %s field is invalid!'), '"'.$this->l('Numbers of post per page').'"');
            }
            if (!Validate::isUnsignedId(Tools::getValue('XENFORUM_NUM_COM')) || Tools::getValue('XENFORUM_NUM_COM') == 0) {
                $this->errors[] = sprintf(Tools::displayError('The %s field is invalid!'), '"'.$this->l('Numbers of mesages per page').'"');
            }
            if (!Validate::isUnsignedId(Tools::getValue('XENFORUM_NUM_SIDEBAR')) || Tools::getValue('XENFORUM_NUM_SIDEBAR') == 0) {
                $this->errors[] = sprintf(Tools::displayError('The %s field is invalid!'), '"'.$this->l('Numbers of mesages on sidebar').'"');
            }
            if (!Validate::isUnsignedId(Tools::getValue('XENFORUM_NUM_ADMIN_TOP')) || Tools::getValue('XENFORUM_NUM_ADMIN_TOP') == 0) {
                $this->errors[] = sprintf(Tools::displayError('The %s field is invalid!'), '"'.$this->l('Numbers of mesages on notifications').'"');
            }
            if (!Validate::isUnsignedId(Tools::getValue('XENFORUM_REPORTS_MAX')) || Tools::getValue('XENFORUM_REPORTS_MAX') == 0) {
                $this->errors[] = sprintf(Tools::displayError('The %s field is invalid!'), '"'.$this->l('Max of reports to hide messages').'"');
            }

            if (!empty($this->errors)) {
                return $this->displayError(implode('<br/>', $this->errors));
            } else {
                Configuration::updateValue('XENFORUM_TITLE', $values['XENFORUM_TITLE']);
                Configuration::updateValue('XENFORUM_DESC', $values['XENFORUM_DESC']);
                Configuration::updateValue('XENFORUM_KEYW', $values['XENFORUM_KEYW']);
                Configuration::updateValue('XENFORUM_INTRO', $values['XENFORUM_INTRO']);
                Configuration::updateValue('XENFORUM_TERM', $values['XENFORUM_TERM']);
                Configuration::updateValue('XENFORUM_HELP', $values['XENFORUM_HELP']);

                Configuration::updateValue('XENFORUM_URL', Tools::getValue('XENFORUM_URL'));
                Configuration::updateValue('XENFORUM_PRIVATE', Tools::getValue('XENFORUM_PRIVATE'));
                Configuration::updateValue('XENFORUM_SHOW_LCOLUMN', Tools::getValue('XENFORUM_SHOW_LCOLUMN'));
                Configuration::updateValue('XENFORUM_SHOW_RCOLUMN', Tools::getValue('XENFORUM_SHOW_RCOLUMN'));
                Configuration::updateValue('XENFORUM_USE_DOT_HTML', Tools::getValue('XENFORUM_USE_DOT_HTML'));
                Configuration::updateValue('XENFORUM_ENABLE_COM', Tools::getValue('XENFORUM_ENABLE_COM'));
                Configuration::updateValue('XENFORUM_LIKE_MYSELF', Tools::getValue('XENFORUM_LIKE_MYSELF'));
                Configuration::updateValue('XENFORUM_NUM_POST', Tools::getValue('XENFORUM_NUM_POST'));
                Configuration::updateValue('XENFORUM_NUM_COM', Tools::getValue('XENFORUM_NUM_COM'));
                Configuration::updateValue('XENFORUM_NUM_SIDEBAR', Tools::getValue('XENFORUM_NUM_SIDEBAR'));
                Configuration::updateValue('XENFORUM_NUM_ADMIN_TOP', Tools::getValue('XENFORUM_NUM_ADMIN_TOP'));
                Configuration::updateValue('XENFORUM_REPORTS_MAX', Tools::getValue('XENFORUM_REPORTS_MAX'));
                Configuration::updateValue('XENFORUM_WAIT_APPROVE', Tools::getValue('XENFORUM_WAIT_APPROVE'));

                return $this->displayConfirmation($this->l('The settings have been updated.'));
            }

            /* Tools::redirectAdmin(
                $this->context->link->getAdminLink('AdminModules', false)
                .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name
                .'&conf=4&token='.Tools::getAdminTokenLite('AdminModules')
            ); */
        } elseif (Tools::isSubmit('submitXFRModuleB')) {
            Configuration::updateValue('XENFORUM_FB_COMMENT', Tools::getValue('XENFORUM_FB_COMMENT'));
            Configuration::updateValue('XENFORUM_NATIVE_COM_OFF', Tools::getValue('XENFORUM_NATIVE_COM_OFF'));
            Configuration::updateValue('XENFORUM_FB_APP_ID', Tools::getValue('XENFORUM_FB_APP_ID'));

            return Tools::redirectAdmin(
                $this->context->link->getAdminLink('AdminModules', true)
                .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&conf=4'
            );

            //return $this->displayConfirmation($this->l('The settings have been updated.'));
        }
    }
    public function getContent()
    {
        return $this->postProcess().$this->displayInfos().$this->renderForm().$this->renderFormB();
    }

    private function displayInfos()
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0 ', '>=')) {
            $link_to_blocktopmenu = $this->context->link->getAdminLink('AdminModules').'&configure=ps_mainmenu';
        } else {
            $link_to_blocktopmenu = $this->context->link->getAdminLink('AdminModules').'&configure=blocktopmenu';
        }
        $this->context->smarty->assign(array(
                'isconfig' => 1,
                'link_to_setting' => $this->context->link->getAdminLink('AdminXenForumCategory'),
                'link_to_blocktopmenu' => $link_to_blocktopmenu,
                'settings_url' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name,
                'tree_url' => $this->context->link->getAdminLink('AdminXenForumCategory'),
                'topic_url' => $this->context->link->getAdminLink('AdminXenForumTopic'),
                'comment_url' => $this->context->link->getAdminLink('AdminXenForumPost'),
                'user_url' => $this->context->link->getAdminLink('AdminXenForumUser'),
                'permission_url' => $this->context->link->getAdminLink('AdminXenForumGroup'),
                'position_url' => $this->context->link->getAdminLink('AdminXenForumPosition'),
        ));

        return $this->display(__FILE__, '/views/templates/admin/infos.tpl');
    }

    protected function renderForm()
    {
        $type_radio = 'radio';
        if (version_compare(_PS_VERSION_, '1.6.0.0 ', '>=')) {
            $type_radio = 'switch';
        }

        $languages = Language::getLanguages(false);
        $links = '';
        $values = array();

        foreach ($languages as $lang) {
            $page_url = $this->getLink(Configuration::get('XENFORUM_URL'), null, $lang['id_lang']);
            $values[] = '<a href="'.$page_url.'" target=_blank >'.$page_url.'</a>';
        }

        if (count($values)) {
            $links = implode('<br />', $values);
        }

        $fields_form = array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Page Title'),
                        'name' => 'XENFORUM_TITLE',
                        'required' => true,
                        'size' => 50,
                        'lang' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Meta Keywords'),
                        'name' => 'XENFORUM_KEYW',
                        'size' => 50,
                        'lang' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Meta Descripton'),
                        'name' => 'XENFORUM_DESC',
                        'size' => 50,
                        'lang' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Main URL'),
                        'name' => 'XENFORUM_URL',
                        'required' => true,
                        'size' => 50,
                        'desc'=> '<p class="alert alert-info">'.$links.'</p>'
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Forums intro'),
                        'name' => 'XENFORUM_INTRO',
                        'class' => 'rte',
                        'autoload_rte' => true,
                        'lang' => true
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Terms and Conditions'),
                        'name' => 'XENFORUM_TERM',
                        'class' => 'rte',
                        'autoload_rte' => true,
                        'lang' => true,
                        'desc'=> 'Terms and Conditions, Rules...'
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Helps'),
                        'name' => 'XENFORUM_HELP',
                        'class' => 'rte',
                        'autoload_rte' => true,
                        'lang' => true
                    ),
                    array(
                        'type' => $type_radio,
                        'label' => $this->l('Private forums'),
                        'name' => 'XENFORUM_PRIVATE',
                        'is_bool' => true,
                        'class' => 't',
                        'values' => array(
                                    array(
                                        'id' => 'left_on',
                                        'value' => true,
                                        'label' => $this->l('Enabled')
                                    ),
                                    array(
                                        'id' => 'left_off',
                                        'value' => false,
                                        'label' => $this->l('Disabled')
                                    )
                        ),
                        'desc' => $this->l('Must have permissions to access')
                    ),
                    array(
                        'type' => $type_radio,
                        'label' => $this->l('Wait for approved (*NEW)'),
                        'name' => 'XENFORUM_WAIT_APPROVE',
                        'is_bool' => true,
                        'class' => 't',
                        'values' => array(
                                    array(
                                        'id' => 'wait_on',
                                        'value' => true,
                                        'label' => $this->l('Enabled')
                                    ),
                                    array(
                                        'id' => 'wait_off',
                                        'value' => false,
                                        'label' => $this->l('Disabled')
                                    )
                        ),
                        'desc' => $this->l('A customer want to be a forum member need approved by admin.')
                    ),
                    array(
                        'type' => $type_radio,
                        'label' => $this->l('Override left column'),
                        'name' => 'XENFORUM_SHOW_LCOLUMN',
                        'is_bool' => true,
                        'class' => 't',
                        'values' => array(
                                    array(
                                        'id' => 'left_on',
                                        'value' => true,
                                        'label' => $this->l('Yes')
                                    ),
                                    array(
                                        'id' => 'left_off',
                                        'value' => false,
                                        'label' => $this->l('No')
                                    )
                        ),
                    ),
                    array(
                        'type' => $type_radio,
                        'label' => $this->l('Override right column'),
                        'name' => 'XENFORUM_SHOW_RCOLUMN',
                        'is_bool' => true,
                        'class' => 't',
                        'values' => array(
                                    array(
                                        'id' => 'left_on',
                                        'value' => true,
                                        'label' => $this->l('Yes')
                                    ),
                                    array(
                                        'id' => 'right_off',
                                        'value' => false,
                                        'label' => $this->l('No')
                                    )
                        ),
                        'desc' => $this->l('You also have to active left/right-column in your themes')
                    ),
                    array(
                        'type' => $type_radio,
                        'label' => $this->l('Use friendly URL with .html'),
                        'name' => 'XENFORUM_USE_DOT_HTML',
                        'is_bool' => true,
                        'class' => 't',
                        'values' => array(
                                    array(
                                        'id' => 'html_on',
                                        'value' => true,
                                        'label' => $this->l('Enabled')
                                    ),
                                    array(
                                        'id' => 'html_off',
                                        'value' => false,
                                        'label' => $this->l('Disabled')
                                    )
                        ),
                    ),
                    array(
                        'type' => $type_radio,
                        'label' => $this->l('Enable comment'),
                        'name' => 'XENFORUM_ENABLE_COM',
                        'is_bool' => true,
                        'class' => 't',
                        'values' => array(
                                    array(
                                        'id' => 'enable_on',
                                        'value' => true,
                                        'label' => $this->l('Enabled')
                                    ),
                                    array(
                                        'id' => 'enable_off',
                                        'value' => false,
                                        'label' => $this->l('Disabled')
                                    )
                        ),
                    ),
                    array(
                        'type' => $type_radio,
                        'label' => $this->l('Like my own posts'),
                        'name' => 'XENFORUM_LIKE_MYSELF',
                        'is_bool' => true,
                        'class' => 't',
                        'values' => array(
                                    array(
                                        'id' => 'like_on',
                                        'value' => true,
                                        'label' => $this->l('Enabled')
                                    ),
                                    array(
                                        'id' => 'like_off',
                                        'value' => false,
                                        'label' => $this->l('Disabled')
                                    )
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Numbers of post per page'),
                        'name' => 'XENFORUM_NUM_POST',
                        'required' => true,
                        'size' => 50
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Numbers of mesages per page'),
                        'name' => 'XENFORUM_NUM_COM',
                        'required' => true,
                        'size' => 50
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Numbers of mesages on sidebar'),
                        'name' => 'XENFORUM_NUM_SIDEBAR',
                        'required' => true,
                        'size' => 50
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Numbers of mesages on notifications'),
                        'name' => 'XENFORUM_NUM_ADMIN_TOP',
                        'required' => true,
                        'size' => 50,
                        'desc' => $this->l('Show on notifications bar in backoffice page.')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Max of reports to hide messages'),
                        'name' => 'XENFORUM_REPORTS_MAX',
                        'required' => true,
                        'size' => 50,
                        'desc' => $this->l('Automatically hide messages if number of reported exceeds the allowed limit. Leave blank to disable this feature.')
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitXFModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($fields_form));
    }

    protected function getConfigFormValues()
    {
        $languages = Language::getLanguages(false);
        $fields = array();

        foreach ($languages as $lang) {
            $fields['XENFORUM_TITLE'][$lang['id_lang']] = Tools::getValue(
                'XENFORUM_TITLE_'.$lang['id_lang'],
                Configuration::get('XENFORUM_TITLE', $lang['id_lang'])
            );

            $fields['XENFORUM_DESC'][$lang['id_lang']] = Tools::getValue(
                'XENFORUM_DESC_'.$lang['id_lang'],
                Configuration::get('XENFORUM_DESC', $lang['id_lang'])
            );

            $fields['XENFORUM_KEYW'][$lang['id_lang']] = Tools::getValue(
                'XENFORUM_KEYW_'.$lang['id_lang'],
                Configuration::get('XENFORUM_KEYW', $lang['id_lang'])
            );

            $fields['XENFORUM_INTRO'][$lang['id_lang']] = Tools::getValue(
                'XENFORUM_INTRO_'.$lang['id_lang'],
                html_entity_decode(Configuration::get('XENFORUM_INTRO', $lang['id_lang']))
            );

            $fields['XENFORUM_TERM'][$lang['id_lang']] = Tools::getValue(
                'XENFORUM_TERM_'.$lang['id_lang'],
                html_entity_decode(Configuration::get('XENFORUM_TERM', $lang['id_lang']))
            );

            $fields['XENFORUM_HELP'][$lang['id_lang']] = Tools::getValue(
                'XENFORUM_HELP_'.$lang['id_lang'],
                html_entity_decode(Configuration::get('XENFORUM_HELP', $lang['id_lang']))
            );
        }

        $fields['XENFORUM_URL'] = Tools::getValue('XENFORUM_URL', Configuration::get('XENFORUM_URL'));
        $fields['XENFORUM_PRIVATE'] = Tools::getValue('XENFORUM_PRIVATE', Configuration::get('XENFORUM_PRIVATE'));
        $fields['XENFORUM_SHOW_LCOLUMN'] = Tools::getValue('XENFORUM_SHOW_LCOLUMN', Configuration::get('XENFORUM_SHOW_LCOLUMN'));
        $fields['XENFORUM_SHOW_RCOLUMN'] = Tools::getValue('XENFORUM_SHOW_RCOLUMN', Configuration::get('XENFORUM_SHOW_RCOLUMN'));
        $fields['XENFORUM_USE_DOT_HTML'] = Tools::getValue('XENFORUM_USE_DOT_HTML', Configuration::get('XENFORUM_USE_DOT_HTML'));
        $fields['XENFORUM_LIKE_MYSELF'] = Tools::getValue('XENFORUM_LIKE_MYSELF', Configuration::get('XENFORUM_LIKE_MYSELF'));
        $fields['XENFORUM_ENABLE_COM'] = Tools::getValue('XENFORUM_ENABLE_COM', Configuration::get('XENFORUM_ENABLE_COM'));
        $fields['XENFORUM_NUM_POST'] = Tools::getValue('XENFORUM_NUM_POST', Configuration::get('XENFORUM_NUM_POST'));
        $fields['XENFORUM_NUM_COM'] = Tools::getValue('XENFORUM_NUM_COM', Configuration::get('XENFORUM_NUM_COM'));
        $fields['XENFORUM_NUM_SIDEBAR'] = Tools::getValue('XENFORUM_NUM_SIDEBAR', Configuration::get('XENFORUM_NUM_SIDEBAR'));
        $fields['XENFORUM_NUM_ADMIN_TOP'] = Tools::getValue('XENFORUM_NUM_ADMIN_TOP', Configuration::get('XENFORUM_NUM_ADMIN_TOP'));
        $fields['XENFORUM_REPORTS_MAX'] = Tools::getValue('XENFORUM_REPORTS_MAX', Configuration::get('XENFORUM_REPORTS_MAX'));
        $fields['XENFORUM_WAIT_APPROVE'] = Tools::getValue('XENFORUM_WAIT_APPROVE', Configuration::get('XENFORUM_WAIT_APPROVE'));

        return $fields;
    }
    

    protected function renderFormB()
    {
        $type_radio = 'radio';
        if (version_compare(_PS_VERSION_, '1.6.0.0 ', '>=')) {
            $type_radio = 'switch';
        }

        $fields_form = array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Facebook Comment Settings'),
                'icon' => 'icon-cogs',
                ),
                'description' => $this->l('To active FB comment and Get Facebook App ID:').'<br/>'
                .$this->l('Register or Login » My Apps » Add a New App » Website(www) » Setup and Active.')
                .' <a href="https://developers.facebook.com" target="_blank">https://developers.facebook.com</a><br/>'
                .$this->l('About FB Comment:')
                .' <a href="https://developers.facebook.com/docs/plugins/comments" target="_blank">https://developers.facebook.com/docs/plugins/comments</a><br/>',
                'input' => array(
                    array(
                        'type' => $type_radio,
                        'label' => $this->l('Facebook Comment'),
                        'name' => 'XENFORUM_FB_COMMENT',
                        'is_bool' => true,
                        'class' => 't',
                        'values' => array(
                                    array(
                                        'id' => 'fb_on',
                                        'value' => true,
                                        'label' => $this->l('Enabled')
                                    ),
                                    array(
                                        'id' => 'fb_off',
                                        'value' => false,
                                        'label' => $this->l('Disabled')
                                    )
                        ),
                    ),
                    array(
                        'type' => $type_radio,
                        'label' => $this->l('Disable native comments'),
                        'name' => 'XENFORUM_NATIVE_COM_OFF',
                        'is_bool' => true,
                        'class' => 't',
                        'values' => array(
                                    array(
                                        'id' => 'native_on',
                                        'value' => true,
                                        'label' => $this->l('Enabled')
                                    ),
                                    array(
                                        'id' => 'native_off',
                                        'value' => false,
                                        'label' => $this->l('Disabled')
                                    )
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Facebook App ID'),
                        'name' => 'XENFORUM_FB_APP_ID',
                        'size' => 50
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitXFRModuleB';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValuesB(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($fields_form));
    }

    protected function getConfigFormValuesB()
    {
        $fields = array();

        $fields['XENFORUM_FB_COMMENT'] = Tools::getValue('XENFORUM_FB_COMMENT', Configuration::get('XENFORUM_FB_COMMENT'));
        $fields['XENFORUM_NATIVE_COM_OFF'] = Tools::getValue('XENFORUM_NATIVE_COM_OFF', Configuration::get('XENFORUM_NATIVE_COM_OFF'));
        $fields['XENFORUM_FB_APP_ID'] = Tools::getValue('XENFORUM_FB_APP_ID', Configuration::get('XENFORUM_FB_APP_ID'));

        return $fields;
    }
}
