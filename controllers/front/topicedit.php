<?php
/**
* Prestashop Addons | Module by: <App1Pro>
*
* @author    Chuyen Nguyen [App1Pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

include_once(dirname(__FILE__).'/../../classes/controllers/FrontController.php');
class XenForumTopicEditModuleFrontController extends XenForumModuleFrontController
{
    public $ssl = true;

    public function setMedia()
    {
        parent::setMedia();
        if (version_compare(_PS_VERSION_, '1.7.0.0 ', '>=')) {
            $this->registerJavascript(sha1('js/tools.js'), 'js/tools.js');
        }
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $xf_post = new XenForumTopic();
        $xf_cat = new XenForumCat();
        $id_xenforum = Tools::getValue('id_xenforum');
        $post = $xf_post->getPost($id_xenforum);
        $id_xenforum_cat = $post['id_xenforum_cat'];
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
            'title' => sprintf($this->l('Edit topic: %s'), xenforum::xfStripslashes($post['meta_title'])),
            'url' => null,
        );
        
        return $breadcrumb;
    }

    public function postProcess()
    {
        $cookie = Context::getContext()->cookie;
        $id_topic = (int)Tools::getvalue('id_xenforum');
        $xftopic = new XenForumTopic($id_topic);
        $this->className = 'XenForumTopic';
        $this->table = 'xenforum';

        if ($this->context->customer->id) {
            $id_author = $this->context->customer->id;
        }

        // Edit topic
        if ((Tools::isSubmit('edit_post')) && isset($id_author) && isset($cookie->token) && ($cookie->token == Tools::getValue('token'))) {
            $xftopic->meta_title = Tools::getValue('meta_title');
            $xftopic->tags = Tools::getValue('tags');
            $xftopic->products = Tools::getValue('products');
            $xftopic->link_rewrite = strip_tags(trim(Tools::getValue('link_rewrite')));
            $xftopic->closed = Tools::getValue('closed');
            $xftopic->highlight = Tools::getValue('highlight');
            //$xftopic->id_author = $id_author;

            $this->checkValidation();
            if (!empty($this->errors)) {
                return false;
            }

            if ($xftopic->update()) {
                $options = array(
                    'id_xenforum' => $xftopic->id,
                    'slug' => $xftopic->link_rewrite
                );
                $url = xenforum::getLink('xenforum_viewdetails', $options);
                Tools::redirect($url);
            } else {
                $this->errors[] = Tools::displayError('Error on saving');
            }
        }
    }

    public function initContent()
    {
        parent::initContent();
        $blogcat = new XenForumCat();
        $blogtag = new XenForumTags();
        $blogproduct = new XenForumRelated();
        $bloguser = new XenForumUser($this->context->customer->id);
        $cookie = Context::getContext()->cookie;
        $token = Tools::passwdGen(12);
        $products = '';
        $tags = '';
        $id_topic = (int)Tools::getvalue('id_xenforum');
        $xftopic = new XenForumTopic($id_topic);
        
        // Check permission on access this topic
        if ($this->private || !$bloguser->checkCatAccess($this->context->customer->id, $xftopic->id_xenforum_cat)) {
            if (version_compare(_PS_VERSION_, '1.7.0.0 ', '>=')) {
                $this->setTemplate('module:xenforum/views/templates/front/ps17-private_msg.tpl');
            } else {
                $this->setTemplate('private_msg.tpl');
            }
            return;
        }

        $cookie->__set('token', $token);
        $this->context->smarty->assign('token', $token);

        if ((int)$id_topic) {
            $all_tags = $blogtag->getTags($id_topic);
            if ($all_tags) {
                $tags = implode(', ', $all_tags);
            }
            $all_products = $blogproduct->getProducts($id_topic);
            if ($all_products) {
                $products = implode(', ', $all_products);
            }
            $nested_categories = $blogcat->getCategoriesById(array(), $xftopic->id_xenforum_cat);
            $nested_categories = array_reverse($nested_categories);

            $this->context->smarty->assign(array(
                'id_xenforum_cat'       => $xftopic->id_xenforum_cat,
                'title'                 => $xftopic->meta_title,
                'link_rewrite'          => $xftopic->link_rewrite,
                'id_author'             => $xftopic->id_author,
                'closed'                => $xftopic->closed,
                'highlight'             => $xftopic->highlight,
                'nested_categories'     => $nested_categories,
                'tags'                  => $tags,
                'products'              => $products,
                'id_xenforum'           => $id_topic,
                'errors'                => $this->errors
            ));
        }

        if (Tools::isSubmit('edit_post')) {
            $retrieve_meta_title = Tools::getValue('meta_title', '');
            $retrieve_link_rewrite = Tools::getValue('link_rewrite', '');
            $retrieve_products = Tools::getValue('products', '');
            $retrieve_tags = Tools::getValue('tags', '');
            $this->context->smarty->assign(array(
                'title'         => $retrieve_meta_title,
                'link_rewrite'  => $retrieve_link_rewrite,
                'products'      => $retrieve_products,
                'tags'          => $retrieve_tags
            ));
        }
        
        $this->context->smarty->assign(array(
            'nickname'              => $bloguser->nickname
        ));

        if ((int)$id_topic) {
            if (version_compare(_PS_VERSION_, '1.7.0.0 ', '>=')) {
                $this->setTemplate('module:xenforum/views/templates/front/ps17-topic_edit.tpl');
            } else {
                $this->setTemplate('topic_edit.tpl');
            }
        }
    }
}
