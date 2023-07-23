<?php
/**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_2_9()
{
    $res = true;
    $sql = array();
    $sql[] = 'ALTER TABLE `'._DB_PREFIX_.'xenforum_user`
                ADD `approved` tinyint(1) DEFAULT 0 AFTER `private`,
                ADD `date_approved` datetime DEFAULT NULL AFTER `points`,
                ADD `date_submited` datetime DEFAULT NULL AFTER `date_approved`;';

    foreach ($sql as $query) {
        $res &= Db::getInstance()->execute($query);
    }

    return $res;
}
