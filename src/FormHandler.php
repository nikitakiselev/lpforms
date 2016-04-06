<?php

namespace DigitalHammer\LpForms;

class FormHandler
{
    private $forms;

    public function addForm(Form $form)
    {
        return $this->forms[$form->formId] = $form;
    }

    public function isHandled($formId)
    {
        return (bool) isset($this->forms[$formId]);
    }

    public function handle($formId)
    {
        if ($this->isHandled($formId))
        {
            return $this->forms[$formId]->handle();
        }
        
        throw new \Exception('Form with id "' . $formId . '" not handled.' );
    }
}