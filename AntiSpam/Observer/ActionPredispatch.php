<?php

namespace Avivi\AntiSpam\Observer;

use Avivi\AntiSpam\Helper\Data;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Message\ManagerInterface;

class ActionPredispatch implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Http
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var ActionFlag
     */
    private $actionFlag;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var RedirectInterface
     */
    protected $redirect;

    /**
     * @param Data              $helper
     * @param Http              $request
     * @param ActionFlag        $actionFlag
     * @param ResponseInterface $response
     * @param RedirectInterface $redirect
     * @param ManagerInterface  $messageManager
     */
    public function __construct(
        Data $helper,
        Http $request,
        ActionFlag $actionFlag,
        ResponseInterface $response,
        RedirectInterface $redirect,
        ManagerInterface $messageManager
    ) {
        $this->helper = $helper;
        $this->request = $request;
        $this->redirect = $redirect;
        $this->response = $response;
        $this->actionFlag = $actionFlag;
        $this->messageManager = $messageManager;
    }

    /**
     * Execute observer.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        if (!$this->helper->isEnabled()
            || !in_array($this->request->getFullActionName(), $this->helper->getFormActions())
        ) {
            return $this;
        }

        if (empty($this->request->getParam('hs_hid'))) {
            $this->processError();
        }
    }

    /**
     * Process error.
     *
     * @return array | void
     */
    private function processError()
    {
        $message = __('Unauthorized form submission!');
        if ($this->request->isAjax()) {
            return [
                'success' => false,
                'error' => true,
                'message' => $message,
            ];
        }

        $this->messageManager->getMessages(true);
        $this->messageManager->addErrorMessage($message);
        $this->actionFlag->set('', ActionInterface::FLAG_NO_DISPATCH, true);
        $this->response->setRedirect($this->redirect->getRefererUrl());
    }
}
