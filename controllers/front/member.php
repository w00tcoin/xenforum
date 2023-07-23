<?php
/**
* Prestashop Addons | Module by: <App1Pro>
*
* @author    Chuyen Nguyen [App1Pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

include_once(dirname(__FILE__).'/../../classes/controllers/FrontController.php');
class XenForumMemberModuleFrontController extends XenForumModuleFrontController
{
    public $ssl = true;
    public $width = 156;
    public $height = 156;

    public function setMedia()
    {
        parent::setMedia();
        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/member.js', 'all');
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $id_member = Tools::getValue('id_customer');
        $id_online = $this->context->customer->id;
        if ($id_member) {
            $realuser = new XenForumUser();
            $id_member = $realuser->getRealID($id_member);
        }
        if (!$id_member && $id_online) {
            $id_member = $id_online;
        }

        $bloguser = new XenForumUser($id_member);
        $customer = $bloguser->getUser();
        
        $breadcrumb['links'][] = array(
            'title' => $customer['nickname'],
            'url' => null,
        );
        return $breadcrumb;
    }

    public function postProcess()
    {
        $this->className = 'XenForumUser';
        $this->table = 'xenforum_user';
        $cookie = Context::getContext()->cookie;
        $id_online = $this->context->customer->id;
        $id_member = Tools::getValue('id_customer');
        if ($id_member) {
            $realuser = new XenForumUser();
            $id_member = $realuser->getRealID($id_member);
        }
        if (!$id_member && $id_online) {
            $id_member = $id_online;
        }

        $url = addcslashes(xenforum::GetLink(Configuration::get('XENFORUM_URL')), "'");

        // Edit a Post
        if ((Tools::isSubmit('update_member')) && ($id_member) && isset($cookie->token) && ($cookie->token == Tools::getValue('token'))) {
            $bloguser = new XenForumUser($id_member);
            $this->processImage($_FILES['avatar'], $id_member);
            $bloguser->city = xenforum::xfAddSlashes(Tools::getValue('city'));
            $bloguser->id_country = xenforum::xfAddSlashes(Tools::getValue('id_country'));
            $bloguser->notification = Tools::getValue('notification');
            $bloguser->private = Tools::getValue('private');
            $bloguser->nickname = xenforum::xfAddSlashes(Tools::getValue('nickname'));
            $bloguser->signature = xenforum::xfAddSlashes(Tools::getValue('signature'));

            $this->checkValidation();
            if (empty($this->errors)) {
                if ($bloguser->update()) {
                    Tools::redirect($url);
                }
            }
        } elseif ((Tools::isSubmit('submit_register')) && ($id_member) && isset($cookie->token) && ($cookie->token == Tools::getValue('token'))) {
            $bloguser = new XenForumUser($id_member);
            $bloguser->nickname = xenforum::xfAddSlashes(Tools::getValue('nickname'));
            $bloguser->date_submited = date('Y-m-d H:i:s');

            if (!Configuration::get('XENFORUM_WAIT_APPROVE')) {
                $bloguser->approved = true;
                $bloguser->date_approved = date('Y-m-d H:i:s');
            }

            $this->checkValidation();
            if (empty($this->errors)) {
                if (isset($bloguser->id)) {
                    $bloguser->update();
                } else {
                    $bloguser->id = $id_member;
                    $bloguser->id_xenforum_user = $id_member;
                    if ($bloguser->add()) {
                        // Send email to administrator
                        $var_list = array(
                            '{admin}'    => 'Administrator',
                            '{nickname}' => $bloguser->nickname
                        );
                        $this->sendRegisterMail('Message from forum', $var_list);
                    };
                }
            }
        }
    }
    
    public function initContent()
    {
        parent::initContent();
        $cookie = Context::getContext()->cookie;
        $token = Tools::passwdGen(12);
        $id_online = $this->context->customer->id;
        $id_member = Tools::getValue('id_customer');
        if ($id_member) {
            $realuser = new XenForumUser();
            $id_member = $realuser->getRealID($id_member);
        }
        if (!$id_member && $id_online) {
            $id_member = $id_online;
        }
        $notif = new XenForumNotification();
        $notif->id_user = $id_member;

//        if ($this->private) {
//            if (version_compare(_PS_VERSION_, '1.7.0.0 ', '>=')) {
//                $this->setTemplate('module:xenforum/views/templates/front/ps17-private_msg.tpl');
//            } else {
//                $this->setTemplate('private_msg.tpl');
//            }
//            return;
//        }

        $cookie->__set('token', $token);
        $this->context->smarty->assign('token', $token);
        // End add a new post

        $bloguser = new XenForumUser($id_member);
        $customer = $bloguser->getUser();
        $notifications = $notif->getNotification();
        $total_unread = $notif->getUnread();
        $id_lang = $this->context->language->id;
        $countries = Country::getCountries($id_lang);
        if ($customer) {
            $customer = (object)$customer;
            if ($customer->id_country == null || !isset($countries[$customer->id_country])) {
                $customer->id_country = (int)$this->context->country->id;
            }

            $this->context->smarty->assign(array(
                'xen_notifications'     => $notifications,
                'total_unread'      => $total_unread,
                'countries'         => $countries,
                'customer'          => $customer,
                'id_member'         => $id_member,
                'title'             => ($customer->override_title) ? $customer->override_title : $customer->title,
                'errors'            => $this->errors,
            ));
        }

        if ($id_online && !$bloguser->approved && $id_online == $id_member) {
            $online = new Customer($id_online);
            $online->nickname = $online->firstname.' '.$online->lastname;
            $this->context->smarty->assign(array(
                'online'          => $online,
                'term_content'    => Configuration::get('XENFORUM_TERM', $this->context->language->id),
            ));
            // $this->setTemplate('register.tpl');
            if (version_compare(_PS_VERSION_, '1.7.0.0 ', '>=')) {
                $this->setTemplate('module:xenforum/views/templates/front/ps17-register.tpl');
            } else {
                $this->setTemplate('register.tpl');
            }
        } else {
            // $this->setTemplate('member.tpl');
            if (version_compare(_PS_VERSION_, '1.7.0.0 ', '>=')) {
                $this->setTemplate('module:xenforum/views/templates/front/ps17-member.tpl');
            } else {
                $this->setTemplate('member.tpl');
            }
        }
    }

    private function sendRegisterMail($title = null, $var_list = null)
    {
        $id_lang = $this->context->language->id;
        $email_to = Configuration::get('PS_SHOP_EMAIL');

        if (Validate::isEmail($email_to)) {
            Mail::Send(
                (int)$id_lang,
                'user_register',
                Mail::l($title, (int)$id_lang),
                $var_list,
                $email_to,
                null,
                null,
                null,
                null,
                null,
                dirname(__FILE__).'/../../mails/'
            );
        }
    }

    public function processImage($files, $id)
    {
        if (isset($files) && isset($files['tmp_name']) && !empty($files['tmp_name'])) {
            if (ImageManager::validateUpload($files, 4096000, array('jpg'))) {
                $this->errors[] = sprintf(Tools::displayError('Invalid image! We only allow filetype: %s, max size: %s'), '.jpg', '4MB');
                return false;
            }

            $path_parts = pathinfo($files['name']);
            //$base = $path_parts['basename'];
            //$name = $path_parts['filename'];
            $ext = $path_parts['extension'];
            $new_ext = 'jpg';
            // Source information
            $src_info = getimagesize($files['tmp_name']);
                $width = $src_info[0];
                $height = $src_info[1];

            $dst_x = 0;
            $dst_y = 0;
            if ($width >= $height) {
                $dst_x = floor(($width - $height) / 2);
                $width = $height;
            } else {
                $dst_y = floor(($height - $width) / 2);
            }

            $new_tmp = _MODULE_XENFORUM_AVT_DIR_.$id.'-tmp.'.$ext;
            $new_dir = _MODULE_XENFORUM_AVT_DIR_.$id.'.'.$new_ext;
            if (file_exists($new_tmp)) {
                unlink($new_tmp);
            }

            if (file_exists($new_dir)) {
                unlink($new_dir);
            }

            if (!(ImageManager::cut(
                $files['tmp_name'],
                $new_tmp,
                $width,
                $width,
                'jpg',
                $dst_x,
                $dst_y
            ) && ImageManager::resize(
                $new_tmp,
                $new_dir,
                (int)$this->width,
                (int)$this->height
            ) && unlink($new_tmp))) {
                $this->errors[] = Tools::displayError('An error occurred while attempting to upload the file.');
                return false;
            }
        }

        return true;
    }
}
