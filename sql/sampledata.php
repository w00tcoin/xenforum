<?php
/**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

$sql = array();
$created = Date('Y-m-d H:i:s');

$sql[] = 'INSERT INTO `'._DB_PREFIX_.'xenforum_group` (`id_xenforum_group`, `title`, `override_title`) VALUES
        (1, "Registered", "Member"),
        (2, "Administrator", "")';

$sql[] = 'INSERT INTO `'._DB_PREFIX_.'xenforum_group_rule` (`id_xenforum_group`, `id_rule`, `validation`) VALUES
        (1, 2, ""),
        (1, 3, "all"),
        (1, 4, ""),
        (1, 6, ""),
        (1, 8, ""),
        (1, 9, ""),
        (1, 10, ""),
        (1, 15, "");';

/* Position - blocks */
$sql[] = 'INSERT INTO `'._DB_PREFIX_.'xenforum_position` (`id_xenforum_position`, `block`, `position`, `excepts`) VALUES
        (1, "account", "left", "member, details, topicedit, topiccreate, editpost"),
        (2, "search", "left", "details, topicedit, topiccreate, editpost"),
        (3, "addtopic", "left", "category, member, tags, search, details, topicedit, topiccreate, editpost, term, help"),
        (4, "statistics", "left", "details, topicedit, topiccreate, editpost"),
        (5, "latest", "left", "details, topicedit, topiccreate, editpost")';

/* Categories */
$sql[] = 'INSERT INTO `'._DB_PREFIX_.'xenforum_cat` (`id_xenforum_cat`, `id_parent`, `position`, `active`, `created`) VALUES
        (1, 0, 0, 1, "'.pSQL($created).'"),
        (2, 1, 1, 1, "'.pSQL($created).'"),
        (3, 1, 2, 1, "'.pSQL($created).'"),
        (4, 0, 3, 1, "'.pSQL($created).'"),
        (5, 4, 4, 1, "'.pSQL($created).'"),
        (6, 4, 5, 1, "'.pSQL($created).'"),
        (7, 4, 6, 1, "'.pSQL($created).'");';

$languages = Language::getLanguages(false);
foreach ($languages as $language) {
    $sql[] = 'INSERT INTO `'._DB_PREFIX_.'xenforum_cat_lang` (`id_xenforum_cat`, `id_lang`, `meta_title`, `description`, `link_rewrite`) VALUES
            (1, '.(int)$language['id_lang'].', "Official Forums", "", "official-forums"),
            (2, '.(int)$language['id_lang'].', "Announcements", "", "announcements"),
            (3, '.(int)$language['id_lang'].',
                "Frequently Asked Questions", "Answers to various questions you may have. Frequently updated.", "frequently-asked-questions"),
            (4, '.(int)$language['id_lang'].', "Public Forums", "", "public-forums"),
            (5, '.(int)$language['id_lang'].', "Main Discussions", "", "main-discussions"),
            (6, '.(int)$language['id_lang'].', "Pre-Sales Questions", "", "pre-sales-questions"),
            (7, '.(int)$language['id_lang'].', "After-sales service", "", "after-sales-service");';
}

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
