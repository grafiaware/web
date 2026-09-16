<?php
declare(strict_types=1);

namespace Test\Support;

use Container\AuthContainerConfigurator;
use Container\AuthDbContainerConfigurator;
use Mail\MessageFactory\HtmlMessage;
use Pes\Container\Container;
use Pes\Mail\MailInterface;
use Test\Integration\Mail\Support\MailSpy;

final class AuthControllerContainerFactory
{
    public static function createWithMailSpy(): array
    {
        $mailSpy = new MailSpy();
        $container = (new AuthContainerConfigurator())->configure(
            (new AuthDbContainerConfigurator())->configure(new Container())
        );
        $container->set(MailInterface::class, $mailSpy);
        $container->set(HtmlMessage::class, new HtmlMessage());

        return [$container, $mailSpy];
    }
}
