<?php
/**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

class XenForumNotification extends ObjectModel
{
    public $id_notification;
    public $id_user;
    public $id_visitor;
    public $action;
    public $id_post;
    public $readn;
    public $date_add;

    public static $definition = array(
        'table' => 'xenforum_notification',
        'primary' => 'id_notification',
        'multilang'=>false,
        'fields' => array(
                'id_user'           => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
                'id_visitor'        => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
                'action'            => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName','required' => true),
                'id_post'           => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
                'readn'             => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
                'date_add'          => array('type' => self::TYPE_DATE, 'validate' => 'isString'),
        ),
    );

    /**
     * @see ObjectModel::add()
     */
    public function add($autodate = true, $null_values = false)
    {
        if (!parent::add($autodate, $null_values)) {
            return false;
        }

        // Handle to send mail
        $bloguser = new XenForumUser((int)$this->id_user);
        $user = $bloguser->getUser();

        if ((bool)$user['notification']) {
            $blogvisitor = new XenForumUser((int)$this->id_visitor);
            $blogcom = new XenForumPost((int)$this->id_post);
            $id_topic = $blogcom->id_xenforum;
            $blogtopic = new XenForumTopic($id_topic);

            $options = array(
                    'id_xenforum' => $blogtopic->id_xenforum,
                    'slug' => $blogtopic->link_rewrite
            );
            $link = addcslashes(xenforum::getLink('xenforum_viewdetails', $options), "'");

            $params = array(
                    '{nickname}'    => $user['nickname'],
                    '{visitor}'     => $blogvisitor->nickname,
                    '{comment}'     => $blogcom->comment,
                    '{link}'        => $link,
            );

            // Send notif email to owner
            self::sendNotifMail($user['email'], $this->action, $blogtopic->meta_title, $params);
        }

        return true;
    }

    /* Send the notification via email */
    public static function sendNotifMail($email_to, $action, $topic_title = null, $var_list = false)
    {
        $id_lang = Context::getContext()->language->id;
        if ($action == 'like') {
            $var_list['{action}'] = Mail::l('likes', (int)$id_lang);
        } elseif ($action == 'comment') {
            $var_list['{action}'] = Mail::l('also comments on', (int)$id_lang);
        }

        if (Validate::isEmail($email_to)) {
            Mail::Send(
                (int)$id_lang,
                'message_from_xenforum',
                $topic_title,
                $var_list,
                $email_to,
                null,
                null,
                null,
                null,
                null,
                dirname(__FILE__).'/../mails/'
            );
        }
    }

    public function readnNotification($id_user, $id_topic)
    {
        $query = Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'xenforum_notification` n
                LEFT JOIN `'._DB_PREFIX_.'xenforum_post` p ON (p.`id_xenforum_post` = n.`id_post`)
                SET readn = 1
                WHERE n.readn = 0 AND n.`id_user` = '.(int)$id_user.' AND p.`id_xenforum` = '.(int)$id_topic);

        return $query;
    }

    public function delNotifLike()
    {
        return Db::getInstance()->execute('DELETE 
            FROM `'._DB_PREFIX_.'xenforum_notification`
            WHERE `id_visitor` = '.(int)$this->id_visitor.' AND `action` = \''.pSQL($this->action).'\' AND `id_post` = '.(int)$this->id_post);
    }

    /*
    * Add new notification to DB when an other user comment on a post
    */

    public function addNewNotifCom($id_user, $id_visitor, $id_post, $id_topic)
    {
        $res = true;
        $this->id_user = (int)$id_user;
        $this->id_visitor = (int)$id_visitor;
        $this->action = 'comment';
        $this->id_post = (int)$id_post;

        // Check if exist. We will only send once for each topic/user/visitor
        $query = $this->checkByTopic($id_topic);

        if (!$query) {
            $res &= $this->add();
        }

        return $res;
    }

    public function checkByTopic($id_topic)
    {
        return Db::getInstance()->executeS('SELECT *
                FROM `'._DB_PREFIX_.'xenforum_notification` n
                LEFT JOIN `'._DB_PREFIX_.'xenforum_post` p ON (p.`id_xenforum_post` = n.`id_post` AND p.`id_xenforum` = '.(int)$id_topic.')
                WHERE n.`id_user` = '.(int)$this->id_user.'
                AND n.`id_visitor` = '.(int)$this->id_visitor.'
                AND n.`action` = \''.pSQL($this->action).'\'
                AND n.readn = 0');
    }

    public function getUnread()
    {
        return Db::getInstance()->getValue('SELECT COUNT(*)
                FROM `'._DB_PREFIX_.'xenforum_notification`
                WHERE `id_user` = '.(int)$this->id_user.' AND `id_visitor` <> '.(int)$this->id_user.' AND `readn` = 0');
    }

    public function getNotification()
    {
        $query = Db::getInstance()->executeS('SELECT *
                FROM `'._DB_PREFIX_.'xenforum_notification` n
                LEFT JOIN `'._DB_PREFIX_.'xenforum_user` u ON (u.`id_xenforum_user` = n.`id_visitor`)
                LEFT JOIN `'._DB_PREFIX_.'xenforum_post` p ON (p.`id_xenforum_post` = n.`id_post`)
                LEFT JOIN `'._DB_PREFIX_.'xenforum` t ON (t.`id_xenforum` = p.`id_xenforum`)
                WHERE n.`id_user` = '.(int)$this->id_user.' AND n.`id_visitor` <> '.(int)$this->id_user.'
                ORDER BY n.`readn` ASC, n.`id_notification` DESC LIMIT 20');

        if (!empty($query)) {
            foreach ($query as $key => $value) {
                $query[$key]['comment'] = xenforum::removeQuote($value['comment']);
                $query[$key]['filter_date_add'] = date('c', strtotime($value['date_add']));
                $query[$key]['date_add'] = date('Y-m-d H:i:s P', strtotime($value['date_add']));
            }
        }

        return $query;
    }
}
