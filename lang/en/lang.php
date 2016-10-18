<?php
return [
    'plugin' => [
        'name' => 'Mail Sender'
        ],
    'backend' => [
        'title' => 'Mail Sender',
        ],
    'controller' => [
        'groups'        => 'Select a group',
        'subject'       => 'Message subject',
        'typesubject'   => 'Type the message subject here',
        'message'       => 'Type the message',
        'type'          => 'Type your message here',
        'submit'        => 'Send Message Now',
        ],
    'sent'      => 'Message has been sent successfully to ',
    'users'     => ' users',
    'nousers'   => 'There\'s no users in this group',
    'callout'   => [
        'title'   => 'Backend users mail sender',
        'body'    => 'This plugin allows you to send a mail to a specific backend group',
        ],
    'test' => [
        'text'              => 'You can send a test mail through this form',
        'email'             => 'Type your email here',
        'send_mail'         => 'Send Test Mail',
        'email_placeholder' => 'email@email.com',
        'sent'              => 'Message has been sent successfully',
        ],
    'error_nodata' => 'All fields are required',
    'username' => 'Username',
    'email' => 'Email',
    'sendToAll' => 'Send to all users',
    'chosen' => [
        'sent' => 'The email was sent to all chosen users',
        ],
    ];
