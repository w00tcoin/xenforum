<?php
/**
* Prestashop Addons | Module by: <App1Pro>
*
* @author    Chuyen Nguyen [App1Pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

class XenForumTopic extends ObjectModel
{
    public $id_xenforum;
    public $id_xenforum_cat;
    public $meta_title;
    public $link_rewrite;
    public $id_author;
    public $viewed;
    public $created;
    public $modified;
    public $closed = 0;
    public $highlight = 0;
    public $active = 1;     /* 0: Hidden/Unapproved, 1: Visible */
    public $position = 0;

    public static $definition = array(
        'table' => 'xenforum',
        'primary' => 'id_xenforum',
        'multilang'=>false,
        'fields' => array(
            'id_xenforum_cat'   => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_author'         => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'meta_title'        => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
            'link_rewrite'      => array('type' => self::TYPE_STRING, 'validate' => 'isLinkRewrite'),
            'viewed'            => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'active'            => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'closed'            => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'highlight'         => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'position'          => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'created'           => array('type' => self::TYPE_DATE, 'validate' => 'isString'),
            'modified'          => array('type' => self::TYPE_DATE, 'validate' => 'isString')
        ),
    );
    /*
    *public function __construct($id = null, $id_lang = null)
    *{
    *       parent::__construct($id, $id_lang);
    *}
    */
    public function update($null_values = false)
    {
        $return = parent::update($null_values);

        $blogtags = new XenForumTags();
        $blogtags->deleteTags($this->id_xenforum);
        if (!empty($this->tags)) {
            $blogtags->addTags($this->id, $this->tags);
        }

        $blogproduct = new XenForumRelated();
        $blogproduct->deleteProducts($this->id_xenforum);
        if (!empty($this->products)) {
            $blogproduct->addProducts($this->id, $this->products);
        }
        return $return;
    }

    public function add($autodate = true, $null_values = false)
    {
        //if ($this->position <= 0) {
        //    $this->position = XenForumCat::getHigherPosition($this->id_xenforum_cat) + 1;
        //}
        $this->created = date('Y-m-d H:i:s');
        $return = parent::add($autodate, $null_values);
        if ($this->id) {
            $blogtags = new XenForumTags();
            $blogproduct = new XenForumRelated();

            if (!empty($this->tags)) {
                $blogtags->addTags($this->id, $this->tags);
            }
            if (!empty($this->products)) {
                $blogproduct->addProducts($this->id, $this->products);
            }
        }
        return $return;
    }

    public function getAuthor($id)
    {
        return Db::getInstance()->getValue('SELECT DISTINCT `id_author`
                FROM `'._DB_PREFIX_.'xenforum` WHERE `id_xenforum` = '.(int)$id);
    }

    public function recordViewed($id)
    {
        return Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'xenforum` SET `viewed` = (`viewed` + 1) WHERE `id_xenforum` = '.(int)$id);
    }

    /**
     * @see ObjectModel::toggleStatus()
     */
    public function toggleStatus()
    {
        $result = parent::toggleStatus();
        return $result;
    }

    public function deleteTopic($id)
    {
        $res = Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'xenforum` WHERE `id_xenforum` = '.(int)$id);
        $res &= Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'xenforum_tags` WHERE `id_topic` = '.(int)$id);
        $res &= Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'xenforum_related` WHERE `id_topic` = '.(int)$id);

        // Delete all post and like
        $blogcom = new XenForumPost;
        $sql = 'SELECT `id_xenforum_post` FROM `'._DB_PREFIX_.'xenforum_post` p WHERE p.`id_xenforum` = '.(int)$id;
        if ($posts = Db::getInstance()->executeS($sql)) {
            foreach ($posts as $post) {
                $res &= $blogcom->delete($post['id_xenforum_post']);
            }
        }

        return $res;
    }

    public function updatePost($values)
    {
        $blogtags = new XenForumTags();
        $blogproduct = new XenForumRelated();
        $res = Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'xenforum`
            SET `meta_title` = "'.pSQL($values['meta_title']).'",
            `link_rewrite` = "'.pSQL($values['link_rewrite']).'",
            `closed` = '.(int)$values['closed'].',
            `highlight` = '.(int)$values['highlight'].'
            WHERE `id_xenforum` = '.(int)$values['id_xenforum']
        );

        $blogtags->deleteTags($values['id_xenforum']);
        if ($values['tags']) {
            $blogtags->addTags($values['id_xenforum'], $values['tags']);
        }
        $blogproduct->deleteProducts($values['id_xenforum']);
        if (!empty($values['products'])) {
            $blogproduct->addProducts($values['id_xenforum'], $values['products']);
        }

        return $res;
    }

    public static function getPost($id_xenforum)
    {
        $id_lang = (int)Context::getContext()->language->id;
        $sql = 'SELECT a.*, c.lastname, c.firstname,
                pcl.meta_title AS cat_meta_title,
                pcl.link_rewrite AS cat_link_rewrite,
                u.*
            FROM `'._DB_PREFIX_.'xenforum` a
            LEFT JOIN `'._DB_PREFIX_.'customer` c ON (a.id_author = c.id_customer)
            LEFT JOIN `'._DB_PREFIX_.'xenforum_user` u ON (u.id_xenforum_user = c.id_customer)
            LEFT JOIN `'._DB_PREFIX_.'xenforum_cat` pc ON (a.id_xenforum_cat = pc.id_xenforum_cat)
            LEFT JOIN `'._DB_PREFIX_.'xenforum_cat_lang` pcl ON (pc.id_xenforum_cat = pcl.id_xenforum_cat AND pcl.id_lang = '.(int)$id_lang.')
            WHERE a.`id_xenforum` = '.(int)$id_xenforum.' GROUP BY a.`id_xenforum`';

        return Db::getInstance()->getRow($sql);
    }

    public function getAllPosts($limit_start, $limit, $id_xenforum_cat = 0)
    {
        $sql = 'SELECT a.*, u.*, xp.replies
                FROM `'._DB_PREFIX_.'xenforum` a
                LEFT JOIN 
                    (
                        SELECT `id_xenforum`, `created`, count(*) AS replies, MAX(`created`) AS `max_created` FROM `'._DB_PREFIX_.'xenforum_post` GROUP BY  `id_xenforum`
                    ) AS xp
                    ON (a.`id_xenforum` = xp.`id_xenforum`)
                LEFT JOIN (
                    SELECT * FROM `'._DB_PREFIX_.'xenforum_user` u1
                    LEFT JOIN `'._DB_PREFIX_.'customer` c
                    ON (u1.`id_xenforum_user` = c.`id_customer`) WHERE c.`active` = 1
                )
                AS u ON (a.`id_author` = u.`id_customer`)
                WHERE a.`active` = 1 ';
        if ((int)$id_xenforum_cat) {
            $sql .= ' AND a.`id_xenforum_cat` = '.(int)$id_xenforum_cat;
        }

        $sql .= ' GROUP BY a.`id_xenforum` ORDER BY a.`highlight` DESC, COALESCE(xp.`max_created`, a.`created`) DESC LIMIT '.(int)$limit_start.', '.(int)$limit;

        if (!$result = Db::getInstance()->executeS($sql)) {
            return false;
        }

        return $result;
    }

    public function getLatestPost()
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'xenforum a
                WHERE a.active = 1';
        $sql .= ' ORDER BY a.position DESC LIMIT 10';

        if (!$result = Db::getInstance()->executeS($sql)) {
            return false;
        }

        return $result;
    }

    public function getTotalByCat($category_ids = null)
    {
        $sql = 'SELECT COUNT(id_xenforum) FROM `'._DB_PREFIX_.'xenforum`
            WHERE `active` = 1 AND `id_xenforum_cat` IN ('.pSQL(implode(',', $category_ids)).')';

        return Db::getInstance()->getValue($sql);
    }

    public function getTopicByTag($limit_start, $limit, $tags = null, $id_user = false)
    {
        $user = new XenForumUser();

        $sql = 'SELECT a.*, u.*,
                (SELECT COUNT(p.id_xenforum_post) FROM `'._DB_PREFIX_.'xenforum_post` p
                        WHERE p.`active` = 1 AND p.`id_xenforum` = a.`id_xenforum`) AS replies
            FROM `'._DB_PREFIX_.'xenforum` a
            LEFT JOIN `'._DB_PREFIX_.'xenforum_tags` at ON (a.`id_xenforum` = at.`id_topic`)
            LEFT JOIN `'._DB_PREFIX_.'customer` c ON (a.`id_author` = c.`id_customer`)
            LEFT JOIN `'._DB_PREFIX_.'xenforum_user` u ON (u.`id_xenforum_user` = c.`id_customer`)
            WHERE at.`name` = \''.pSQL($tags).'\' AND a.`active` = 1';

        if ($id_user !== false && !$user->isAdmin($id_user)) {
            $category = new XenForumCat();
            $availables = $category->getAvailableCat($id_user);
            if (empty($availables)) {
                return false;
            }
            $sql .= ' AND a.`id_xenforum_cat` IN ('.pSQL(implode(',', $availables)).')';
        }

        $sql .= ' ORDER BY a.`highlight` DESC, a.`id_xenforum` DESC LIMIT '.(int)$limit_start.', '.(int)$limit;

        if (!$result = Db::getInstance()->executeS($sql)) {
            return false;
        }

        return $result;
    }

    public function getTopicSearch($limit_start, $limit, $key = null, $id_user = false)
    {
        $user = new XenForumUser();

        $sql = 'SELECT a.*, u.*,
                (SELECT COUNT(p.id_xenforum_post) FROM `'._DB_PREFIX_.'xenforum_post` p
                        WHERE p.`active` = 1 AND p.`id_xenforum` = a.`id_xenforum`) AS replies
            FROM `'._DB_PREFIX_.'xenforum` a
            LEFT JOIN `'._DB_PREFIX_.'customer` c ON (a.`id_author` = c.`id_customer`)
            LEFT JOIN `'._DB_PREFIX_.'xenforum_user` u ON (u.`id_xenforum_user` = c.`id_customer`)
            WHERE a.`meta_title` LIKE \'%'.pSQL($key).'%\' AND a.`active` = 1';

        if ($id_user !== false && !$user->isAdmin($id_user)) {
            $category = new XenForumCat();
            $availables = $category->getAvailableCat($id_user);
            if (empty($availables)) {
                return false;
            }
            $sql .= ' AND a.`id_xenforum_cat` IN ('.pSQL(implode(',', $availables)).')';
        }

        $sql .= ' ORDER BY a.`highlight` DESC, a.`id_xenforum` DESC LIMIT '.(int)$limit_start.', '.(int)$limit;

        if (!$result = Db::getInstance()->executeS($sql)) {
            return false;
        }

        return $result;
    }

    public function getLatestOneByCat($category_ids)
    {
        $result = array();
        $sql = 'SELECT a.*, u.* FROM `'._DB_PREFIX_.'xenforum` a
                LEFT JOIN `'._DB_PREFIX_.'customer` c ON (a.`id_author` = c.`id_customer`)
                LEFT JOIN '._DB_PREFIX_.'xenforum_user u ON (u.`id_xenforum_user` = c.`id_customer`)
                WHERE a.`active` = 1 AND c.`active` = 1 AND a.`id_xenforum_cat` IN ('.pSQL(implode(',', $category_ids)).')';
        $sql .= ' ORDER BY a.`id_xenforum` DESC LIMIT 1';

        if ($values = Db::getInstance()->executeS($sql)) {
            foreach ($values as $value) {
                $result = $value;
            }

            $result['meta_title'] = xenforum::xfStripslashes($result['meta_title']);
            $result['filter_created'] = date('c', strtotime($result['created']));
            $result['created'] = date('Y-m-d H:i:s P', strtotime($result['created']));
        }

        return $result;
    }

    public static function getMetaById($id_xenforum)
    {
        $meta = array();
        $tags = '';

        if (!$post = XenForumTopic::getPost($id_xenforum)) {
            return false;
        }

        $all_tags = XenForumTags::getTags($id_xenforum);
        if ($all_tags) {
            $tags = implode(', ', $all_tags);
        }

        $messages = XenForumPost::getLatestOneByPost($id_xenforum);

        $meta['meta_title'] = xenforum::xfStripslashes($post['meta_title']);

        if ($messages) {
            $meta['meta_description'] = Tools::substr(trim(preg_replace('/\s\s+/', ' ', strip_tags($messages['comment']))), 0, 100);
        }

        if ($tags == '') {
            $meta['meta_keywords'] = Configuration::get('XENFORUM_KEYW', (int)Context::getContext()->language->id);
        } else {
            $meta['meta_keywords'] = $tags;
        }

        return $meta;
    }

    public static function getToltal($id_xenforum_cat = 0)
    {
        $sql = 'SELECT COUNT(*) FROM '._DB_PREFIX_.'xenforum a
                WHERE a.active = 1';
        if ($id_xenforum_cat) {
            $sql .= ' AND a.id_xenforum_cat = '.(int)$id_xenforum_cat;
        }


        return Db::getInstance()->getValue($sql);
    }

    public static function getToltalByCat($id_xenforum_cat = 0)
    {
        $total = 0;
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'xenforum` a WHERE a.id_xenforum_cat = '.(int)$id_xenforum_cat;
        if ($posts = Db::getInstance()->executeS($sql)) {
            $total += count($posts);
        }

        $sql2 = 'SELECT * FROM `'._DB_PREFIX_.'xenforum_cat` c WHERE c.id_parent = '.(int)$id_xenforum_cat;
        if ($cats = Db::getInstance()->executeS($sql2)) {
            $total += count($cats);
        }

        return $total;
    }

    public static function getToltalByTag($tags)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'xenforum` a
            LEFT JOIN `'._DB_PREFIX_.'xenforum_tags` at ON (a.`id_xenforum` = at.`id_topic`)
            WHERE at.`name` = \''.pSQL($tags).'\' AND a.`active` = 1';

        if (!$posts = Db::getInstance()->executeS($sql)) {
            return false;
        }

        return count($posts);
    }

    public static function getToltalSearch($key, $id_user = false)
    {
        $user = new XenForumUser();

        $sql = 'SELECT * FROM `'._DB_PREFIX_.'xenforum` a
            WHERE a.`meta_title` LIKE \'%'.pSQL($key).'%\' AND a.`active` = 1';

        if ($id_user !== false && !$user->isAdmin($id_user)) {
            $category = new XenForumCat();
            $availables = $category->getAvailableCat($id_user);
            if (empty($availables)) {
                return false;
            }
            $sql .= ' AND a.`id_xenforum_cat` IN ('.pSQL(implode(',', $availables)).')';
        }

        if (!$posts = Db::getInstance()->executeS($sql)) {
            return false;
        }

        return count($posts);
    }

    public function getAllHidden()
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'xenforum_post` WHERE `active` = 0';
        if (!$result = Db::getInstance()->executeS($sql)) {
            return 0;
        }
        return count($result);
    }

    /**
     * Move an xenforum inside its group
     * @param boolean $way Up (1)  or Down (0)
     * @param integer $position
     * @return boolean Update result
     */
    public function updatePosition($way, $position)
    {
        if (!$id_xenforum_cat = (int)Tools::getValue('id_xenforum_cat')) {
            $id_xenforum_cat = (int)$this->id_xenforum_cat;
        }

        $sql = '
            SELECT a.`id_xenforum`, a.`position`, a.`id_xenforum_cat`
            FROM `'._DB_PREFIX_.'xenforum` a
            WHERE a.`id_xenforum_cat` = '.(int)$id_xenforum_cat.'
            ORDER BY a.`position` ASC';

        if (!$res = Db::getInstance()->executeS($sql)) {
            return false;
        }

        foreach ($res as $xenforum) {
            if ((int)$xenforum['id_xenforum'] == (int)$this->id) {
                $moved_xenforum = $xenforum;
            }
        }

        if (!isset($moved_xenforum) || !isset($position)) {
            return false;
        }

        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases

        $res1 = Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'xenforum`
            SET `position`= `position` '.($way ? '- 1' : '+ 1').'
            WHERE `position`
            '.($way
                ? '> '.(int)$moved_xenforum['position'].' AND `position` <= '.(int)$position
                : '< '.(int)$moved_xenforum['position'].' AND `position` >= '.(int)$position).'
            AND `id_xenforum_cat`='.(int)$moved_xenforum['id_xenforum_cat']
        );

        $res2 = Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'xenforum`
            SET `position` = '.(int)$position.'
            WHERE `id_xenforum` = '.(int)$moved_xenforum['id_xenforum'].'
            AND `id_xenforum_cat`='.(int)$moved_xenforum['id_xenforum_cat']
        );

        return ($res1 && $res2);
    }

    public function cleanPositions($id_xenforum_cat, $use_last_xenforum = true)
    {
        $return = true;

        $sql = '
            SELECT `id_xenforum`
            FROM `'._DB_PREFIX_.'xenforum`
            WHERE `id_xenforum_cat` = '.(int)$id_xenforum_cat;

        // when delete, you must use $use_last_xenforum
        if ($use_last_xenforum) {
            $sql .= ' AND `id_xenforum` != '.(int)$this->id;
        }

        $sql .= ' ORDER BY `position`';

        $result = Db::getInstance()->executeS($sql);

        $i = 0;
        foreach ($result as $value) {
            $return = Db::getInstance()->execute(
                'UPDATE `'._DB_PREFIX_.'xenforum`
                SET `position` = '.(int)$i++.'
                WHERE `id_xenforum` = '.(int)$value['id_xenforum']
            );
        }

        return $return;
    }

    public static function getHigherPosition($id_xenforum_cat)
    {
        $sql = 'SELECT MAX(`position`)
                FROM `'._DB_PREFIX_.'xenforum`
                WHERE id_xenforum_cat = '.(int)$id_xenforum_cat;

        $position = DB::getInstance()->getValue($sql);

        return (is_numeric($position)) ? $position : - 1;
    }
}
