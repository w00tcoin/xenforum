<?php
/**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

$ppsql = array();
$ppsql[] = 'DROP TABLE IF EXISTS  `'._DB_PREFIX_.'xenforum_cat`;';
$ppsql[] = 'DROP TABLE IF EXISTS  `'._DB_PREFIX_.'xenforum_cat_lang`;';
$ppsql[] = 'DROP TABLE IF EXISTS  `'._DB_PREFIX_.'xenforum_cat_shop`;';

$ppsql[] = 'DROP TABLE IF EXISTS  `'._DB_PREFIX_.'xenforum`;';
$ppsql[] = 'DROP TABLE IF EXISTS  `'._DB_PREFIX_.'xenforum_post`;';
$ppsql[] = 'DROP TABLE IF EXISTS  `'._DB_PREFIX_.'xenforum_tags`;';
$ppsql[] = 'DROP TABLE IF EXISTS  `'._DB_PREFIX_.'xenforum_group`;';
$ppsql[] = 'DROP TABLE IF EXISTS  `'._DB_PREFIX_.'xenforum_user`;';
$ppsql[] = 'DROP TABLE IF EXISTS  `'._DB_PREFIX_.'xenforum_group_rule`;';
$ppsql[] = 'DROP TABLE IF EXISTS  `'._DB_PREFIX_.'xenforum_like`;';
$ppsql[] = 'DROP TABLE IF EXISTS  `'._DB_PREFIX_.'xenforum_related`;';
$ppsql[] = 'DROP TABLE IF EXISTS  `'._DB_PREFIX_.'xenforum_position`;';
$ppsql[] = 'DROP TABLE IF EXISTS  `'._DB_PREFIX_.'xenforum_notification`;';
$ppsql[] = 'DROP TABLE IF EXISTS  `'._DB_PREFIX_.'xenforum_report`;';
$ppsql[] = 'DROP TABLE IF EXISTS  `'._DB_PREFIX_.'xenforum_attachment`;';

foreach ($ppsql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
