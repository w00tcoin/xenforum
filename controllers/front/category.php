<?php
/**
* Prestashop Addons | Module by: <App1Pro>
*
* @author    Chuyen Nguyen [App1Pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

include_once(dirname(__FILE__).'/../../classes/controllers/FrontController.php');
class XenForumCategoryModuleFrontController extends XenForumModuleFrontController
{
    public $ssl = true;

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $blogcat = new XenForumCat();
        $id_xenforum_cat = (int)Tools::getvalue('id_xenforum_cat');
        $nested_categories = $blogcat->getCategoriesById(array(), $id_xenforum_cat);
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
        return $breadcrumb;
    }

    public function initContent()
    {
        parent::initContent();
        $result = array();
        $post = array();
        $allposts = array();
        $blog = new XenForumTopic();
        $blogcat = new XenForumCat();
        $blogcom = new XenForumPost();
        $user = new XenForumUser();
        $id_lang = $this->context->language->id;
        $i = 0;

        $id_xenforum_cat = (int)Tools::getvalue('id_xenforum_cat');
        $total = $blog->getToltal($id_xenforum_cat);

        // Check permission on access this topic
        if ($this->private || !$user->checkCatAccess($this->context->customer->id, $id_xenforum_cat)) {
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

        if ((bool)$total) {
            $total_pages = ceil($total / $limit);
        }

        if ((boolean)Tools::getValue('page')) {
            $page = Tools::getValue('page');
            if ($page > $total_pages) {
                $page = $total_pages;
            }

            $limit_start = $limit * ($page - 1);
        } else {
            $page = 1;
        }

        // End of Function to paging

        if ((int)$id_xenforum_cat) {
            $categoryinfo = $blogcat->getCategoryById($id_xenforum_cat);
            $nested_categories = $blogcat->getCategoriesById(array(), $id_xenforum_cat);
            $nested_categories = array_reverse($nested_categories);
            //$category_status = $categoryinfo['active'];

            $allposts = $blog->getAllPosts($limit_start, $limit, $id_xenforum_cat);
            $availables = XenForumCat::getAvailableCat($this->context->customer->id);
            $categoryinfo['child_categories'] = $blogcat->getCategories($id_lang, $id_xenforum_cat, $this->context->customer->id, $availables);
            $categoryinfo['meta_title'] = xenforum::xfStripslashes($categoryinfo['meta_title']);
            $categoryinfo['description'] = html_entity_decode($categoryinfo['description']);

            $this->context->smarty->assign(array(
                'id_xenforum_cat'   => $id_xenforum_cat,
                'nested_categories' => $nested_categories,
                'category'          => $categoryinfo
            ));
        }

        if (!empty($allposts)) {
            foreach ($allposts as $post) {
                $blogcats = $blogcat->getCategoryById($post['id_xenforum_cat']);
                $lastest_mess = $blogcom->getLatestOneByPost($post['id_xenforum']);

                $result[$i]                     = $post;
                $result[$i]['meta_title']       = xenforum::xfStripslashes($post['meta_title']);
                $result[$i]['filter_created']   = date('c', strtotime($post['created']));
                $result[$i]['created']          = date('Y-m-d H:i:s P', strtotime($post['created']));
                $result[$i]['cat_link_rewrite'] = $blogcats['link_rewrite'];
                $result[$i]['category_title']   = xenforum::xfStripslashes($blogcats['meta_title']);

                // The one lastest message
                if ($lastest_mess) {
                    $result[$i]['mess_id_post']         = $lastest_mess['id_xenforum_post'];
                    $result[$i]['mess_created']         = $lastest_mess['created'];
                    $result[$i]['filter_mess_created']  = date('c', strtotime($lastest_mess['created']));
                    $result[$i]['mess_created']         = date('Y-m-d H:i:s P', strtotime($lastest_mess['created']));
                    $result[$i]['mess_id_author']       = $lastest_mess['id_author'];
                    $result[$i]['mess_author']          = $lastest_mess['nickname'];
                }
                $i++;
            }

            // Function Paging
            $vision = 4;
            $start = 1;

            if ($total_pages <= $vision) {
                $vision = $total_pages - 1;
            } else {
                if ($page > $vision / 2) {
                    $start = $page - ($vision / 2);
                }

                if (($start + $vision) > $total_pages) {
                    $start = $total_pages - $vision;
                }
            }

            $this->context->smarty->assign(array(
                    'posts_per_page'    => $limit,
                    'curent'            => $page,
                    'start'             => $start,
                    'vision'            => $vision,
                    'page_nums'         => $total_pages - 1,
                    'total_pages'       => $total_pages
            ));
            // End of Function Paging
        }

        $this->context->smarty->assign(array(
            'allposts'          => $result
        ));

        if (version_compare(_PS_VERSION_, '1.7.0.0 ', '>=')) {
            $this->setTemplate('module:xenforum/views/templates/front/ps17-category.tpl');
        } else {
            $this->setTemplate('category.tpl');
        }
    }
}
