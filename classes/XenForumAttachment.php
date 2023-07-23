<?php
/**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

class XenForumAttachment extends ObjectModel
{
    public $id_xenforum_attachment;
    public $id_user;
    public $id_post = 0;
    public $name;
    public $path;
    public $date_add;

    public static $definition = array(
        'table' => 'xenforum_attachment',
        'primary' => 'id_xenforum_attachment',
        'multilang'=>false,
        'fields' => array(
                'id_user'   => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
                'name'      => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                'path'      => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
                'id_post'   => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
                'date_add'  => array('type' => self::TYPE_DATE, 'validate' => 'isString')
        ),
    );

    /**
     * @see ObjectModel::add()
     */

    /**
     * @see ObjectModel::delete()
     */
    public function delete()
    {
        $full_path = _MODULE_XENFORUM_UPLOAD_DIR_.basename($this->path);
        if (file_exists($full_path)) {
            @unlink($full_path);
        }
        return parent::delete();
    }

    public static function getByPost($id_post = 0)
    {
        $sql = 'SELECT *, id_xenforum_attachment as id
            FROM '._DB_PREFIX_.'xenforum_attachment WHERE id_post = '.(int)$id_post;
        return DB::getInstance()->executeS($sql);
    }
    
    public static function getListAttachments($ids)
    {
        $sql = 'SELECT *, id_xenforum_attachment as id
            FROM '._DB_PREFIX_.'xenforum_attachment WHERE id_xenforum_attachment IN ('.implode(',', $ids).');';
        return DB::getInstance()->executeS($sql);
    }
}
