<?php

namespace App\Providers;

use Config;
use Illuminate\Support\ServiceProvider;

class MailServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if(getenv("MAIL_SES_URL") !== false && !empty(env('MAIL_SES_URL'))){
            $url = env('MAIL_SES_URL');
            $content = file_get_contents($url);
            $mail = json_decode($content);
            $config = array(
                'driver'     => $mail->mail_driver,
                'host'       => $mail->mail_host,
                'port'       => $mail->mail_port,
                'from'       => array('address' => Config::get('mail.from.address'), 'name' => Config::get('mail.from.name')),
                'encryption' => $mail->mail_encryption,
                'username'   => $mail->mail_username,
                'password'   => $mail->mail_password,
                'sendmail'   => '/usr/sbin/sendmail -bs',
                'pretend'    => false,
            );
            Config::set('mail', $config);
        }
    }
}
