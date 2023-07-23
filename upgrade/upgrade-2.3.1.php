<?php
/**
* Prestashop Addons | Module by: <App1Pro>
*
* @author    Chuyen Nguyen [App1Pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_3_1()
{
    $res = true;
    $sql = array();

    $result = Db::getInstance()->query('SHOW COLUMNS FROM `'._DB_PREFIX_.'xenforum_cat` LIKE "private"');
    $col_exist = (!empty($result)) ? true : false;
    if (!$col_exist) {
        $sql[] = 'ALTER TABLE `'._DB_PREFIX_.'xenforum_cat` ADD `private` TINYINT(1) NOT NULL DEFAULT 0 AFTER `position`;';
        foreach ($sql as $query) {
            $res &= Db::getInstance()->execute($query);
        }
    }
    
    return $res;
}
