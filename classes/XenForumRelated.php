<?php
/**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

class XenForumRelated extends ObjectModel
{
    public $id_topic;
    public $id_product;
    public $position = 0;

    public static $definition = array(
        'table' => 'xenforum_related',
        'primary' => 'id_topic',
        'multilang'=>false,
        'fields' => array(
            'id_topic'          => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_product'        => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'position'          => array('type' => self::TYPE_INT, 'validate' => 'isInt')
        ),
    );

    public function getTopics($id_product = null)
    {
        $topics = array();
        $tp = new XenForumTopic();
        $list = Db::getInstance()->executeS('SELECT DISTINCT `id_topic`
                FROM `'._DB_PREFIX_.'xenforum_related`
                WHERE `id_product` = '.(int)$id_product.'
                ORDER BY `position` ASC');
        foreach ($list as $key => $row) {
            $row = $tp->getPost($row['id_topic']);
            if ($row['active']) {
                $topics[$key] = $row;
            }
        }
        
        return $topics;
    }

    public static function deleteProducts($id_topic = null)
    {
        return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'xenforum_related` WHERE `id_topic` = '.(int)$id_topic);
    }

    public static function addProducts($id_topic, $string_products = null)
    {
        $result = true;
        $product_ids = explode(',', strip_tags($string_products));

        if (is_array($product_ids)) {
            foreach ($product_ids as $key => $id_product) {
                $product = new Product($id_product);
                $product = new Product((int)$id_product);
                if (!isset($product->id)) {
                    continue;
                }

                $result &= Db::getInstance()->execute('
                REPLACE INTO `'._DB_PREFIX_.'xenforum_related` (`id_topic`, `id_product`, `position`)
                VALUES ('.(int)$id_topic.', '.(int)$id_product.', '.(int)$key.');');
            }
        }

        return $result;
    }
    
    public static function getProducts($id_topic = null)
    {
        $product_list = array();
        $list = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT `id_product` FROM `'._DB_PREFIX_.'xenforum_related`
            WHERE `id_topic` = '.(int)$id_topic.'
            ORDER BY `position` ASC;');
        if (!empty($list)) {
            foreach ($list as $row) {
                $product_list[] = $row['id_product'];
            }
        }

        return $product_list;
    }
}
