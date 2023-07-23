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

function upgrade_module_2_0_0()
{
    $res = true;
    $sql = array();
    $sql[] = 'ALTER TABLE `'._DB_PREFIX_.'xenforum_position`
                ADD `sort_order` INT(11) NOT NULL DEFAULT 0 AFTER `excepts`;';

    foreach ($sql as $query) {
        $res &= Db::getInstance()->execute($query);
    }

    return $res;
}
