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

function upgrade_module_2_4_0()
{
    $res = true;
    $xenforum = new XenForum();
    $languages = Language::getLanguages(false);
    $sql = array();

    $res &= $xenforum->registerHook('displayFooterProduct');
    
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
        $res &= Db::getInstance()->execute($query);
    }
    
    $tab_id = Tab::getIdFromClassName('AdminXenForum');
    if ($tab_id) {
        $tab = array(
            'class_name' => 'AdminXenForumAttachment',
            'name' => 'Attachments',
        );
        $newtab = new Tab();
        $newtab->class_name = $tab['class_name'];
        $newtab->id_parent = $tab_id;
        $newtab->module = $xenforum->name;

        foreach ($languages as $lang) {
            $newtab->name[$lang['id_lang']] = $tab['name'];
        }
        $res &= $newtab->save();
    }

    return $res;
}
