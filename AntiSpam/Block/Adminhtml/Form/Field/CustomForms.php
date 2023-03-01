<?php

namespace Avivi\AntiSpam\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
class CustomForms extends AbstractFieldArray
{
    /**
     * {@inheritdoc}
     */
    protected function _prepareToRender()
    {
        $this->addColumn('selector', ['label' => __('Selector'), 'class' => 'required-entry']);
        $this->addColumn('action', ['label' => __('Form Action'), 'class' => 'required-entry']);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Form');
    }
}
