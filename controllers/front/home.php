<?php
/**
* Prestashop Addons | Module by: <App1Pro>
*
* @author    Chuyen Nguyen [App1Pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

include_once(dirname(__FILE__).'/../../classes/controllers/FrontController.php');
class XenForumHomeModuleFrontController extends XenForumModuleFrontController
{
    public $ssl = true;

    public function initContent()
    {
        parent::initContent();
        $blog = new XenForumTopic();
        $blogcat = new XenForumCat();
        $blogcom = new XenForumPost();
        $id_lang = $this->context->language->id;
        $allforums = array();
        $children = array();
        
        if ($this->private) {
            if (version_compare(_PS_VERSION_, '1.7.0.0 ', '>=')) {
                $this->setTemplate('module:xenforum/views/templates/front/ps17-private_msg.tpl');
            } else {
                $this->setTemplate('private_msg.tpl');
            }
            return;
        }

        // Function to paging
        $limit_start = 0;
        $limit = (bool)Configuration::get('XENFORUM_NUM_POST') ? Configuration::get('XENFORUM_NUM_POST') : 5;

        $availables = XenForumCat::getAvailableCat($this->context->customer->id);
        $children = $blogcat->getChildrenCategoriesId($children, 1, $this->context->customer->id, $availables);
        // pd($children);
        $allforums = $blogcat->getCategories($id_lang, 0, $this->context->customer->id, $availables);

        if (!empty($allforums)) {
            foreach ($allforums as $key => $forum) {
                $allposts = $blog->getAllPosts($limit_start, $limit, $forum['id_xenforum_cat']);
                if (!empty($allposts)) {
                    foreach ($allposts as $i => $post) {
                        $lastest_mess = $blogcom->getLatestOneByPost($post['id_xenforum']);

                        $allposts[$i]                     = $post;
                        $allposts[$i]['meta_title']       = xenforum::xfStripslashes($post['meta_title']);
                        $allposts[$i]['filter_created']   = date('c', strtotime($post['created']));
                        $allposts[$i]['created']          = date('Y-m-d H:i:s P', strtotime($post['created']));
                        $allposts[$i]['link_rewrite']     = $forum['link_rewrite'];
                        $allposts[$i]['category_title']   = xenforum::xfStripslashes($forum['meta_title']);

                        // The one lastest message
                        if ($lastest_mess) {
                            $allposts[$i]['mess_id_post']         = $lastest_mess['id_xenforum_post'];
                            $allposts[$i]['mess_created']         = $lastest_mess['created'];
                            $allposts[$i]['filter_mess_created']  = date('c', strtotime($lastest_mess['created']));
                            $allposts[$i]['mess_created']         = date('Y-m-d H:i:s P', strtotime($lastest_mess['created']));
                            $allposts[$i]['mess_id_author']       = $lastest_mess['id_author'];
                            $allposts[$i]['mess_author']          = $lastest_mess['nickname'];
                        }
                    }
                }
                $allforums[$key]['allposts'] = $allposts;
            }
        }

        $this->context->smarty->assign(array(
            'allforums'         => $allforums,
            'page_header'       => html_entity_decode(Configuration::get('XENFORUM_INTRO', $id_lang))
        ));

        if (version_compare(_PS_VERSION_, '1.7.0.0 ', '>=')) {
            $this->setTemplate('module:xenforum/views/templates/front/ps17-home.tpl');
        } else {
            $this->setTemplate('home.tpl');
        }
    }
}
