<?php
/**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

class XenForumReport extends ObjectModel
{
    public $id_xenforum_report;
    public $id_post;
    public $id_user;
    public $reason;
    public $date_add;

    public static $definition = array(
        'table' => 'xenforum_report',
        'primary' => 'id_xenforum_report',
        'multilang'=>false,
        'fields' => array(
                'id_post'   => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
                'id_user'   => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
                'reason'    => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
                'date_add'  => array('type' => self::TYPE_DATE, 'validate' => 'isString')
        ),
    );

    /**
     * @see ObjectModel::add()
     */
    public function add($autodate = true, $null_values = false)
    {
        $reports_post = Db::getInstance()->getValue('SELECT count(*) FROM `'._DB_PREFIX_.'xenforum_report`
            WHERE `id_post` = '.(int)$this->id_post.' AND `id_user` = '.(int)$this->id_user);
        if ($reports_post > 0) {
            return false;
        }

        if (!parent::add($autodate, $null_values)) {
            return false;
        }

        if (Validate::isUnsignedId($this->id_post)) {
            $reports_count = Db::getInstance()->getValue('SELECT count(*) FROM `'._DB_PREFIX_.'xenforum_report`
                WHERE `id_post` = '.(int)$this->id_post);
            if (Configuration::get('XENFORUM_REPORTS_MAX') && ($reports_count > Configuration::get('XENFORUM_REPORTS_MAX'))) {
                $post = new XenForumPost((int)$this->id_post);
                if ($post->active) {
                    $post->active = 0;
                    $post->update();
                }
            }
        }
        return true;
    }
}
