<?php
/**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'xenforum` (
            `id_xenforum`  int(11) NOT NULL AUTO_INCREMENT,
            `id_xenforum_cat` int(11) DEFAULT 0,
            `id_author` int(11) DEFAULT NULL,
            `meta_title` varchar(255) DEFAULT NULL,
            `link_rewrite` varchar(255) DEFAULT NULL,
            `viewed` int(11) DEFAULT 0,
            `active` tinyint(1) DEFAULT 0,
            `closed` tinyint(1) DEFAULT 0,
            `highlight` tinyint(1) DEFAULT 0,
            `position` int(11) DEFAULT 0,
            `created` datetime NOT NULL,
            `modified` datetime DEFAULT NULL,
            PRIMARY KEY (`id_xenforum`)
    )ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'xenforum_post` (
            `id_xenforum_post` int(11) NOT NULL AUTO_INCREMENT,
            `id_xenforum` int(11) DEFAULT 0,
            `id_author` int(11) DEFAULT 0,
            `comment` text,
            `active` tinyint(1) DEFAULT 0,
            `created` datetime NOT NULL,
            PRIMARY KEY (`id_xenforum_post`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'xenforum_cat` (
            `id_xenforum_cat` int(11) NOT NULL AUTO_INCREMENT,
            `id_parent` int(10) DEFAULT 0,
            `position` int(11) DEFAULT 0,
            `private` TINYINT(1) NOT NULL DEFAULT 0,
            `closed` tinyint(1) NOT NULL DEFAULT 0,
            `active` tinyint(1) DEFAULT 0,
            `created` datetime NOT NULL,
            `modified` datetime DEFAULT NULL,
            PRIMARY KEY (`id_xenforum_cat`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'xenforum_cat_lang` (
            `id_xenforum_cat` int(11) NOT NULL,
            `id_lang` int(11) NOT NULL,
            `meta_title` varchar(125) DEFAULT NULL,
            `description` varchar(500) DEFAULT NULL,
            `link_rewrite` varchar(255) DEFAULT NULL,
            PRIMARY KEY (`id_xenforum_cat`,`id_lang`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'xenforum_cat_shop` (
            `id_xenforum_cat_shop`  int(11) NOT NULL AUTO_INCREMENT,
            `id_xenforum_cat` int(11) NOT NULL,
            `id_shop` int(11) NOT NULL,
            PRIMARY KEY (`id_xenforum_cat_shop`,`id_xenforum_cat`,`id_shop`)
    )ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'xenforum_group`(
            `id_xenforum_group` int(11) NOT NULL AUTO_INCREMENT,
            `title` varchar(50) DEFAULT NULL,
            `override_title` varchar(50) DEFAULT NULL,
            `style` varchar(20) DEFAULT NULL,
            PRIMARY KEY (`id_xenforum_group`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'xenforum_user`(
            `id_xenforum_user` int(11) NOT NULL DEFAULT 0,
            `nickname` varchar(50) DEFAULT NULL,
            `id_xenforum_group` int(11) DEFAULT 0,
            `signature` varchar(500) DEFAULT NULL,
            `city` varchar(64) DEFAULT NULL,
            `id_country` int(10) DEFAULT 0,
            `private` tinyint(1) DEFAULT 0,
            `approved` tinyint(1) DEFAULT 0,
            `notification` tinyint(1) DEFAULT 1,
            `is_staff` tinyint(1) DEFAULT 0,
            `points` int(11) DEFAULT 0,
            `date_approved` datetime DEFAULT NULL,
            `date_submited` datetime DEFAULT NULL,
            `last_access` datetime DEFAULT NULL,
    PRIMARY KEY (`id_xenforum_user`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'xenforum_group_rule`(
            `id_xenforum_group` int(11) NOT NULL DEFAULT 0,
            `id_rule` int(11) DEFAULT 0,
            `validation` varchar(1000) DEFAULT NULL,
    PRIMARY KEY (`id_xenforum_group`, `id_rule`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'xenforum_tags` (
            `id_topic` int(11) NOT NULL DEFAULT 0,
            `name` varchar(30) NOT NULL,
    PRIMARY KEY (`id_topic`, `name`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'xenforum_related` (
            `id_topic` int(11) NOT NULL DEFAULT 0,
            `id_product` int(11) NOT NULL DEFAULT 0,
            `position` int(11) DEFAULT 0,
            PRIMARY KEY (`id_product`, `id_topic`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'xenforum_like`(
            `id_post` int(11) NOT NULL DEFAULT 0,
            `id_user` int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id_post`, `id_user`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'xenforum_position`(
            `id_xenforum_position` int(11) NOT NULL AUTO_INCREMENT,
            `block` varchar(100) DEFAULT NULL,
            `position` varchar(100) DEFAULT NULL,
            `excepts` varchar(500) DEFAULT NULL,
            `sort_order` int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id_xenforum_position`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'xenforum_notification`(
            `id_notification` int(11) NOT NULL AUTO_INCREMENT,
            `id_user` int(11) NOT NULL DEFAULT 0,
            `id_visitor` int(11) NOT NULL DEFAULT 0,
            `action` varchar(30) DEFAULT NULL,
            `id_post` int(11) NOT NULL DEFAULT 0,
            `readn` tinyint(1) DEFAULT 0,
            `date_add` datetime DEFAULT NULL,
            PRIMARY KEY (`id_notification`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'xenforum_report`(
            `id_xenforum_report` int(11) NOT NULL AUTO_INCREMENT,
            `id_post` int(11) NOT NULL DEFAULT 0,
            `id_user` int(11) NOT NULL DEFAULT 0,
            `reason` varchar(1000) DEFAULT NULL,
            `date_add` datetime DEFAULT NULL,
            PRIMARY KEY (`id_xenforum_report`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'xenforum_attachment`(
            `id_xenforum_attachment` int(11) NOT NULL AUTO_INCREMENT,
            `id_user` int(11) NOT NULL DEFAULT 0,
            `name` varchar(100) DEFAULT NULL,
            `path` varchar(150) NOT NULL,
            `id_post` int(11) NULL DEFAULT 0,
            `date_add` datetime DEFAULT NULL,
            PRIMARY KEY (`id_xenforum_attachment`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
            return false;
    }
}
