<?php
/**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

class XenForumPosition extends ObjectModel
{
    public $id_xenforum_position;
    public $block;
    public $position;
    public $excepts;
    public $sort_order;

    public static $definition = array(
        'table' => 'xenforum_position',
        'primary' => 'id_xenforum_position',
        'multilang'=>false,
        'fields' => array(
                'id_xenforum_position'  => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
                'block'                 => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
                'position'              => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
                'excepts'               => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
                'sort_order'            => array('type' => self::TYPE_INT, 'validate' => 'isInt')
        ),
    );

    public static function getBlocks($position, $controller = null)
    {
        $result = array();
        $sql = 'SELECT DISTINCT *, `id_xenforum_position` as `id` FROM `'._DB_PREFIX_.'xenforum_position` p
                WHERE p.`position` = \''.pSQL($position).'\' ORDER BY `sort_order` ASC;';

        if ($query = Db::getInstance()->executeS($sql)) {
            foreach ($query as $value) {
                if (($value['excepts'] == null) || ($value['excepts'] == '' || ($controller == null))) {
                    $result[] = $value;
                } else {
                    $string = preg_replace('/\s+/', '', $value['excepts']);
                    $list_page = explode(',', $string);

                    if (!in_array($controller, $list_page)) {
                        $result[] = $value;
                    }
                }
            }
        }

        return $result;
    }
}
