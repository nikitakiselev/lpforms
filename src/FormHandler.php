<?php

namespace DigitalHammer\LpForms;

use Exception;

class FormHandler
{
    /**
     * @var \DigitalHammer\LpForms\Form
     */
    private $forms;

    /**
     * Add form to the handler
     *
     * @param \DigitalHammer\LpForms\Form $form
     * @return Form
     */
    public function addForm(Form $form)
    {
        return $this->forms[$form->formId] = $form;
    }

    /**
     * Check for the form is handled
     *
     * @param string $formId
     * @return bool
     */
    public function isHandled($formId)
    {
        return (bool) isset($this->forms[$formId]);
    }

    /**
     * Handle the form
     *
     * @param string $formId
     * @return \DigitalHammer\LpForms\ResponseJson
     * @throws \Exception
     */
    public function handle($formId)
    {
        if ($this->isHandled($formId))
        {
            return $this->forms[$formId]->handle();
        }
        
        throw new Exception('Form with id "' . $formId . '" not handled.' );
    }
}
