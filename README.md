AlternativeMail [![Build Status](https://travis-ci.org/m8rge/AlternativeMail.png?branch=master)](https://travis-ci.org/m8rge/AlternativeMail) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/82765052-0be2-445e-92ad-cd494a1e4534/mini.png)](https://insight.sensiolabs.com/projects/82765052-0be2-445e-92ad-cd494a1e4534)
===============

Simple php class for utf8 html/text emails with small attachments

## Using
```php
$email = new AlternativeMail();
$email->setFrom('me@mail.localhost')
    ->addTo('bro@mail.localhost')
    ->setSubject('True story, bro')
    ->setTextBody("simple\ntext\nbody")
    ->send();
```
or
```php
$email = new AlternativeMail();
$email->setFrom('me@mail.localhost', 'Your brother')
    ->addTo('bro@mail.localhost', 'My Bro')
    ->addTo('bro2@mail.localhost', 'My Bro, too')
    ->setSubject('True story, bro')
    ->setTextBody("simple\ntext\nbody")
    ->setHtmlBody('<hr />html message<hr />')
    ->addAttachment(__FILE__, 'forceName.php', 'application/x-php')
    ->send();
```
