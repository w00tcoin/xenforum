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

function upgrade_module_1_2_0()
{
    $res = true;
    $sql = array();
    $res &= Configuration::updateValue('XENFORUM_TERM', '');
    $res &= Configuration::deleteByName('XENFORUM_AUTO_ACCEPT_TOPIC');
    $res &= Configuration::deleteByName('XENFORUM_AUTO_ACCEPT_COM');

    $sql[] = 'DROP TABLE IF EXISTS  `'._DB_PREFIX_.'xenforum_rule`;';
    $sql[] = 'INSERT INTO `'._DB_PREFIX_.'xenforum_group_rule` (`id_xenforum_group`, `id_rule`) VALUES
            (1, 10),
            (2, 9),
            (2, 10);';

    foreach ($sql as $query) {
        $res &= Db::getInstance()->execute($query);
    }

    return $res;
}
