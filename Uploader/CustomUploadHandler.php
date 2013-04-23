<?php

namespace Vx\JsUploadBundle\Uploader;

class CustomUploadHandler extends UploadHandler
{

    private $delete_url;

    protected static function getFullUrl() {
        $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        return
            ($https ? 'https://' : 'http://').
            (!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'].'@' : '').
            (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'].
            ($https && $_SERVER['SERVER_PORT'] === 443 ||
            $_SERVER['SERVER_PORT'] === 80 ? '' : ':'.$_SERVER['SERVER_PORT']))).
            substr($_SERVER['SCRIPT_NAME'],0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
    }

    public static function getDefaultOptions()
    {
        return array(
            'erase_file' => false,
            'filename' => null,
            'upload_dir' => dirname($_SERVER['SCRIPT_FILENAME']).'/uploads/',
            'upload_url' => CustomUploadHandler::getFullUrl().'/uploads/',
            );
    }

    public function __construct($delete_url, $options)
    {
        $this->delete_url = $delete_url;
        $opts = CustomUploadHandler::getDefaultOptions();

		if (array_key_exists('image_versions', $options) && is_null($options['image_versions']))
            $options['image_versions'] = array();

        if (array_key_exists('upload_dir', $options))
        {
            $options['upload_url'] = $this->getFullUrl().'/'.$options['upload_dir'];
            $options['upload_dir'] = dirname($_SERVER['SCRIPT_FILENAME']).'/'.$options['upload_dir'];
        }
		
        if ($options != null)
            $opts = array_merge($opts, $options);

        parent::__construct($opts, false, null);
    }

    protected function trim_file_name($name, $type, $index, $content_range) {
        $name = parent::trim_file_name($name, $type, $index, $content_range);
        
        if (isset($this->options['filename']))
        {
            $name = $this->options['filename'].strtolower(strrchr($name, '.'));
        }
        return $name;
    }

    protected function get_file_name($name, $type, $index, $content_range) {
        if ($this->options['erase_file'])
            return $this->trim_file_name($name, $type, $index, $content_range);
        else
            return parent::get_file_name($name, $type, $index, $content_range);
    }

    protected function get_file_name_param() {
        return $this->options['filename'];
    }

    protected function set_file_delete_properties($file) {
        parent::set_file_delete_properties($file);

        if ($this->delete_url != null)
        {
            if (substr($this->delete_url, -1) != '/')
                $this->delete_url .= '/';
            $file->delete_url = $this->delete_url
                .rawurlencode($file->name);
        }
    }
}

?>