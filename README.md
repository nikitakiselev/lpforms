# lpforms
Easy create landing page forms

```
composer require digitalhammer/lpforms
bower install digitalhammer/ajax-forms
```


## How to use

Use with ```https://github.com/digitalhammer/ajax-forms```

**File structure**
```
|- bower_components
|   |- ajax-forms
|      |- AjaxForm.js
|
|- emails
|   |- contact_template.tpl.php
|
|- form-handler.php
|- index.html
```

**index.html**
```html
  <body>
    <form action="form-handler.php" method="post" id="contact-form">
      <input type="hidden" name="form_id" value="contact"/>
      
      <div class="form-group">
        <label for="email" class="control-label">Your e-mail</label>
        <input type="email" id="email" name="email" value="" class="form-control"/>
        <div class="help-block"></div>
      </div>
      
      <button type="submit">Send Form!</button>
    </form>
    
    <script src="/bower_components/ajax-forms/AjaxForm.js"></script>
    <script>
      var contactForm = new AjaxForm('#contact-form');
    </script>
  </body>
</html>
```

**form-handler.php**
```php
<?php
require __DIR__ . '/vendor/autoload.php';
date_default_timezone_set('Europe/Moscow');

$post = $_POST;
$formId = isset($post['form_id']) ? $post['form_id'] : null;

use DigitalHammer\LpForms\Form;
use DigitalHammer\LpForms\Mailer;
use DigitalHammer\LpForms\FormHandler;

/**
 * Settings
 */
$siteName = 'Your site name';
$mailFrom = ['from@mail.com', $siteName];
$mailTo = 'to@mail.com';

/**
 * Contact form
 */
$contactFormMailer = new Mailer($mailFrom, $mailTo);
$contactFormMailer->setSubject('New request from ' . $siteName);
$contactkForm = new Form('contact', $post, $contactFormMailer);
$contactForm
    ->addField('email', ['required', 'email'])
    ->setFieldNames([
        'email' => 'Your e-mail',
    ])
    ->setMessageBodyTemplate('emails/contact', [
        'form_name' => "Request from site $siteName"
    ]);
    
$formHandler = new FormHandler();
$formHandler->addForm($contactForm);

// Handle form!
try {
    
    $formHandler->handle($formId);

} catch (Exception $exception) {

    $response = new \DigitalHammer\LpForms\ResponseJson();

    echo $response->fail($exception->getMessage());
}
```

**emails/contact_template.tpl.php**
```html
<p>New form request.</p>
<p>Form was sended at: {current_date}</p>
<p>Form data:</p>
<ul>
  <li>Email: {email}</li>
</ul>
```
