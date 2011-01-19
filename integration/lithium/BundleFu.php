<?php

/*
 * This file is part of BundleFu.
 *
 * (c) 2011 Jan Sorgalla <jan.sorgalla@dotsunited.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace app\extensions\helper;

/**
 * li3_bundlefu\extensions\helper\BundleFu
 *
 * @author  Jan Sorgalla <jan.sorgalla@dotsunited.de>
 */
class BundleFu extends \lithium\template\Helper
{
    /**
     * @var \DotsUnited\BundleFu\BundleFu
     */
    protected $_bundleFu;

    /**
     * Setup autloading.
     *
     * @return void
     */
    protected function _init() {
        parent::_init();

        // Setup autoloading
        spl_autoload_register(function($className) {
            if (strpos($className, 'DotsUnited\\BundleFu\\') === 0) {
                require str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
            }
        });
    }

    /**
     * Set the BundleFu instance
     *
     * @param \DotsUnited\BundleFu\BundleFu $bundleFu
     * @return Zend_View_Helper_BundleFu
     */
    public function setBundleFu(\DotsUnited\BundleFu\BundleFu $bundleFu)
    {
        $this->_bundleFu = $bundleFu;
        return $this;
    }

    /**
     * Get the BundleFu instance
     *
     * @return \DotsUnited\BundleFu\BundleFu
     */
    public function getBundleFu()
    {
        if (null === $this->_bundleFu) {
            $this->_bundleFu = new \DotsUnited\BundleFu\BundleFu();
            $this->_bundleFu->setDocRoot($this->_context->request()->env('DOCUMENT_ROOT'));
        }

        return $this->_bundleFu;
    }

    /**
     * @param string $method
     * @param array $params
     * @return mixed
     */
    public function __call($method, $params)
    {
        $return = call_user_func_array(array($this->getBundleFu(), $method), $params);

        switch ($method) {
            case 'start':
            case 'end':
            case substr($method, 0, 3) == 'set':
                return $this;
            default:
                return $return;
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        try {
            $return = $this->getBundleFu()->render();
            return $return;
        } catch (\Exception $e) {
            trigger_error($e->getMessage(), E_USER_WARNING);
            return '';
        }
    }
}

?>
