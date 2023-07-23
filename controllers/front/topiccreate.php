<?php
/**
* Prestashop Addons | Module by: <App1Pro>
*
* @author    Chuyen Nguyen [App1Pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

include_once(dirname(__FILE__).'/../../classes/controllers/FrontController.php');
class XenForumTopicCreateModuleFrontController extends XenForumModuleFrontController
{
    public $ssl = true;
    public $saved = false;

    public function setMedia()
    {
        parent::setMedia();
//        $this->addJS(_MODULE_DIR_.$this->module->name.'/libraries/tinymce/tinymce.min.js');
        if (version_compare(_PS_VERSION_, '1.7.0.0 ', '>=')) {
            $this->registerJavascript(sha1('js/tools.js'), 'js/tools.js');
            $this->registerJavascript(null, '//cdn.tinymce.com/4/tinymce.min.js', array('server' => 'remote'));
        } else {
            $this->addJS('//cdn.tinymce.com/4/tinymce.min.js');
        }
        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/post_form.js');
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $xf_cat = new XenForumCat();
        $id_xenforum_cat = Tools::getValue('id_xenforum_cat');
        $nested_categories = $xf_cat->getCategoriesById(array(), $id_xenforum_cat);
        $nested_categories = array_reverse($nested_categories);
        foreach ($nested_categories as $category) {
            $breadcrumb['links'][] = array(
                'title' => $category['meta_title'],
                'url' => $this->module->GetLink('xenforum_category', array(
                    'id_xenforum_cat' => $category['id_xenforum_cat'],
                    'slug' => $category['link_rewrite']
                )),
            );
        }

        $breadcrumb['links'][] = array(
            'title' => $this->l('Create new topic'),
            'url' => null,
        );

        return $breadcrumb;
    }

    public function postProcess()
    {
        $user = new XenForumUser();
        $xftopic = new XenForumTopic();
        $xfpost = new XenForumPost();
        $cookie = Context::getContext()->cookie;
        $id_xenforum_cat = (int)Tools::getvalue('id_xenforum_cat');
        $this->className = 'XenForumTopic';
        $this->table = 'xenforum';
        
        if ($this->context->customer->id) {
            $id_author = $this->context->customer->id;
        }

        // Add a new post
        if ((Tools::isSubmit('edit_post')) && isset($id_author) && isset($cookie->token) && ($cookie->token == Tools::getValue('token'))) {
            $xftopic->meta_title = Tools::getValue('meta_title');
            $xfpost->comment = Tools::getValue('comment');
            $xftopic->tags = Tools::getValue('tags');
            $xftopic->products = Tools::getValue('products');
            $xftopic->link_rewrite = strip_tags(trim(Tools::getValue('link_rewrite')));
            $xftopic->closed = Tools::getValue('closed');
            $xftopic->highlight = Tools::getValue('highlight');
            $xfpost->attachments = Tools::getValue('attachments');
            $xftopic->id_xenforum_cat = $id_xenforum_cat;
            $xftopic->id_author = $id_author;
            $xftopic->active = 0;
            
            $this->checkValidation();
            $this->className = 'XenForumPost';
            $this->table = 'xenforum_post';
            $this->checkValidation();

            if (!empty($this->errors)) {
                return false;
            }

            if ($user->checkPerm($id_author, '_auto_approve_topic_')) {
                $xftopic->active = 1;
            }

            if ($user->checkPerm($id_author, '_auto_approve_comment_')) {
                $xfpost->active = 1;
            }

            if ($xftopic->add()) {
                $xfpost->id_xenforum = $xftopic->id;
                $xfpost->id_author = $id_author;
                $xfpost->add();
                $this->saved = true;

                $var_list = array(
                    '{admin}'       => 'Administrator',
                    '{topic}'       => $xftopic->meta_title,
                    '{author}'      => $this->context->customer->firstname.' '.$this->context->customer->lastname
                );

                $this->sendNewTopicMail('New topic was created: ', $var_list);

                $options = array(
                    'id_xenforum'   => $xftopic->id,
                    'slug'          => $xftopic->link_rewrite
                );
                $already_sent = addcslashes(xenforum::getLink('xenforum_viewdetails', $options), "'");

                $this->context->smarty->assign('alreadySent', $already_sent);
            } else {
                $this->context->smarty->assign('error', 'error when save');
            }
        }
    }
    
    public function initContent()
    {
        parent::initContent();
        $blogcat = new XenForumCat();
        $user = new XenForumUser();
        $cookie = Context::getContext()->cookie;
        $token = Tools::passwdGen(12);
        $id_xenforum_cat = (int)Tools::getvalue('id_xenforum_cat');
        // Check permission on access this topic
        if ($this->private || !$user->checkCatAccess($this->context->customer->id, $id_xenforum_cat)) {
            if (version_compare(_PS_VERSION_, '1.7.0.0 ', '>=')) {
                $this->setTemplate('module:xenforum/views/templates/front/ps17-private_msg.tpl');
            } else {
                $this->setTemplate('private_msg.tpl');
            }
            return;
        }

        $cookie->__set('token', $token);
        $this->context->smarty->assign('token', $token);

        if ((int)$id_xenforum_cat) {
            $categoryinfo = $blogcat->getCategoryById($id_xenforum_cat);
            $nested_categories = $blogcat->getCategoriesById(array(), $id_xenforum_cat);
            $nested_categories = array_reverse($nested_categories);
            //$category_status = $categoryinfo['active'];

            $this->context->smarty->assign(array(
                'category_title'    => $categoryinfo['meta_title'],
                'cat_link_rewrite'  => $categoryinfo['link_rewrite'],
                'description'       => $categoryinfo['description'],
                'closed'            => $categoryinfo['closed'],
                'nested_categories' => $nested_categories,
                'id_xenforum_cat'   => $id_xenforum_cat,
                'errors'            => $this->errors,
            ));
        }

        $retrieve_meta_title = '';
        $retrieve_comment = '';
        $retrieve_link_rewrite = '';
        $retrieve_tags = '';
        $retrieve_attachments = array();
        if (Tools::isSubmit('edit_post') && !$this->saved) {
            $retrieve_meta_title = Tools::getValue('meta_title', '');
            $retrieve_comment = Tools::getValue('comment', '');
            $attachments = Tools::getValue('attachments', null);
            $retrieve_link_rewrite = Tools::getValue('link_rewrite', '');
            $retrieve_tags = Tools::getValue('tags', '');
            if (!empty($attachments)) {
                $retrieve_attachments = XenForumAttachment::getListAttachments($attachments);
            }
        }
        // Output variables for default of retrieve error
        $this->context->smarty->assign(array(
            'title'         => $retrieve_meta_title,
            'comment'       => $retrieve_comment,
            'link_rewrite'  => $retrieve_link_rewrite,
            'tags'          => $retrieve_tags,
            'attachments'   => $retrieve_attachments
        ));

        if ((int)$id_xenforum_cat) {
            if (version_compare(_PS_VERSION_, '1.7.0.0 ', '>=')) {
                $this->setTemplate('module:xenforum/views/templates/front/ps17-topic_create.tpl');
            } else {
                $this->setTemplate('topic_create.tpl');
            }
        }
    }

    private function sendNewTopicMail($title = null, $var_list = null)
    {
        $id_lang = $this->context->language->id;
        $email_to = Configuration::get('PS_SHOP_EMAIL');

        if (Validate::isEmail($email_to)) {
            Mail::Send(
                (int)$id_lang,
                'new_topic',
                Mail::l($title, (int)$id_lang).$var_list['{topic}'],
                $var_list,
                $email_to,
                null,
                null,
                null,
                null,
                null,
                dirname(__FILE__).'/../../mails/'
            );
        }
    }
}
