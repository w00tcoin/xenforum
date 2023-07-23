<?php
/**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

$idtabs = array();
$idtabs[] = Tab::getIdFromClassName('AdminXenForum');
$idtabs[] = Tab::getIdFromClassName('AdminXenForumTopic');
$idtabs[] = Tab::getIdFromClassName('AdminXenForumCategory');
$idtabs[] = Tab::getIdFromClassName('AdminXenForumPost');
$idtabs[] = Tab::getIdFromClassName('AdminXenForumUser');
$idtabs[] = Tab::getIdFromClassName('AdminXenForumGroup');
$idtabs[] = Tab::getIdFromClassName('AdminXenForumPosition');
$idtabs[] = Tab::getIdFromClassName('AdminXenForumReport');
$idtabs[] = Tab::getIdFromClassName('AdminXenForumAttachment');

foreach ($idtabs as $tabid) {
    if ($tabid) {
        $tab = new Tab($tabid);
        $tab->delete();
    }
}
