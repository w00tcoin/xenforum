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

function upgrade_module_2_7_0($module)
{
    $res = true;
    $res &= Configuration::updateValue('XENFORUM_FB_COMMENT', 0);
    $res &= Configuration::updateValue('XENFORUM_NATIVE_COM_OFF', 0);
    $res &= Configuration::updateValue('XENFORUM_FB_APP_ID', '');

    return $res;
}
