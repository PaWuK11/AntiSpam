<?php

namespace Avivi\AntiSpam\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Avivi\AntiSpam\Model\System\Config\Source\Forms;



class Data extends AbstractHelper
{
    const CONFIG_ENABLED = 'hs_antispam/general/enabled';
    const CONFIG_FORMS = 'hs_antispam/general/forms';
    const CONFIG_CUSTOM_FORMS = 'hs_antispam/general/custom_forms';

    private $formActionSelectors = [
        Forms::TYPE_LOGIN => 'body.customer-account-login #login-form.form.form-login',
        Forms::TYPE_CREATE => 'body.customer-account-create #form-validate.form-create-account',
        Forms::TYPE_FORGOT => '#form-validate.form.password.forget',
        Forms::TYPE_CONTACT => '#contact-form',
        Forms::TYPE_CHANGE_PASSWORD => '#form-validate.form.form-edit-account',
        Forms::TYPE_PRODUCT_REVIEW => '#review-form',
    ];

    /**
     * Currently selected store ID if applicable.
     *
     * @var int
     */
    protected $_storeId = null;

    /**
     * Get config value by path.
     *
     * @param string $path
     *
     * @return mixed
     */
    public function getConfigValue($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get config flag by path.
     *
     * @param string $path
     *
     * @return bool
     */
    public function getConfigFlag($path)
    {
        return $this->scopeConfig->isSetFlag($path, ScopeInterface::SCOPE_STORE, $this->_storeId);
    }

    /**
     * Return true if active and false otherwise.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getConfigFlag(self::CONFIG_ENABLED);
    }

    /**
     * Get selected forms.
     *
     * @return array
     */
    public function getForms($store = null)
    {
        $forms = $this->scopeConfig->getValue(
            self::CONFIG_FORMS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );

        $forms = explode(',', $forms) ?: [];
        if (!is_array($forms)) {
            return [trim($forms)];
        }

        return array_map('trim', $forms);
    }

    /**
     * Get selected forms.
     *
     * @return array
     */
    public function getCustomForms()
    {
        $data = $this->scopeConfig->getValue(
            self::CONFIG_CUSTOM_FORMS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return json_decode($data, true) ?: [];
    }

    /**
     * Get css selectors for the selected forms.
     *
     * @return array
     */
    public function getSelectedFormSelectors()
    {
        $forms = $this->getForms();
        $finalForms = [];
        foreach ($forms as $action) {
            if (isset($this->formActionSelectors[$action])) {
                $finalForms[] = $this->formActionSelectors[$action];
            }
        }

        return $finalForms;
    }

    /**
     * Get selected forms paths.
     *
     * @return array
     */
    public function getFormSelectors()
    {
        $forms = $this->getSelectedFormSelectors();
        $customForms = array_column($this->getCustomForms(), 'selector');

        return array_merge($forms, $customForms);
    }

    /**
     * Get selected form actions.
     *
     * @return array
     */
    public function getFormActions()
    {
        $forms = $this->getForms();
        $customForms = array_column($this->getCustomForms(), 'action');

        return array_merge($forms, $customForms);
    }
}
