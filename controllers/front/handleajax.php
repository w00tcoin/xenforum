<?php
/**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

include_once(dirname(__FILE__).'/../../classes/controllers/FrontController.php');
class XenForumHandleAjaxModuleFrontController extends XenForumModuleFrontController
{
    public $ssl = true;

    public function initContent()
    {
        parent::initContent();
        $this->ajax = true;
    }

    public function displayAjaxLike()
    {
        $bloguser = new XenForumUser();
        $id_post = (int)Tools::getvalue('id');
        $id_customer = $this->context->customer->id;

        $bloguser->switchLikeUnlike($id_post, $id_customer);
        $res = $bloguser->listUserLiked($id_post, $id_customer);
        die($res);
    }

    public function displayAjaxQuote()
    {
        $id_post = (int)Tools::getvalue('id');

        $res = XenForumPost::getCom($id_post);
        $txt = '[QUOTE="'.$res['nickname'].'"]'.xenforum::removeQuote($res['comment'], true).'[/QUOTE]';

        die($txt);
    }

    public function displayAjaxReport()
    {
        $id_post = (int)Tools::getvalue('id');
        $id_user = (int)$this->context->customer->id;
        $reason = Tools::getvalue('reason');
        $reporter = new XenForumReport();
        $reporter->id_post = $id_post;
        $reporter->id_user = $id_user;
        $reporter->reason = $reason;

        if ($reporter->add()) {
            $saved = true;
        } else {
            $saved = false;
        }

        $response = array(
            'saved' => $saved,
        );

        die(Tools::jsonEncode($response));
    }

    public function displayAjaxDelete()
    {
        $id_post = (int)Tools::getvalue('id');
        $blogcom = new XenForumPost($id_post);
        $user = new XenForumUser();
        $res = true;

        // Edit a Post
        $id_customer = (int)$this->context->customer->id;

        if ($id_post && $id_customer) {
            // Check permission to delete
            if ($user->checkPerm($id_customer, '_delete_all_')
            || (($id_customer == $blogcom->id_author) && $user->checkPerm($id_customer, '_delete_mine_'))) {
                $res = $blogcom->delete();
            }

            die($res);
        }
    }

    public function displayAjaxDeletenotif()
    {
        $id_notification = (int)Tools::getvalue('id');
        $blognotif = new XenForumNotification($id_notification);
        $res = $blognotif->delete();

        die($res);
    }

    public function displayAjaxUpload()
    {
        $user = new XenForumUser();
        $id_customer = (int)$this->context->customer->id;
        if (!$user->checkPerm($id_customer, '_allowed_upload_')) {
            $this->errors[] = Tools::displayError('Do not have permission!');
        } else {
            $id_user = (int)Tools::getvalue('id');
            $id_post = (int)Tools::getvalue('id_post');
            if ($image = $this->processImage($_FILES['images'], $id_user)) {
                $attachment = new XenForumAttachment();
                $attachment->id_user = $id_user;
                $attachment->id_post = $id_post;
                $attachment->name = $image['name'];
                $attachment->path = $image['abs_path'];

                // Update image array
                if ($attachment->add()) {
                    $image['id'] = $attachment->id;
                }

                die(Tools::jsonEncode(
                    array(
                        'image' => $image
                    )
                ));
            }
        }

        // If error
        die(Tools::jsonEncode($this->errors));
    }

    public function processImage($files, $id)
    {
        if (isset($files) && isset($files['tmp_name']) && !empty($files['tmp_name'])) {
            if (ImageManager::validateUpload($files, 2048000, array('jpg', 'gif', 'png'))) {
                $this->errors[] = sprintf(Tools::displayError('Invalid image! We only allow filetype: %s, max-size: %s'), 'JPG|GIF|PNG', '2MB');
                return false;
            }

            $path_parts = pathinfo($files['name']);
            //$base = $path_parts['basename'];
            $real_name = $path_parts['filename'];
            $ext = $path_parts['extension'];
            $new_filename = $id.'_'.time().'_'.$real_name.'.'.$ext;
            $new_dir = _MODULE_XENFORUM_UPLOAD_DIR_.$new_filename;

            if (@move_uploaded_file($files['tmp_name'], $new_dir)) {
                return array(
                    'name' => $files['name'],
                    'abs_path' => _MODULE_XENFORUM_UPLOAD_URI_.$new_filename,
                    'full_path' => _PS_BASE_URL_._MODULE_XENFORUM_UPLOAD_URI_.$new_filename
                );
            }
        }

        return false;
    }
    
    public function displayAjaxDeleteAttachment()
    {
        $user = new XenForumUser();
        $id_customer = (int)$this->context->customer->id;
        if (!$user->checkPerm($id_customer, '_allowed_upload_')) {
            $this->errors[] = Tools::displayError('Do not have permission!');
        } else {
            $id_attachment = (int)Tools::getvalue('id');
            $attachment = new XenForumAttachment($id_attachment);
            if ($attachment->delete()) {
                die(Tools::jsonEncode(
                    array(
                        'result' => 'OK'
                    )
                ));
            } else {
                $this->errors[] = Tools::displayError('Error on deleting!');
            }
        }

        // If error
        die(Tools::jsonEncode($this->errors));
    }
}
