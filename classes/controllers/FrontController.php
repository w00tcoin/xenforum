<?php
/**
* Prestashop Addons | Module by: <App1Pro>
*
* @author    Chuyen Nguyen [App1Pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

class XenForumModuleFrontController extends ModuleFrontController
{
    public $private = false;

    public function setMedia()
    {
        parent::setMedia();
        $this->addJquery();
        $this->addjqueryPlugin('fancybox');
        // $lang_iso_code = $this->context->language->iso_code;
        // $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/main.css', 'all');
        // $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/style.css', 'all');
        // $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/custom.css', 'all');
        //$this->context->controller->addCSS(($this->_path).'views/css/product_list.css', 'all');
        // $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/jquery.timeago.min.js');
        // $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/locales/jquery.timeago.'.$lang_iso_code.'.js');
        //$this->addJS(_PS_JS_DIR_.'tools.js', false);
        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/xenforum.min.js');
    }

    public function getLayout()
    {
        if (method_exists($this, 'getPageName')) {
            $this->php_self = $this->getPageName();
        }
        return parent::getLayout();
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = array(
            'title' => Configuration::get('XENFORUM_TITLE', $this->context->language->id),
            'url' => $this->module->GetLink(Configuration::get('XENFORUM_URL')),
        );

        return $breadcrumb;
    }

    public function initContent()
    {
        parent::initContent();
        $meta = array();
        $id_lang = $this->context->language->id;
        $id_online = (int)$this->context->customer->id;
        $id_customer = Tools::getvalue('id_customer');
        // $page_url = Configuration::get('XENFORUM_URL');
        $private_mode = Configuration::get('XENFORUM_PRIVATE');
        $controller = Tools::getvalue('controller');
        $search = Tools::getvalue('search');
        $xenforum = new XenForum();
        $user = new XenForumUser($id_online);
        $cookie = Context::getContext()->cookie;
        if ($this->context->customer->isLogged()) {
            if (!isset($cookie->b2b_marketplace_session)) {
                // New session login -> set new cookie and save last_access to DB.
                $cookie->setExpire(0);
                $cookie->__set('b2b_marketplace_session', $id_online);
                $user->saveAccess();
            }
        } else {
            $cookie->__unset('b2b_marketplace_session');
        }

        // Check if forum is in private and responsive
        if ((int)$private_mode && ($controller != 'help' && $controller != 'term'
            && !($controller == 'member' && $id_customer == $id_online))
            && (!$user->checkPerm($id_online, '_view_all_'))) {
            // $redirect_home = $xenforum->getLink($page_url);
            // Tools::redirect($redirect_home);
            $this->private = true;
        }

        if ($search) {
            $search = trim($search);
            $search = str_replace(' ', '+', $search);
            $option = array('key' => $search);
            $redirect_search = $xenforum->getLink('xenforum_search', $option);
            Tools::redirect($redirect_search);
        }

        if ((int)$id_xenforum_cat = Tools::getvalue('id_xenforum_cat')) {
            $this->context->smarty->assign(XenForumCat::getMetaById($id_xenforum_cat));
        } elseif ((int)$id_xenforum = Tools::getvalue('id_xenforum')) {
            $this->context->smarty->assign(XenForumTopic::getMetaById($id_xenforum));
        } elseif ($tags = $id_xenforum = Tools::getvalue('tags')) {
            $this->context->smarty->assign(XenForumTags::getMetaById($tags));
        } else {
            $meta['meta_title']         = xenforum::xfStripslashes(Configuration::get('XENFORUM_TITLE', $this->context->language->id));
            $meta['meta_description']   = Configuration::get('XENFORUM_DESC', $this->context->language->id);
            $meta['meta_keywords']      = Configuration::get('XENFORUM_KEYW', $this->context->language->id);
            $this->context->smarty->assign($meta);
        }

        $iso_code = $this->context->language->iso_code;
        $tinymce_lang_link = null;
        $path_dir = dirname(__FILE__).'/../../libraries/tinymce/langs/';
        $path_uri = _PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->module->name.'/libraries/tinyMCE/langs/';
        if ($iso_code != 'en' && file_exists($path_dir.$iso_code.'.js')) {
            $tinymce_lang_link = $path_uri.$iso_code.'.js';
        }

        $this->context->smarty->assign(array(
            'js_theme_cache'    => Configuration::get('PS_JS_THEME_CACHE'),
            'page_url'          => Configuration::get('XENFORUM_URL'),
            'page_title'        => Configuration::get('XENFORUM_TITLE', $id_lang),
            'private'           => $private_mode,
            'xenforum_show_lcolumn'  => Configuration::get('XENFORUM_SHOW_LCOLUMN'),
            'xenforum_show_rcolumn'  => Configuration::get('XENFORUM_SHOW_RCOLUMN'),
            'xenforum'          => $xenforum,
            'user'              => $user,
            'tinymce_lang_link' => $tinymce_lang_link,
            'id_lang'           => $this->context->language->id,
            'id_online'         => $this->context->customer->id
        ));
        
        //pd($this->getTemplateVarPage());

        if ((int)Configuration::get('XENFORUM_SHOW_LCOLUMN')) {
            $this->context->smarty->assign(array(
                'HOOK_LEFT_COLUMN' => Hook::exec('displayXenForumLeft')
            ));
        }

        if ((int)Configuration::get('XENFORUM_SHOW_RCOLUMN')) {
            $this->context->smarty->assign(array(
                'HOOK_RIGHT_COLUMN' => Hook::exec('displayXenForumRight')
            ));
        }
    }

    public function checkValidation()
    {
        $rules = call_user_func(array($this->className, 'getValidationRules'), $this->className);
        $default_language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $languages = Language::getLanguages(false);

        // Check required fields
        foreach ($rules['required'] as $field) {
            if (($value = Tools::getValue($field)) == false && $value != '0') {
                if (Tools::getValue('id_'.$this->table) && $field == 'passwd') {
                    continue;
                }
                $this->errors[] = sprintf(
                    Tools::displayError('The %s field is required.'),
                    call_user_func(array($this->className, 'displayFieldName'), $field, $this->className)
                );
            }
        }

        // Check multilingual required fields
        foreach ($rules['requiredLang'] as $fieldLang) {
            if (!Tools::getValue($fieldLang.'_'.$default_language->id)) {
                $this->errors[] = sprintf(
                    Tools::displayError('This %1$s field is required at least in %2$s'),
                    call_user_func(array($this->className, 'displayFieldName'), $fieldLang, $this->className),
                    $default_language->name
                );
            }
        }

        // Check fields validity
        foreach ($rules['validate'] as $field => $function) {
            if (($value = Tools::getValue($field))) {
                $res = true;
                if (Tools::strtolower($function) == 'iscleanhtml') {
                    if (!Validate::{$function}($value, (int)Configuration::get('PS_ALLOW_HTML_IFRAME'))) {
                        $res = false;
                    }
                } elseif (!Validate::{$function}($value)) {
                    $res = false;
                }

                if (!$res) {
                    $this->errors[] = sprintf(
                        Tools::displayError('The %s field is invalid.'),
                        call_user_func(array($this->className, 'displayFieldName'), $field, $this->className)
                    );
                }
            }
        }

        // Check multilingual fields validity
        foreach ($rules['validateLang'] as $fieldLang => $function) {
            foreach ($languages as $language) {
                if (($value = Tools::getValue($fieldLang.'_'.$language['id_lang']))) {
                    if (!Validate::{$function}($value, (int)Configuration::get('PS_ALLOW_HTML_IFRAME'))) {
                        $this->errors[] = sprintf(
                            Tools::displayError('The %1$s field (%2$s) is invalid.'),
                            call_user_func(array($this->className, 'displayFieldName'), $fieldLang, $this->className),
                            $language['name']
                        );
                    }
                }
            }
        }
    }
}
