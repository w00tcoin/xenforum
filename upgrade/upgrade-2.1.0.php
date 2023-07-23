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

function upgrade_module_2_1_0()
{
    $res = true;
    $sql = array();
    $res &= Configuration::updateValue('XENFORUM_REPORTS_MAX', 5);

    $sql[] = 'ALTER TABLE `'._DB_PREFIX_.'xenforum_group_rule` ADD `validation` varchar(1000) DEFAULT NULL AFTER `id_rule`;';
    $sql[] = 'ALTER TABLE `'._DB_PREFIX_.'xenforum_user` ADD `last_access` DATETIME NULL AFTER `date_submited`;';

    $sql[] = 'UPDATE `'._DB_PREFIX_.'xenforum_group_rule` SET `validation` = "all"
                WHERE `id_rule` = 3;';

    $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'xenforum_report`(
                `id_xenforum_report` int(11) NOT NULL auto_increment,
                `id_post` int(11) NOT NULL DEFAULT 0,
                `id_user` int(11) NOT NULL DEFAULT 0,
                `reason` varchar(1000) DEFAULT NULL,
                `date_add` datetime DEFAULT NULL,
                PRIMARY KEY (`id_xenforum_report`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

    foreach ($sql as $query) {
        $res &= Db::getInstance()->execute($query);
    }

    return $res;
}
