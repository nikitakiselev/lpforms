<?php

namespace DigitalHammer\LpForms;

use \Valitron\Validator as Validator;
use \Text_Template as Template;

class Form
{
    public $formId;
    private $post;
    private $fields;

    private $rules;
    private $attributeNames;
    private $validator;

    private $templateFile;

    private $templateData;

    public function __construct($formId, $post, $mailer, $lang = 'ru')
    {
        $this->formId = $formId;
        $this->post = $post;
        $this->mailer = $mailer;

        Validator::lang($lang);
        $this->validator = new Validator($this->post);
        $this->response = new ResponseJson();
        $this->setMessageBodyTemplate($formId);
    }

    public function setRules(array $rules)
    {
        $this->rules = $rules;
    }

    public function setFieldNames($attributeNames)
    {
        $this->attributeNames = $attributeNames;

        return $this;
    }

    private function compoundRule($rule)
    {
        $exploded = explode(':', $rule);

        return count($exploded) > 1 ? $exploded : false;
    }

    private function setValidationRules()
    {
        $validatorRules = [];

        foreach ($this->fields as $field => $rules) {
            foreach ($rules as $rule) {
                if ($compoundRule = $this->compoundRule($rule)) {
                    $this->validator->rule($compoundRule[0], $field, $compoundRule[1]);
                } else {
                    $this->validator->rule($rule, $field);
                }
            }
        }

        return $this;
    }

    public function setMessageBodyTemplate($template, $data = [])
    {
        $this->templateFile = $template . '_template.tpl.php';
        $this->templateData = $data;
    }

    private function validate()
    {
        $this->validator->labels($this->attributeNames);
        $this->setValidationRules();

        return $this->validator->validate();
    }

    private function validationErrors()
    {
        return $this->validator->errors();
    }

    public function getLabel($field)
    {
        return isset($this->attributeNames[$field])
            ? $this->attributeNames[$field]
            : $field;
    }

    public function getValue($field)
    {
        return isset($this->post[$field])
            ? $this->post[$field]
            : '';
    }

    protected function generateMessageBody()
    {
        $template = new Template($this->templateFile);
        $template->setVar([
            'current_date' => date('d.m.Y H:i'),
            'form_id' => $this->formId
        ]);

        foreach ($this->fields as $field => $rules) {
            $value = $this->getValue($field);
            $template->setVar([
                $field => $value
            ]);
        }

        $template->setVar($this->templateData);

        return $template->render();
    }

    public function handle()
    {
        if ($this->validate())
        {
            $this->mailer->setMessageBody(
                $this->generateMessageBody()
            );

            if ($this->mailer->send())
            {
                return $this->response->success('Письмо успешно отправлено');
            }

            return $this->response->fail('Возникли ошибки при отправке письма: ' . $this->mailer->getErrorsInline());
        }

        return $this->response
            ->setResponseCode(422)
            ->make($this->validationErrors());
    }

    public function addField($name, array $rules)
    {
        $this->fields[$name] = $rules;

        return $this;
    }
}