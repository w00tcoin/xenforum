<?php
/**
* Prestashop Addons | Module by: <App1Pro>
*
* @author    Chuyen Nguyen [App1Pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

include_once(dirname(__FILE__).'/../../classes/controllers/FrontController.php');
class XenForumDetailsModuleFrontController extends XenForumModuleFrontController
{
    public $ssl = true;
    public $saved = false;

    public function setMedia()
    {
        parent::setMedia();
        $this->addJqueryPlugin('bxslider');
        $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/product_list.css', 'all');
        //$this->addJS('//cdn.tinymce.com/4/tinymce.min.js', 'all');
        if (version_compare(_PS_VERSION_, '1.7.0.0 ', '>=')) {
            $this->registerJavascript(null, '//cdn.tinymce.com/4/tinymce.min.js', array('server' => 'remote'));
        } else {
            $this->addJS('//cdn.tinymce.com/4/tinymce.min.js');
        }
        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/detail.js');
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $xf_post = new XenForumTopic();
        $xf_cat = new XenForumCat();
        $id_xenforum = Tools::getValue('id_xenforum');
        $topic = $xf_post->getPost($id_xenforum);
        $id_xenforum_cat = $topic['id_xenforum_cat'];
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
            'title' => xenforum::xfStripslashes($topic['meta_title']),
            'url' => null,
        );
        
        return $breadcrumb;
    }

    public function postProcess()
    {
        if (!($id_xenforum = Tools::getValue('id_xenforum'))) {
            return false;
        }

        $xftopic = new XenForumTopic($id_xenforum);
        $xfpost = new XenForumPost();
        $user = new XenForumUser();
        $cookie = Context::getContext()->cookie;
        $id_customer = 0;
        $this->className = 'XenForumPost';
        $this->table = 'xenforum_post';
        
        // Add a new comment
        if ($this->context->customer->isLogged()) {
            $id_customer = $this->context->customer->id;
        }

        if ((Tools::isSubmit('add_comment')) && ($id_customer) && isset($cookie->token) && ($cookie->token == Tools::getValue('token'))) {
            $xfpost->comment = Tools::getValue('comment');
            $xfpost->attachments = Tools::getValue('attachments');
            $xfpost->id_xenforum = $id_xenforum;
            $xfpost->id_author = $id_customer;
            $xfpost->active = 0;

            if ($user->checkPerm($id_customer, '_auto_approve_comment_')) {
                $xfpost->active = 1;
            }

            $this->checkValidation();
            if (empty($this->errors)) {
                if ($xfpost->add()) {
                    $this->context->smarty->assign('alreadySent', 1);
                    $this->saved = true;
                    
                    $options = array(
                        'id_xenforum'   => $xftopic->id,
                        'slug'          => $xftopic->link_rewrite
                    );
                    $redirect_url = xenforum::getLink('xenforum_viewdetails', $options);
                    Tools::redirect($redirect_url);
                } else {
                    $this->errors[] = Tools::displayError('Can not add a new topic!');
                }
            }
        }
        // End add a new comment
    }

    public function initContent()
    {
        parent::initContent();
        $id_xenforum = Tools::getValue('id_xenforum');

        $xf_topic = new XenForumTopic();
        $xf_cat = new XenForumCat();
        $xfpost = new XenForumPost();
        $blogtag = new XenForumTags();
        $related = new XenForumRelated();
        $notif = new XenForumNotification();
        $user = new XenForumUser();
        $id_customer = 0;
        $cookie = Context::getContext()->cookie;
        $token = Tools::passwdGen(12);
        $total_comments = 0;
        $all_comments = '';

        $total_comments = $xfpost->getToltalByPost($id_xenforum);

        if ($this->context->customer->isLogged()) {
            $id_customer = $this->context->customer->id;
            $notif->readnNotification($id_customer, $id_xenforum);
        }

        $cookie->__set('token', $token);
        $xf_topic->recordViewed($id_xenforum);
        $topic = $xf_topic->getPost($id_xenforum);
        $id_xenforum_cat = $topic['id_xenforum_cat'];
        // Check permission on access this topic
        if ($this->private || !$user->checkCatAccess($this->context->customer->id, $id_xenforum_cat)) {
            if (version_compare(_PS_VERSION_, '1.7.0.0 ', '>=')) {
                $this->setTemplate('module:xenforum/views/templates/front/ps17-private_msg.tpl');
            } else {
                $this->setTemplate('private_msg.tpl');
            }
            return;
        }
        $categories = $xf_cat->getCategoryById($id_xenforum_cat);

        $nested_categories = $xf_cat->getCategoriesById(array(), $id_xenforum_cat);
        $nested_categories = array_reverse($nested_categories);
        //$all_relateds = $xf_topic->getBLOGRelated($id_xenforum, $id_xenforum_cat);

        //$all_tags = $xf_topic->getTags($id_xenforum);
        //$count_tags = count($all_tags);
        if ($total_comments) {
            // Function to paging
            $limit_start = 0;
            $limit = (bool)Configuration::get('XENFORUM_NUM_COM') ? Configuration::get('XENFORUM_NUM_COM') : 20;

            if ((bool)$total_comments) {
                $total_pages = ceil($total_comments / $limit);
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

            $all_comments = $xfpost->getCommentByPost($id_xenforum, $limit_start, $limit);
            if ($all_comments) {
                foreach ($all_comments as $key => $value) {
                    $all_comments[$key]['comment'] = $this->module->bbcodeParser($value['comment']);
                    $all_comments[$key]['filter_created'] = date('c', strtotime($value['created']));
                    $all_comments[$key]['created'] = date('Y-m-d H:i:s P', strtotime($value['created']));
                }
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

        $all_tags = $blogtag->getTags($id_xenforum);
        $product_data = $related->getProducts($id_xenforum);
        $related_products = array();

        if (!empty($product_data)) {
            foreach ($product_data as $id_product) {
                $related_products[] = $this->getProductData($id_product);
            }
        }
    
        $retrieve_comment = '';
        if (Tools::isSubmit('add_comment') && !$this->saved) {
            $retrieve_comment = Tools::safeOutput(Tools::getValue('comment', ''));
        }

        $this->context->smarty->assign('token', $token);
        $this->context->smarty->assign(array(
            //'all_relateds'        => $all_relateds,
            'total_comments'    => $total_comments,
            'all_tags'          => $all_tags,
            'count_tags'        => count($all_tags),
            'all_comments'      => $all_comments,
            'related_products'  => $related_products,
            'retrieve_comment'  => $retrieve_comment,
            'id_xenforum'       => $id_xenforum,
            'topic'             => $topic,
            // 'link_rewrite'      => $topic['link_rewrite'],
            //'meta_title'        => xenforum::xfStripslashes($topic['meta_title']),
            // 'id_author'         => $topic['id_author'],
            //'author'            => $topic['nickname'],
            'fb_comment_on'     => (bool)Configuration::get('XENFORUM_FB_COMMENT'),
            'native_com_off'    => (bool)Configuration::get('XENFORUM_NATIVE_COM_OFF'),
            'fb_app_id'         => Configuration::get('XENFORUM_FB_APP_ID'),
            'comment_closed'    => (!(int)Configuration::get('XENFORUM_ENABLE_COM') || ((int)$topic['closed'])),
            'like_myself'       => (int)Configuration::get('XENFORUM_LIKE_MYSELF'),
            // 'viewed'            => (int)$topic['viewed'],
            // 'active'            => (int)$topic['active'],
            'filter_created'    => date('c', strtotime($topic['created'])),
            'created'           => date('Y-m-d H:i:s P', strtotime($topic['created'])),
            'errors'            => $this->errors,
        ));

        $image_fommatted_name = (method_exists('ImageType', 'getFormatedName')) ? ImageType::getFormatedName('home') : ImageType::getFormattedName('home');
        $this->context->smarty->assign(array(
            'page_url'          => Configuration::get('XENFORUM_URL'),
            'page_title'        => Configuration::get('XENFORUM_TITLE', $this->context->language->id),
            'homeSize'          => Image::getSize($image_fommatted_name),
            'title_category'    => $categories['meta_title'],
            'cat_link_rewrite'  => $categories['link_rewrite'],
            'id_customer'       => $id_customer,
            'id_xenforum_cat'   => $id_xenforum_cat,
            'nested_categories' => $nested_categories
        ));

        $options = array(
            'id_xenforum' => $topic['id_xenforum'],
            'slug' => $topic['link_rewrite']
        );

        // Sharing
        $this->context->smarty->assign(array(
            'sharing_name'      => addcslashes($topic['meta_title'], "'"),
            'sharing_url'       => addcslashes(xenforum::getLink('xenforum_viewdetails', $options), "'"),
            'sharing_img'       => ''
        ));

        if (version_compare(_PS_VERSION_, '1.7.0.0 ', '>=')) {
            // prepare the products
            $related_products = $this->prepareMultipleProductsForTemplate(
                $related_products
            );

            $this->context->smarty->assign(array(
                'related_products' => $related_products
            ));

            $this->setTemplate('module:xenforum/views/templates/front/ps17-details.tpl');
        } else {
            $this->setTemplate('details.tpl');
        }
    }

    private function getFactory()
    {
        return new ProductPresenterFactory($this->context, new TaxConfiguration());
    }

    protected function getProductPresentationSettings()
    {
        return $this->getFactory()->getPresentationSettings();
    }

    protected function getProductPresenter()
    {
        return $this->getFactory()->getPresenter();
    }

    private function prepareProductForTemplate(array $rawProduct)
    {
        $product = (new ProductAssembler($this->context))
            ->assembleProduct($rawProduct)
            ;

        $presenter = $this->getProductPresenter();
        $settings = $this->getProductPresentationSettings();

        return $presenter->present(
            $settings,
            $product,
            $this->context->language
        );
    }

    protected function prepareMultipleProductsForTemplate(array $products)
    {
        return array_map(array($this, 'prepareProductForTemplate'), $products);
    }

    private function getProductData($id_product)
    {
        $sql = 'SELECT product_shop.id_product, product_attribute_shop.id_product_attribute
                FROM `'._DB_PREFIX_.'product` p
                '.Shop::addSqlAssociation('product', 'p').'
                LEFT JOIN  `'._DB_PREFIX_.'product_attribute` pa ON (product_shop.id_product = pa.id_product)
                '.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.default_on = 1').'
                WHERE product_shop.`active` = 1 
                AND product_shop.`id_product`='.(int)$id_product.' 
                AND p.`id_product`='.(int)$id_product.' 
                AND product_shop.`visibility` IN ("both", "catalog") 
                AND (pa.id_product_attribute IS NULL OR product_attribute_shop.default_on = 1)
                GROUP BY product_shop.id_product';

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

        $sql = 'SELECT p.*, product_shop.*, stock.`out_of_stock` out_of_stock, pl.`description`, pl.`description_short`,
                    pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`,
                    p.`ean13`, p.`upc`, image_shop.`id_image`, il.`legend`, t.`rate`
                FROM `'._DB_PREFIX_.'product` p
                LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (
                    p.`id_product` = pl.`id_product`
                    AND pl.`id_lang` = '.(int)$this->context->cookie->id_lang.Shop::addSqlRestrictionOnLang('pl').'
                )
                '.Shop::addSqlAssociation('product', 'p').'
                LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)'.
                Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover = 1').'
                LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$this->context->cookie->id_lang.')
                LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (product_shop.`id_tax_rules_group` = tr.`id_tax_rules_group`
                    AND tr.`id_country` = '.(int)Context::getContext()->country->id.'
                    AND tr.`id_state` = 0)
                LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
                '.Product::sqlStock('p', 0).'
                WHERE p.id_product = '.(int)$id_product.'
                AND (i.id_image IS NULL OR image_shop.id_shop='.(int)$this->context->shop->id.')';

        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

        if (!$row) {
            return false;
        }

        if ($result['id_product_attribute']) {
            $row['id_product_attribute'] = $result['id_product_attribute'];
        }

        $product = Product::getProductProperties($this->context->cookie->id_lang, $row);

        if ($product['reduction']) {
            if (!Configuration::get('RELATED_SIMPLE_PRICE')) {
                $product['price_without_reduction'] = Tools::displayPrice($product['price_tax_exc'] + $product['reduction']);
            } else {
                $product['price_without_reduction'] = Tools::displayPrice(
                    Product::getPriceStatic(
                        $product['id_product'],
                        true,
                        $product['id_product_attribute'],
                        6,
                        null,
                        false,
                        false
                    )
                );
            }
        }

        $product['quantity'] = StockAvailable::getQuantityAvailableByProduct($product['id_product'], $product['id_product_attribute']);

        return $product;
    }
}
