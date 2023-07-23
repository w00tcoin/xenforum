<?php
/**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

class XenForumUser extends ObjectModel
{
    public $id_xenforum_user;
    public $nickname;
    public $signature;
    public $city;
    public $id_country;
    public $private;
    public $approved;
    public $is_staff;
    public $active;
    public $id_xenforum_group = 1;
    public $notification = 1;
    public $date_approved;
    public $date_submited;
    public $last_access;
    public $permissions = array(
        array('id_rule' => 1, 'rule' => '_view_all_', 'name' => 'View private forum'),
        array('id_rule' => 2, 'rule' => '_view_private_', 'name' => 'View private topics'),
        array('id_rule' => 3, 'rule' => '_create_topic_', 'name' => 'Create new topics'),
        array('id_rule' => 4, 'rule' => '_create_comment_', 'name' => 'Create comments'),
        array('id_rule' => 5, 'rule' => '_edit_all_', 'name' => 'Edit all'),
        array('id_rule' => 6, 'rule' => '_edit_mine_', 'name' => 'Edit my contents'),
        array('id_rule' => 7, 'rule' => '_delete_all_', 'name' => 'Delete all'),
        array('id_rule' => 8, 'rule' => '_delete_mine_', 'name' => 'Delete my contents'),
        array('id_rule' => 9, 'rule' => '_auto_approve_topic_', 'name' => 'Automatically approve my topics'),
        array('id_rule' => 10, 'rule' => '_auto_approve_comment_', 'name' => 'Automatically approve my comments'),
        array('id_rule' => 11, 'rule' => '_edit_link_rewrite_', 'name' => 'Edit link-rewrite'),
        array('id_rule' => 12, 'rule' => '_edit_related_products_', 'name' => 'Add/edit related products'),
        array('id_rule' => 13, 'rule' => '_close_topic_', 'name' => 'Close topic'),
        array('id_rule' => 14, 'rule' => '_highlight_topic_', 'name' => 'Highlight topic'),
        array('id_rule' => 15, 'rule' => '_allowed_upload_', 'name' => 'Upload image in posts'),
    );
    
    const ID_ADMIN_GROUP = 2;

    public static $definition = array(
        'table' => 'xenforum_user',
        'primary' => 'id_xenforum_user',
        'multilang'=>false,
        'fields' => array(
                'id_xenforum_user'  => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
                'nickname'          => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName','required' => true),
                'id_xenforum_group' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
                'private'           => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
                'approved'           => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
                'is_staff'          => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
                'city'              => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                'id_country'        => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
                'signature'         => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                'notification'      => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
                'date_approved'     => array('type' => self::TYPE_DATE, 'validate' => 'isString'),
                'date_submited'     => array('type' => self::TYPE_DATE, 'validate' => 'isString'),
                'last_access'       => array('type' => self::TYPE_DATE, 'validate' => 'isString'),
        ),
    );

    public function getUser()
    {
        $result = array();
        if (!$this->id) {
            return null;
        }
//        $this->updateOlderMember();
        $sql = 'SELECT * FROM (SELECT * FROM `'._DB_PREFIX_.'customer` WHERE `id_customer` = '.(int)$this->id.') as c';
        $sql .= ' LEFT JOIN (SELECT * FROM `'._DB_PREFIX_.'xenforum_user` WHERE `id_xenforum_user` = '.(int)$this->id.') as u ON (c.id_customer = u.id_xenforum_user) ';
        $sql .= ' LEFT JOIN '._DB_PREFIX_.'xenforum_group ug ON (ug.id_xenforum_group = u.id_xenforum_group)';
        //$sql .= ' WHERE u.`id_xenforum_user` = '.(int)$this->id;

        if ($values = Db::getInstance()->executeS($sql)) {
            foreach ($values as $value) {
                    $result = $value;
            }

            if ($result['last_access'] && $result['last_access']) {
                $result['filter_date_upd'] = date('c', strtotime($result['last_access']));
                $result['date_upd'] = date('Y-m-d H:i:s P', strtotime($result['last_access']));
            }
        }

        return $result;
    }

    public function saveAccess()
    {
        return Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'xenforum_user` SET `last_access` = "'.date('Y-m-d H:i:s').'" WHERE `id_xenforum_user` = '.(int)$this->id);
    }

    public static function getRealID($id_user)
    {
        $sql = 'SELECT u.id_xenforum_user FROM `'._DB_PREFIX_.'xenforum_user` u
                WHERE MD5(u.`id_xenforum_user`) = \''.pSQL($id_user).'\'';

        return Db::getInstance()->getValue($sql);
    }

    public static function isAdmin($id_user)
    {
        if (!$id_user) {
            return false;
        }
        $sql = 'SELECT u.id_xenforum_group FROM `'._DB_PREFIX_.'xenforum_user` u
                LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.id_customer = u.id_xenforum_user)
                WHERE u.approved = 1 AND u.`id_xenforum_user` = '.(int)$id_user;

        if ($id_xenforum_group = Db::getInstance()->getValue($sql)) {
            if ($id_xenforum_group == self::ID_ADMIN_GROUP) {
                return true;
            }
        }

        return false;
    }

    public function addUser($values = array())
    {
        $query = Db::getInstance()->execute('
                REPLACE INTO `'._DB_PREFIX_.'xenforum_user` (`id_xenforum_user`, `id_xenforum_group`, `nickname`, `is_staff`)
                VALUES ('.(int)$values['id_user'].', '.(int)$values['id_group'].', "'.pSQL($values['nickname']).'", '.(int)$values['is_staff'].');');

        if (!$query) {
            return false;
        }

        return true;
    }

    public function upd($id_user, $nickname, $signature = '')
    {
        return Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'xenforum_user`
                SET `nickname` = "'.pSQL($nickname).'", `signature` = "'.pSQL($signature).'" WHERE `id_xenforum_user` = '.(int)$id_user);
    }

    /*public function updateOlderMember()
    {
        $new = array();
        $query = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'customer`;');
        if (!$query) {
            return '';
        }

        foreach ($query as $value) {
            $new['id_user'] = (int)$value['id_customer'];
            $new['nickname'] = $value['firstname'].' '.$value['lastname'];
            $new['id_group'] = 1;
            $new['is_staff'] = 0;

            if (!$this->exist($value['id_customer'])) {
                $this->addUser($new);
            }
        }
    }*/


    public function getValidation($id_user = 0, $rule = null)
    {
        $id_rule = 0;
        if (!$id_user || $rule == null || $rule == '') {
            return false;
        } elseif (null !== $key = array_search($rule, array_column($this->permissions, 'rule'))) {
            $id_rule = $this->permissions[$key]['id_rule'];
        }

        $sql = 'SELECT ugp.`validation` FROM `'._DB_PREFIX_.'xenforum_user` u
                LEFT JOIN '._DB_PREFIX_.'xenforum_group ug ON (ug.id_xenforum_group = u.id_xenforum_group)
                LEFT JOIN '._DB_PREFIX_.'xenforum_group_rule ugp ON (ugp.id_xenforum_group = ug.id_xenforum_group)
                WHERE u.`id_xenforum_user` = '.(int)$id_user.' AND u.approved = 1 AND ugp.id_rule = '.(int)$id_rule;

        return Db::getInstance()->getValue($sql);
    }
    
    
    public function checkCatAccess($id_user = 0, $id_category = 0)
    {
        $cat = new XenForumCat();
        $availables = $cat->getAvailableCat((int)$id_user);
        if (!$id_category || empty($availables) || !in_array((int)$id_category, $availables)) {
            return false;
        }
        return true;
    }
    
    
    public function checkPerm($id_user = 0, $rule = null, $finder = null)
    {
        if (!$id_user || $rule == null || $rule == '') {
            return false;
        } elseif (null !== $key = array_search($rule, array_column($this->permissions, 'rule'))) {
            $id_rule = $this->permissions[$key]['id_rule'];
        }

        if (self::isAdmin($id_user)) {
            return true;
        }

        $sql = 'SELECT * FROM `'._DB_PREFIX_.'xenforum_user` u
                LEFT JOIN '._DB_PREFIX_.'xenforum_group ug ON (ug.id_xenforum_group = u.id_xenforum_group)
                LEFT JOIN '._DB_PREFIX_.'xenforum_group_rule ugp ON (ugp.id_xenforum_group = ug.id_xenforum_group)
                WHERE u.`id_xenforum_user` = '.(int)$id_user.' AND u.approved = 1 AND ugp.id_rule = '.(int)$id_rule;

        if (!$perm = Db::getInstance()->getRow($sql)) {
            return false;
        }
        
        if (!is_null($finder)) {
            if (is_null($perm['validation'])) {
                return false;
            }

            $validation = $perm['validation'];

            if (is_string($validation)) {
                $validation = explode(",", preg_replace('/\s+/', '', $validation));
            }
            if ($finder != $validation && !in_array('all', $validation) && !in_array($finder, $validation)) {
                return false;
            }
        }

        return true;
    }

    /*public function exist($id_user)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'xenforum_user` WHERE `id_xenforum_user` = '.(int)$id_user;
        if (!Db::getInstance()->executeS($sql)) {
            return false;
        }

        return true;
    }*/

    public static function getGroupList()
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'xenforum_group` ORDER BY id_xenforum_group ASC;';

        return Db::getInstance()->executeS($sql);
    }

    public static function checkUserLiked($id_post, $id_user = 0)
    {
        $query = Db::getInstance()->executeS('SELECT *
                FROM `'._DB_PREFIX_.'xenforum_like` l
                WHERE l.`id_post` = '.(int)$id_post.' AND l.`id_user` = '.(int)$id_user);

        if (!count($query)) {
            return false;
        }

        return true;
    }

    public static function listUserLiked($id_post, $id_user = 0)
    {
        $result = array();
        $query = Db::getInstance()->executeS('SELECT *
                FROM `'._DB_PREFIX_.'xenforum_like` l
                LEFT JOIN `'._DB_PREFIX_.'customer` c ON (l.`id_user` = c.`id_customer`)
                LEFT JOIN `'._DB_PREFIX_.'xenforum_user` u ON (u.id_xenforum_user = c.`id_customer`)
                WHERE l.`id_post` = '.(int)$id_post.' AND l.`id_user` = '.(int)$id_user.' AND c.`active` = 1');

        foreach ($query as $value) {
            $result[] = $value;
        }

        $query2 = Db::getInstance()->executeS('SELECT *
                FROM `'._DB_PREFIX_.'xenforum_like` l
                LEFT JOIN `'._DB_PREFIX_.'customer` c ON (l.`id_user` = c.`id_customer`)
                LEFT JOIN `'._DB_PREFIX_.'xenforum_user` u ON (u.`id_xenforum_user` = c.`id_customer`)
                WHERE l.`id_post` = '.(int)$id_post.' AND l.`id_user` <> '.(int)$id_user.' AND c.`active` = 1');

        foreach ($query2 as $value) {
            $result[] = $value;
        }

        if (!$result) {
            return '';
        }

        $total_like = count($result);
        $max_display = 4; // Must be greater 2.

        Context::getContext()->smarty->assign(array(
                'id_user'       => $id_user,
                'totalLike'     => $total_like,
                'result'        => $result,
                'max'           => $max_display
        ));

        return Context::getContext()->smarty->display(dirname(__FILE__).'/../views/templates/hook/like_template.tpl');
    }

    public static function getToltalByCust($id_user)
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'xenforum_like l
                LEFT JOIN '._DB_PREFIX_.'xenforum_post p ON (l.id_post = p.id_xenforum_post)
            WHERE p.id_author = '.(int)$id_user;

        if (!$posts = Db::getInstance()->executeS($sql)) {
            return 0;
        }

        return count($posts);
    }

    public function switchLikeUnlike($id_post, $id_user)
    {
        $notification = new XenForumNotification();
        $notification->id_post = $id_post;
        $notification->id_visitor = $id_user;
        $notification->action = 'like';

        $query = Db::getInstance()->executeS('SELECT *
                FROM `'._DB_PREFIX_.'xenforum_like`
                WHERE `id_post` = '.(int)$id_post.' AND `id_user` = '.(int)$id_user);
        if ($query) {
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'xenforum_like` WHERE `id_post` = '.(int)$id_post.' AND `id_user` = '.(int)$id_user);
            $notification->delNotifLike();
        } else {
            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'xenforum_like` (`id_post`, `id_user`) VALUES ('.(int)$id_post.', '.(int)$id_user.');');
            $blogcom = new XenForumPost($id_post);
            $notification->id_user = $id_author = $blogcom->id_author;
            if ($id_author <> $id_user) {
                $notification->add();
            }
        }
    }

    public static function getLatest()
    {
        $result = 0;
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'xenforum_user` u
                LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = u.`id_xenforum_user`)
                WHERE u.`approved` = 1
                ORDER BY c.`id_customer` DESC LIMIT 1';

        if ($values = Db::getInstance()->executeS($sql)) {
            foreach ($values as $value) {
                $result = $value;
            }
        }

        return $result;
    }

    public static function getToltal()
    {
        $sql = 'SELECT count(*) FROM `'._DB_PREFIX_.'xenforum_user` u
                LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = u.`id_xenforum_user`)
                WHERE c.`active` = 1 AND u.`approved` = 1;';

        return Db::getInstance()->getValue($sql);
    }

    public function getAllHidden()
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'xenforum_user` WHERE `approved` = 0';
        if (!$result = Db::getInstance()->executeS($sql)) {
            return 0;
        }
        return count($result);
    }
}

if (!function_exists('array_column')) {
    function array_column($array, $column_name)
    {
        return array_map(
            function ($element) use ($column_name) {
                return $element[$column_name];
            },
            $array
        );
    }
}
