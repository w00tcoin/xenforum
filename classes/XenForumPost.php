<?php
/**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

class XenForumPost extends ObjectModel
{
    public $id_xenforum;
    public $id_author;
    public $comment;
    public $active = 0;
    public $created;

    public static $definition = array(
        'table' => 'xenforum_post',
        'primary' => 'id_xenforum_post',
        'multilang'=>false,
        'fields' => array(
            'id_xenforum'   => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_author'     => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'comment'       => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 3999999999999),
            'active'        => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'created'       => array('type' => self::TYPE_DATE, 'validate' => 'isString')
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
        $output = parent::update($null_values);

        if ($this->id && !empty($this->attachments)) {
            foreach ($this->attachments as $id_attach) {
                $xfattachment = new XenForumAttachment($id_attach);
                $xfattachment->id_post = $this->id;
                $xfattachment->update();
            }
        }
        return $output;
    }

    public function delete()
    {
        if (!parent::delete()) {
            return false;
        }

        $res = Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'xenforum_like` WHERE `id_post` = '.(int)$this->id);
        $res &= Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'xenforum_notification` WHERE `id_post` = '.(int)$this->id);

        return $res;
    }

    public function add($autodate = true, $null_values = false)
    {
        $this->created = date('Y-m-d H:i:s');
        $return = parent::add($autodate, $null_values);

        if ($this->id && !empty($this->attachments)) {
            foreach ($this->attachments as $id_attach) {
                if (!$id_attach) {
                    continue;
                }
                
                $xfattachment = new XenForumAttachment($id_attach);
                $xfattachment->id_post = $this->id;
                $xfattachment->update();
            }
        }
        
        $notification = new XenForumNotification();
        $authors = $this->getRelatedAuthor($this->id_xenforum, $this->id_author);
        if (!empty($authors)) {
            foreach ($authors as $com) {
                $notification->addNewNotifCom(
                    $com['id_author'],
                    $this->id_author,
                    $this->id,
                    $this->id_xenforum
                );
            }
        }

        return $return;
    }

    public function getRelatedAuthor($id_topic, $id_author)
    {
        return Db::getInstance()->executeS('SELECT DISTINCT `id_author`
                FROM `'._DB_PREFIX_.'xenforum_post`
                WHERE `id_xenforum` = '.(int)$id_topic.' AND `id_author` <> '.(int)$id_author);
    }

    public static function getCom($id_xenforum_post)
    {
        $sql = 'SELECT p.*, x.`meta_title`, x.`link_rewrite`, x.`id_xenforum_cat`, u.*
        FROM '._DB_PREFIX_.'xenforum_post p
                LEFT JOIN '._DB_PREFIX_.'xenforum x ON (x.id_xenforum = p.id_xenforum)
                LEFT JOIN '._DB_PREFIX_.'customer c ON (p.id_author = c.id_customer)
                LEFT JOIN '._DB_PREFIX_.'xenforum_user u ON (u.id_xenforum_user = c.id_customer)
                WHERE p.id_xenforum_post = '.(int)$id_xenforum_post;

        $post = Db::getInstance()->getRow($sql);
        if (!empty($post)) {
            $attachment = new XenForumAttachment();
            $post['attachment'] = $attachment->getByPost($post['id_xenforum_post']);
        }

        return $post;
    }

    public static function getCommentByPost($id_post, $limit_start = 0, $limit = 5)
    {
        $sql = 'SELECT *, count(rp.id_xenforum_report) as reports FROM '._DB_PREFIX_.'xenforum_post pc
                LEFT JOIN '._DB_PREFIX_.'customer c ON (pc.id_author = c.id_customer)
                LEFT JOIN '._DB_PREFIX_.'xenforum_user u ON (u.id_xenforum_user = c.id_customer)
                LEFT JOIN '._DB_PREFIX_.'xenforum_group ug ON (ug.id_xenforum_group = u.id_xenforum_group)
                LEFT JOIN '._DB_PREFIX_.'xenforum_report rp ON (rp.id_post = pc.id_xenforum_post)
                WHERE pc.active = 1 AND pc.id_xenforum = '.(int)$id_post.' GROUP BY pc.id_xenforum_post LIMIT '.(int)$limit_start.', '.(int)$limit;

        if (!$comments = DB::getInstance()->executeS($sql)) {
            return false;
        }

        return $comments;
    }

    public static function getToltal()
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'xenforum_post a
            WHERE a.`active` = 1';

        if (!$posts = Db::getInstance()->executeS($sql)) {
            return 0;
        }

        return count($posts);
    }

    public static function getToltalByPost($id_post)
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'xenforum_post a
            WHERE a.`active` = 1 AND a.`id_xenforum` ='.(int)$id_post;

        if (!$posts = Db::getInstance()->executeS($sql)) {
            return 0;
        }

        return count($posts);
    }

    public static function getTotalByCat($category_ids)
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'xenforum_post p
            LEFT JOIN '._DB_PREFIX_.'xenforum x ON (p.id_xenforum = x.id_xenforum)
            WHERE p.active= 1 AND x.id_xenforum_cat IN ('.pSQL(implode(',', $category_ids)).')';

        if (!$posts = Db::getInstance()->executeS($sql)) {
            return 0;
        }

        return count($posts);
    }

    public static function getToltalByCust($id_author)
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'xenforum_post a
            WHERE a.id_author = '.(int)$id_author;

        if (!$posts = Db::getInstance()->executeS($sql)) {
            return 0;
        }

        return count($posts);
    }

    public static function getLatestOneByPost($id_xenforum)
    {
        $result = array();
        $sql = 'SELECT a.*, u.* FROM `'._DB_PREFIX_.'xenforum_post` a
                LEFT JOIN `'._DB_PREFIX_.'customer` c ON (a.`id_author` = c.`id_customer`)
                LEFT JOIN `'._DB_PREFIX_.'xenforum_user` u ON (u.`id_xenforum_user` = c.`id_customer`)
                WHERE a.`active` = 1 AND c.`active` = 1 AND a.`id_xenforum` = '.(int)$id_xenforum;
        $sql .= ' ORDER BY a.`created` DESC LIMIT 1';

        if ($values = Db::getInstance()->executeS($sql)) {
            foreach ($values as $value) {
                $result = $value;
            }
        }

        return $result;
    }

    public function getLatestCom($limit, $id_user = false)
    {
        $cat = new XenForumCat();
        $user = new XenForumUser();

        $results = array();
        $sql = 'SELECT a.*,
                b.`meta_title`, b.`link_rewrite`,
                c.firstname, c.lastname, u.* FROM `'._DB_PREFIX_.'xenforum_post` a
                LEFT JOIN `'._DB_PREFIX_.'xenforum` b ON (b.`id_xenforum` = a.`id_xenforum`)
                LEFT JOIN `'._DB_PREFIX_.'customer` c ON (a.`id_author` = c.`id_customer`)
                LEFT JOIN `'._DB_PREFIX_.'xenforum_user` u ON (u.`id_xenforum_user` = c.`id_customer`)
                WHERE a.`active` = 1 AND b.`active` = 1 AND c.`active` = 1';

        if ($id_user !== false && !$user->isAdmin($id_user)) {
            $availables = $cat->getAvailableCat($id_user);
            if (empty($availables)) {
                return false;
            }
            $sql .= ' AND b.`id_xenforum_cat` IN ('.pSQL(implode(',', $availables)).')';
        }

        $sql .= ' ORDER BY a.`created` DESC LIMIT '.(int)$limit.';';

        if (!$results = Db::getInstance()->executeS($sql)) {
            return '';
        }

        foreach ($results as $key => $value) {
            $results[$key]['meta_title'] = xenforum::xfStripslashes($value['meta_title']);
            $results[$key]['comment'] = xenforum::removeQuote($value['comment']);
            $results[$key]['filter_created'] = date('c', strtotime($value['created']));
            $results[$key]['created'] = date('Y-m-d H:i:s P', strtotime($value['created']));
        }

        return $results;
    }

    public function getAllHidden()
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'xenforum` WHERE `active` = 0';
        if (!$result = Db::getInstance()->executeS($sql)) {
            return 0;
        }
        return count($result);
    }
}
