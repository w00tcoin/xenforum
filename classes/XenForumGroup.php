<?php
/**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

class XenForumGroup extends ObjectModel
{
    public $id_xenforum_group;
    public $title;
    public $override_title;
    public $style = 0;

    public static $definition = array(
        'table' => 'xenforum_group',
        'primary' => 'id_xenforum_group',
        'multilang'=>false,
        'fields' => array(
                'id_xenforum_group' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
                'title'             => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
                'override_title'    => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
                'style'             => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName')
        ),
    );

    /**
     * @see ObjectModel::delete()
     */
    public function delete()
    {
        if (in_array($this->id, array(1, 2))) {
            return false;
        }
        return parent::delete();
    }

    public function getList($id_group = 0)
    {
        $user = new XenForumUser();
        $permissions = $user->permissions;

        foreach ($permissions as $key => $permission) {
            $id_rule = $permission['id_rule'];
            $query = Db::getInstance()->getRow('SELECT *
                    FROM `'._DB_PREFIX_.'xenforum_group_rule`
                    WHERE `id_xenforum_group` = '.(int)$id_group.'
                    AND `id_rule` = '.(int)$id_rule.';');

            if ($id_group == XenForumUser::ID_ADMIN_GROUP || !empty($query)) {
                $permissions[$key]['valid'] = true;
            } else {
                $permissions[$key]['valid'] = false;
            }
            if (!empty($query)) {
                $permissions[$key]['validation'] = $query['validation'];
            } else {
                $permissions[$key]['validation'] = '';
            }
        }
//        print_r($permissions);
//        die;

        return $permissions;
    }

    public function getRule($id_group = 0, $id_rule = 0)
    {
        $query = Db::getInstance()->getRow('SELECT *
                FROM `'._DB_PREFIX_.'xenforum_group_rule`
                WHERE `id_xenforum_group` = '.(int)$id_group.' AND `id_rule` = '.(int)$id_rule.';');

        return $query;
    }

    public static function getGroupName($id_group)
    {
        $query = 'SELECT `title` FROM `'._DB_PREFIX_.'xenforum_group` WHERE `id_xenforum_group` = '.(int)$id_group;
            return Db::getInstance()->getValue($query);
    }

    public function deleteOnGroup($id_group)
    {
        return Db::getInstance()->execute('
        DELETE FROM `'._DB_PREFIX_.'xenforum_group_rule`
        WHERE `id_xenforum_group` = '.(int)$id_group);
    }

    public function addNewRule($id_group, $id_rule, $validation = null)
    {
        $sql = 'REPLACE INTO `'._DB_PREFIX_.'xenforum_group_rule` (id_xenforum_group, id_rule, validation)
                VALUES('.(int)$id_group.', '.(int)$id_rule.', \''.pSQL($validation).'\')';

        return Db::getInstance()->execute($sql);
    }
}
