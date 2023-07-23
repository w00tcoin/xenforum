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

function upgrade_module_1_2_4()
{
    $res = true;
    $sql = array();
    $res &= Configuration::deleteByName('XENFORUM_EDIT_LINK_REWRITE');

    $sql[] = 'ALTER TABLE `'._DB_PREFIX_.'xenforum_user`
                ADD `city` VARCHAR(64) NULL DEFAULT NULL AFTER `signature`,
                ADD `id_country` INT(10) NOT NULL DEFAULT 0 AFTER `city`,
                ADD `private` TINYINT(1) NOT NULL DEFAULT 0 AFTER `id_country`;';

    foreach ($sql as $query) {
        $res &= Db::getInstance()->execute($query);
    }

    return $res;
}
