<?php

namespace Nikitakiselev\LpForms;

use Nikitakiselev\LpForms\Exceptions\FormHandlerException;

class FormHandler
{
    /**
     * @var \Nikitakiselev\LpForms\Form
     */
    protected $forms;

    /**
     * Add form to the handler
     *
     * @param \Nikitakiselev\LpForms\Form $form
     *
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
     *
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
     *
     * @return \Nikitakiselev\LpForms\ResponseJson
     * @throws \Nikitakiselev\Exceptions\FormHandlerException
     */
    public function handle($formId)
    {
        if ($this->isHandled($formId))
        {
            return $this->forms[$formId]->handle();
        }
        
        throw new FormHandlerException('Form with id "' . $formId . '" not handled.' );
    }
}
