<?php
/**
* Prestashop Addons | Module by: <App1Pro>
*
* @author    Chuyen Nguyen [App1Pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

class XenForumTags extends ObjectModel
{
    public $id_tags;
    public $name;

    public static $definition = array(
        'table' => 'xenforum_tags',
        'primary' => 'id_tags',
        'multilang'=>false,
        'fields' => array(
                'id_tags'       => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
                'name'          => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
        ),
    );

    public static function addTags($id_topic, $string_tag)
    {
        $result = true;
        $tags = explode(',', strip_tags($string_tag));

        if (is_array($tags)) {
            foreach ($tags as $tag) {
                $tag = Tools::strtolower(trim($tag));
                if ($tag != '') {
                    $result &= Db::getInstance()->execute('
                        REPLACE INTO `'._DB_PREFIX_.'xenforum_tags` (`id_topic`, `name`)
                        VALUES ('.(int)$id_topic.', \''.pSQL($tag).'\')');
                }
            }
        }

        return $result;
    }

    public static function deleteTags($id_topic)
    {
        return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'xenforum_tags` WHERE `id_topic` = '.(int)$id_topic);
    }

    public static function getTags($id_topic)
    {
        $tags = array();

        $tmp = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT `name` FROM '._DB_PREFIX_.'xenforum_tags
            WHERE `id_topic` = '.(int)$id_topic);
        if (!$tmp) {
            return false;
        }

        foreach ($tmp as $val) {
            $tags[] = $val['name'];
        }

        return $tags;
    }

    public static function getMetaById($tags)
    {
        $meta = array();
        $meta['meta_title'] = $tags.' | '.Configuration::get('XENFORUM_TITLE', (int)Context::getContext()->language->id);
        $meta['meta_description'] = Configuration::get('XENFORUM_DESC', (int)Context::getContext()->language->id);
        $meta['meta_keywords'] = Configuration::get('XENFORUM_KEYW', (int)Context::getContext()->language->id);

        return $meta;
    }
}
