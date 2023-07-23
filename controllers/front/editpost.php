<?php
/**
* Prestashop Addons | Module by: <App1Pro>
*
* @author    Chuyen Nguyen [App1Pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

include_once(dirname(__FILE__).'/../../classes/controllers/FrontController.php');
class XenForumEditPostModuleFrontController extends XenForumModuleFrontController
{
    public $ssl = true;

    public function setMedia()
    {
        parent::setMedia();
//        $this->addJS(_MODULE_DIR_.$this->module->name.'/libraries/tinymce/tinymce.min.js', 'all');
        //$this->addJS('//cdn.tinymce.com/4/tinymce.min.js', 'all');
        if (version_compare(_PS_VERSION_, '1.7.0.0 ', '>=')) {
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
        $id_xenforum_post = (int)Tools::getvalue('id');
        $xfpost = new XenForumPost($id_xenforum_post);
        $xf_topic = new XenForumTopic($xfpost->id_xenforum);
        $id_xenforum_cat = $xf_topic->id_xenforum_cat;
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
            'title' => sprintf($this->l('Edit post in: %s'), $xf_topic->meta_title),
            'url' => null,
        );
        
        return $breadcrumb;
    }

    public function postProcess()
    {
        $cookie = Context::getContext()->cookie;
        $id_customer = $this->context->customer->id;
        $id_xenforum_post = (int)Tools::getvalue('id');
        $xfpost = new XenForumPost($id_xenforum_post);
        $this->className = 'XenForumPost';
        $this->table = 'xenforum_post';

        if ($id_xenforum_post) {
            $post = $xfpost->getCom($id_xenforum_post);
            //$category_status = $categoryinfo['active'];
            $options = array(
                'id_xenforum' => $post['id_xenforum'],
                'slug' => $post['link_rewrite']
            );
            $redirect_url = xenforum::getLink('xenforum_viewdetails', $options);
        }

        if ((Tools::isSubmit('edit_post')) && ($id_customer) && isset($cookie->token) && ($cookie->token == Tools::getValue('token'))) {
            $xfpost->comment = Tools::getValue('comment');
            $xfpost->active = Tools::getValue('active');
            $xfpost->attachments = Tools::getValue('attachments');

            $this->checkValidation();
            if (!empty($this->errors)) {
                return false;
            }
            
            if ($xfpost->update()) {
                Tools::redirect($redirect_url);
            } else {
                $this->context->smarty->assign('error', 'error when save');
            }
        }
    }
    
    public function initContent()
    {
        parent::initContent();
        $cookie = Context::getContext()->cookie;
        $token = Tools::passwdGen(12);
        // Edit a Post
        $id_customer = $this->context->customer->id;
        $id_xenforum_post = (int)Tools::getvalue('id');
        $xfpost = new XenForumPost($id_xenforum_post);
        $post = $xfpost->getCom($id_xenforum_post);
        // $blogcat = new XenForumCat();
        $bloguser = new XenForumUser($id_customer);

        $id_xenforum_cat = $post['id_xenforum_cat'];
        // Check permission on access this topic
        if ($this->private || !$bloguser->checkCatAccess($this->context->customer->id, $id_xenforum_cat)) {
            if (version_compare(_PS_VERSION_, '1.7.0.0 ', '>=')) {
                $this->setTemplate('module:xenforum/views/templates/front/ps17-private_msg.tpl');
            } else {
                $this->setTemplate('private_msg.tpl');
            }
            return;
        }

        if ($id_customer) {
            $user = $bloguser->getUser();
            $nickname = $user['nickname'];

            $this->context->smarty->assign(array(
                'id_customer'       => $id_customer,
                'nickname'          => $nickname
            ));
        }

        $cookie->__set('token', $token);
        $this->context->smarty->assign('token', $token);
        // $nested_categories = $blogcat->getCategoriesById(array(), $post['id_xenforum']);
        // $nested_categories = array_reverse($nested_categories);

        if ((int)$id_xenforum_post) {
            $this->context->smarty->assign(array(
                'id_xenforum'      => $post['id_xenforum'],
                'meta_title'       => html_entity_decode($post['meta_title']),
                'link_rewrite'     => $post['link_rewrite'],
                'id_author'        => $post['id_author'],
                'active'           => $post['active'],
                'attachments'      => $post['attachment'],
                'comment'          => xenforum::xfStripslashes($post['comment']),
                'id_xenforum_post' => $id_xenforum_post,
                'errors'           => $this->errors,
                // 'nested_categories' => $nested_categories
            ));
        }

        if (Tools::isSubmit('edit_post')) {
            $retrieve_comment = Tools::getValue('comment', '');
            $attachments = Tools::getValue('attachments', null);
            $retrieve_attachments = array();
            if (!empty($attachments)) {
                $retrieve_attachments = XenForumAttachment::getListAttachments($attachments);
            }
            $this->context->smarty->assign(array(
                'comment'      => $retrieve_comment,
                'attachments'  => $retrieve_attachments
            ));
        }

        if ((int)$id_xenforum_post) {
            if (version_compare(_PS_VERSION_, '1.7.0.0 ', '>=')) {
                $this->setTemplate('module:xenforum/views/templates/front/ps17-edit_post.tpl');
            } else {
                $this->setTemplate('edit_post.tpl');
            }
        }
    }
}
