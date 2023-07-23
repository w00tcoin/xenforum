<?php
/**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

include_once(dirname(__FILE__).'/../../classes/controllers/FrontController.php');
class XenForumHelpModuleFrontController extends XenForumModuleFrontController
{
    public $ssl = true;

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = array(
            'title' => $this->l('Helps'),
            'url' => null,
        );
        return $breadcrumb;
    }

    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign(array(
            'help_content'      => Configuration::get('XENFORUM_HELP', $this->context->language->id)
        ));

        if (version_compare(_PS_VERSION_, '1.7.0.0 ', '>=')) {
            $this->setTemplate('module:xenforum/views/templates/front/ps17-help.tpl');
        } else {
            $this->setTemplate('help.tpl');
        }
    }
}
