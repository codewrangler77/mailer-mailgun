<?php 

namespace Codewrangler77\MailerMailgun;

use Swift_Mailer;
use Illuminate\Support\ServiceProvider;
use Illuminate\Mail\MailServiceProvider;
use Codewrangler77\MailerMailgun\MailgunTransport;


class MailgunServiceProvider extends MailServiceProvider {

	/**
	 * Register the Swift Transport instance, now with mailgun support
	 *
	 * @param  array  $config
	 * @return void
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function registerSwiftTransport($config)
	{
		switch ($config['driver'])
		{
			case 'mailgun':
				return $this->registerMailgunTransport($config);
			case 'smtp':
				return $this->registerSmtpTransport($config);
			case 'sendmail':
				return $this->registerSendmailTransport($config);
			case 'mail':
				return $this->registerMailTransport($config);
			default:
				throw new \InvalidArgumentException('Invalid mail driver.');
		}
	}

	/**
	 * Register the SMTP Swift Transport instance.
	 *
	 * @param  array  $config
	 * @return void
	 */
	protected function registerMailgunTransport($config)
	{
		$this->app['swift.transport'] = $this->app->share(function($app) use ($config)
		{
			extract($config);

			$transport = MailgunTransport::newInstance($mailgun_key, $mailgun_domain);

			return $transport;
		});
	}
}
