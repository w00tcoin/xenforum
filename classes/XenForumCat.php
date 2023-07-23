<?php
/**
* Prestashop Addons | Module by: <App1Pro>
*
* @author    Chuyen Nguyen [App1Pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

class XenForumCat extends ObjectModel
{
    public $id_xenforum_cat;
    public $meta_title;
    public $link_rewrite;
    public $id_parent;
    public $description;
    public $private = 0;
    public $closed = 0;
    public $active = 1;
    public $created;
    public $position;
    public $modified;

    public static $definition = array(
        'table' => 'xenforum_cat',
        'primary' => 'id_xenforum_cat',
        'multilang'=>true,
        'fields' => array(
                'active'        => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
                'private'       => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
                'closed'        => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
                'id_parent'     => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
                'position'      => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
                'created'       => array('type' => self::TYPE_DATE, 'validate' => 'isString'),
                'modified'      => array('type' => self::TYPE_DATE, 'validate' => 'isString'),
                'meta_title'    => array('type' => self::TYPE_STRING, 'lang'=>true, 'validate' => 'isGenericName','required' => true),
                'link_rewrite'  => array('type' => self::TYPE_STRING, 'lang'=>true, 'validate' => 'isLinkRewrite','required' => true),
                'description'   => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 3999999999999)
        ),
    );

    /**
     * Get all attributes groups for a given language
     *
     * @param integer $id_lang Language id
     * @return array Attributes groups
     */

    public static function getAvailableCat($id_user = 0)
    {
        $user = new XenForumUser();
        if ($id_user) {
            $validations = $user->getValidation($id_user, '_view_private_');
        }

        $ql = 'SELECT pc.`id_xenforum_cat`
            FROM `'._DB_PREFIX_.'xenforum_cat` pc
            WHERE pc.`active` = 1';
        if (!$user->isAdmin($id_user) && !(!empty($validations) && $validations == 'all')) {
            $ql .= ' AND (pc.`private` = 0';
            // Allow this user access to private forum
            if (!empty($validations)) {
                $ql .= ' OR pc.`id_xenforum_cat` IN ('.pSQL($validations).')';
            }
            $ql .= ')';
        }
        
        $ql .= ' ORDER BY pc.`position` ASC';
        $categories = Db::getInstance()->executeS($ql);
        if (!empty($categories)) {
            return array_column($categories, 'id_xenforum_cat');
        }
        return array();
    }

    public static function getChildrenCategoriesId(array &$category_ids, $id_parent = 0, $id_user = false, $availables = null)
    {
        $user = new XenForumUser();
        $sql = 'SELECT pc.`id_xenforum_cat` FROM `'._DB_PREFIX_.'xenforum_cat` pc WHERE pc.`active` = 1';

        if ($id_user !== false && !$user->isAdmin($id_user)) {
            if (empty($availables)) {
                return false;
            }
            $sql .= ' AND pc.`id_xenforum_cat` IN ('.pSQL(implode(',', $availables)).')';
        }

        $sql .= ' AND pc.`id_parent` = '.(int)$id_parent.';';

        $categories = Db::getInstance()->executeS($sql);
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $category_ids[] = $category['id_xenforum_cat'];
                self::getChildrenCategoriesId($category_ids, $category['id_xenforum_cat'], $id_user, $availables);
            }
        }

        return $category_ids;
    }

    public static function getCategories($id_lang, $id_parent = 0, $id_user = false, $availables = null)
    {
        $xf_topic = new XenForumTopic();
        $xf_comment = new XenForumPost();
        $user = new XenForumUser();
        
        $sql = 'SELECT DISTINCT pc.*, pcl.*
            FROM `'._DB_PREFIX_.'xenforum_cat` pc
            LEFT JOIN `'._DB_PREFIX_.'xenforum_cat_lang` pcl ON (pc.`id_xenforum_cat` = pcl.`id_xenforum_cat`)
            WHERE
                pc.`active` = 1';

        if ($id_user !== false && !$user->isAdmin($id_user)) {
            if (empty($availables)) {
                return false;
            }
            $sql .= ' AND pc.`id_xenforum_cat` IN ('.pSQL(implode(',', $availables)).')';
        }

        $sql .= ' AND pc.`id_parent` = '.(int)$id_parent.'
                AND pcl.`id_lang` = '.(int)$id_lang.'
            GROUP BY pc.`id_xenforum_cat`
            ORDER BY pc.`position` ASC
        ';

        $categories = Db::getInstance()->executeS($sql);
        if (!empty($categories)) {
            foreach ($categories as $key => $category) {
                $children_list = array($category['id_xenforum_cat']);
                $children_list = self::getChildrenCategoriesId($children_list, $category['id_xenforum_cat'], $id_user, $availables);
                $categories[$key]['last_post'] = $xf_topic->getLatestOneByCat($children_list);
                $categories[$key]['total'] = $xf_topic->getTotalByCat($children_list);
                $categories[$key]['total_comment'] = $xf_comment->getTotalByCat($children_list);
                $categories_loop = self::getCategories($id_lang, $category['id_xenforum_cat'], $id_user, $availables);
                if (!empty($categories_loop)) {
                    $categories[$key]['child_categories'] = $categories_loop;
                }
            }
        }

        return $categories;
    }

    public static function getCategoriesSelection($id_lang, $level = 0, $id_parent = 0)
    {
        $ret = array();
        $categories = Db::getInstance()->executeS('
            SELECT DISTINCT pc.*, pcl.*, COUNT(p.id_xenforum) AS total FROM `'._DB_PREFIX_.'xenforum_cat` pc
            LEFT JOIN `'._DB_PREFIX_.'xenforum_cat_lang` pcl ON (pc.`id_xenforum_cat` = pcl.`id_xenforum_cat`)
            LEFT JOIN `'._DB_PREFIX_.'xenforum` p ON (p.`id_xenforum_cat` = pc.`id_xenforum_cat`)
            WHERE pc.`id_parent` = '.(int)$id_parent.' AND pcl.`id_lang` = '.(int)$id_lang.'
            GROUP BY pc.`id_xenforum_cat`
            ORDER BY pc.`position` ASC
        ');

        if ($categories) {
            foreach ($categories as $category) {
                $category['meta_title'] = str_repeat('- - - ', $level).$category['meta_title'];
                $ret[] = $category;
                $categories_loop = self::getCategoriesSelection($id_lang, $level + 1, $category['id_xenforum_cat']);
                if (!empty($categories_loop)) {
                    foreach ($categories_loop as $value_loop) {
                        array_push($ret, $value_loop);
                    }
                }
            }
        }

        return $ret;
    }
    
    public static function getCategoryById($id_xenforum_cat)
    {
        $id_lang = (int)Context::getContext()->language->id;
        $sql = 'SELECT * FROM '._DB_PREFIX_.'xenforum_cat ac
                LEFT JOIN '._DB_PREFIX_.'xenforum_cat_lang acl ON (acl.id_xenforum_cat = ac.id_xenforum_cat)
                WHERE ac.id_xenforum_cat = '.(int)$id_xenforum_cat.' AND acl.id_lang = '.(int)$id_lang;

        $result = Db::getInstance()->getRow($sql);

        if (!$result) {
            return false;
        }

        return $result;
    }
    
    public static function getCategoriesById($categories, $id_xenforum_cat)
    {
        $id_lang = (int)Context::getContext()->language->id;
        $sql = 'SELECT * FROM '._DB_PREFIX_.'xenforum_cat ac
                LEFT JOIN '._DB_PREFIX_.'xenforum_cat_lang acl ON (acl.id_xenforum_cat = ac.id_xenforum_cat)
                WHERE ac.id_xenforum_cat = '.(int)$id_xenforum_cat.' AND acl.id_lang = '.(int)$id_lang;

        $result = Db::getInstance()->getRow($sql);

        if (!$result) {
            return false;
        }
        
        $categories[] = $result;
        
        if ($result['id_parent']) {
            // Has child
            $categories = self::getCategoriesById($categories, $result['id_parent']);
        }

        return $categories;
    }

    public static function getMetaById($id_xenforum_cat, $id_lang = null)
    {
        $meta = array();

        if ($id_lang == null) {
            $id_lang = (int)Context::getContext()->language->id;
        }

        $post = self::getCategoryById($id_xenforum_cat);

        if (!$post) {
            return false;
        }

        $meta['meta_title'] = xenforum::xfStripslashes($post['meta_title']);

        if ($post['description']) {
            $meta['meta_description'] = strip_tags($post['description']);
        }

        $meta['meta_keywords'] = Configuration::get('XENFORUM_KEYW', $id_lang);

        return $meta;
    }

    public function updatePosition($way, $position)
    {
        $sql = 'SELECT pp.`position`, pp.`id_xenforum_cat`
            FROM `'._DB_PREFIX_.'xenforum_cat` pp
            WHERE pp.`id_xenforum_cat` = '.(int)Tools::getValue('id_xenforum_cat', 1).'
            ORDER BY pp.`position` ASC';

        $res = Db::getInstance()->executeS($sql);

        if (!$res) {
            return false;
        }

        foreach ($res as $poll) {
            if ((int)$poll['id_xenforum_cat'] == (int)$this->id) {
                $moved_poll = $poll;
            }
        }

        if (!isset($moved_poll) || !isset($position)) {
            return false;
        }

        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        $result = Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'xenforum_cat`
            SET `position`= `position` '.($way ? '- 1' : '+ 1').'
            WHERE `position`
            '.($way
                ? '> '.(int)$moved_poll['position'].' AND `position` <= '.(int)$position
                : '< '.(int)$moved_poll['position'].' AND `position` >= '.(int)$position)
        ) && Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'xenforum_cat`
            SET `position` = '.(int)$position.'
            WHERE `id_xenforum_cat`='.(int)$moved_poll['id_xenforum_cat']
        );

        return $result;
    }
    public static function cleanPositions()
    {
        $return = true;

        $sql = 'SELECT `id_xenforum_cat`
            FROM `'._DB_PREFIX_.'xenforum_cat`
            ORDER BY `position`';
        $result = Db::getInstance()->executeS($sql);

        $i = 0;
        foreach ($result as $value) {
            $return = Db::getInstance()->execute(
                'UPDATE `'._DB_PREFIX_.'xenforum_cat`
                SET `position` = '.(int)$i++.'
                WHERE `id_xenforum_cat` = '.(int)$value['id_xenforum_cat']
            );
        }

        return $return;
    }

    public static function getHigherPosition()
    {
        $sql = 'SELECT MAX(`position`)
                FROM `'._DB_PREFIX_.'xenforum_cat`';
        $position = DB::getInstance()->getValue($sql);

        return (is_numeric($position)) ? $position : - 1;
    }
}
