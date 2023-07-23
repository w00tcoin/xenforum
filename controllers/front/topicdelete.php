<?php
/**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

include_once(dirname(__FILE__).'/../../classes/controllers/FrontController.php');
class XenForumTopicDeleteModuleFrontController extends XenForumModuleFrontController
{
    public $ssl = true;

    public function initContent()
    {
        parent::initContent();
        $blog = new XenForumTopic();
        $user = new XenForumUser();
        $id_topic = (int)Tools::getvalue('id_xenforum');

        if ($this->private) {
            if (version_compare(_PS_VERSION_, '1.7.0.0 ', '>=')) {
                $this->setTemplate('module:xenforum/views/templates/front/ps17-private_msg.tpl');
            } else {
                $this->setTemplate('private_msg.tpl');
            }
            return;
        }

        // Edit a Post
        if ($this->context->customer->id) {
            $id_customer = $this->context->customer->id;
        }

        if ((int)$id_topic && $id_customer) {
            $topic = $blog->getPost($id_topic);
            
            $id_xenforum_cat = $topic['id_xenforum_cat'];
            // Check permission on access this topic
            if (!$user->checkCatAccess($this->context->customer->id, $id_xenforum_cat)) {
                Tools::redirect('index.php?controller=404');
            }
            //$category_status = $categoryinfo['active'];
            $id_author = $topic['id_author'];
            $catoptions = array(
                    'id_xenforum_cat' => $topic['id_xenforum_cat'],
                    'slug' => $topic['cat_link_rewrite']
            );
            $url = xenforum::getLink('xenforum_category', $catoptions);

            // Check permission to delete
            if ($user->checkPerm($id_customer, '_delete_all_')
            || (($id_customer == $id_author) && $user->checkPerm($id_customer, '_delete_mine_'))) {
                $blog->deleteTopic($id_topic);
            }

            Tools::redirect($url);
        }
    }
}
