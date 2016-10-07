<?php

namespace DigitalHammer\LpForms;

use Text_Template as Template;
use Valitron\Validator as Validator;

class Form
{
    private $post;
    private $rules;
    public $formId;
    private $fields;
    private $validator;
    private $templateFile;
    private $templateData;
    private $attributeNames;

    /**
     * Form constructor.
     * @param string $formId Form identificator
     * @param array $post $_POST data array
     * @param \DigitalHammer\LpForms\Mailer $mailer Mailer instance
     * @param string $lang language for validator
     */
    public function __construct($formId, array $post, $mailer, $lang = 'ru')
    {
        $this->formId = $formId;
        $this->post = $post;
        $this->mailer = $mailer;

        Validator::lang($lang);
        $this->validator = new Validator($this->post);
        $this->response = new ResponseJson();
        $this->setMessageBodyTemplate($formId);
    }

    /**
     * Set validation rules for the form
     *
     * @param array $rules
     */
    public function setRules(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * Set fiels names for the form
     *
     * @param array $attributeNames
     * @return $this
     */
    public function setFieldNames(array $attributeNames)
    {
        $this->attributeNames = $attributeNames;

        return $this;
    }

    /**
     * Compound rule
     *
     * @param string $rule
     * @return array|bool
     */
    private function compoundRule($rule)
    {
        $exploded = explode(':', $rule);

        return count($exploded) > 1 ? $exploded : false;
    }

    /**
     * Set validation rules for valitron
     *
     * @return $this
     */
    private function setValidationRules()
    {
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

    /**
     * Set template for mail
     *
     * @param string $template
     * @param array $data
     */
    public function setMessageBodyTemplate($template, $data = [])
    {
        $this->templateFile = $template . '_template.tpl.php';
        $this->templateData = $data;
    }

    /**
     * Validate form
     *
     * @return mixed
     */
    private function validate()
    {
        $this->validator->labels($this->attributeNames);
        $this->setValidationRules();

        return $this->validator->validate();
    }

    /**
     * Get the validation rules
     *
     * @return mixed
     */
    private function validationErrors()
    {
        return $this->validator->errors();
    }

    /**
     * Get the form field label
     *
     * @param string $field
     * @return string
     */
    public function getLabel($field)
    {
        return isset($this->attributeNames[$field])
            ? $this->attributeNames[$field]
            : $field;
    }

    /**
     * Get the form field value
     *
     * @param string $field
     * @return string
     */
    public function getValue($field)
    {
        return isset($this->post[$field])
            ? $this->post[$field]
            : '';
    }

    /**
     * Generate mail body
     *
     * @return mixed
     */
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

    /**
     * Handle the form
     */
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

    /**
     * Add field to the form
     *
     * @param string $name
     * @param array $rules
     * @return $this
     */
    public function addField($name, array $rules)
    {
        $this->fields[$name] = $rules;

        return $this;
    }
}
