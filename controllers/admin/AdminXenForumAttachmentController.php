<?php
/**
* Prestashop Addons | Module by: <App1pro>
*
* @author    Chuyen Nguyen [App1pro].
* @copyright Chuyenim@gmail.com
* @license   http://app1pro.com/license.txt
*/

class AdminXenForumAttachmentController extends ModuleAdminController
{
    public $id_xenforum_attachment;

    public function __construct()
    {
        $this->table = 'xenforum_attachment';
        $this->className = 'XenForumAttachment';
        $this->lang = false;
        $this->bootstrap = true;
        $this->_defaultOrderBy = 'date_add';
        $this->_defaultOrderWay = 'DESC';
        $this->context = Context::getContext();
        parent::__construct();

        $this->fields_list = array(
                'id_xenforum_attachment' => array(
                    'title' => $this->l('ID'),
                    'width' => 50,
                    'type' => 'text',
                    'align' => 'center',
                ),
                'thumbnail' => array(
                    'title' => $this->l('Thumbnail'),
                    'width' => 50,
                    'callback' => 'displayThumbnail',
                    'orderby' => false,
                    'filter' => false,
                    'search' => false
                ),
                'nickname' => array(
                    'title' => $this->l('Uploaded by'),
                    'width' => 100,
                    'type' => 'text',
                ),
                'name' => array(
                    'title' => $this->l('Name'),
                    'width' => 100,
                    'type' => 'text',
                ),
                'path' => array(
                    'title' => $this->l('Path'),
                    'width' => 300,
                    'type' => 'text'
                ),
                'id_post' => array(
                    'title' => $this->l('#Post Id'),
                    'width' => 100,
                    'type' => 'text',
                ),
                'date_add' => array(
                    'title' => $this->l('Date'),
                    'width' => 100,
                    'type' => 'datetime'
                )
        );
    }

    public function setMedia($isNewTheme = false)
    {
        //$this->addCSS(array(_MODULE_DIR_.$this->module.'/views/css/admin.css'));
        return parent::setMedia();
    }

    public function displayThumbnail($thumbnail = null)
    {
        $image = explode(';', $thumbnail);
        $tpl = $this->createTemplate('list_display_thumbnail.tpl');
        $tpl->assign(array(
            'thumbnail_path' => $image[0],
            'thumbnail_alt' => $image[1],
        ));

        return $tpl->fetch();
    }

    public function renderList()
    {
        $this->addRowAction('preview');
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->toolbar_btn = array();

        $this->_select = 'c.nickname as nickname, CONCAT(a.path, ";", a.name) as thumbnail';
        $this->_join = 'LEFT JOIN '._DB_PREFIX_.'xenforum_user c ON (a.id_user = c.id_xenforum_user)';

        return parent::renderList();
    }

    public function displayPreviewLink($token = null, $id = 0, $name = null)
    {
        $tpl = $this->createTemplate('list_action_preview.tpl');
        if (!array_key_exists('Bad SQL query', self::$cache_lang)) {
            self::$cache_lang['Preview'] = $this->l('Preview', 'Helper');
        }
        $attachment = new XenForumAttachment($id);

        $tpl->assign(array(
            'href' => $attachment->path,
            'action' => self::$cache_lang['Preview'],
            'token' => $token,
            'name' => $name,
        ));

        return $tpl->fetch();
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('Edit Attachment'),
                'icon' => 'icon-plus'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('User ID'),
                    'name' => 'id_user',
                    'size' => 50,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'size' => 50,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Path'),
                    'name' => 'path',
                    'size' => 50,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Post ID'),
                    'name' => 'id_post',
                    'size' => 50,
                    'required' => false
                )
            )
        );

        $this->fields_form['submit'] = array(
            'title' => $this->l('Save'),
        );

        if (!($this->loadObject(true))) {
            return;
        }

        // generate Form
        return parent::renderForm();
    }
}
