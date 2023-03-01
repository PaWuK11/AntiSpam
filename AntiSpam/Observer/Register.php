<?php

namespace Avivi\AntiSpam\Observer;

use Avivi\AntiSpam\Helper\Data;
use Magento\Framework\Event\ObserverInterface;

class Register implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param Data $helper
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Register extension.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->helper->register('Avivi_AntiSpam', '1.0.2', 'confirm');
    }
}
