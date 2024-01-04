<?php
declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Translation\LocaleSwitcher;

#[AsEventListener(event: KernelEvents::REQUEST)]
final readonly class RequestLocaleListener
{
    public function __construct(
        private LocaleSwitcher $localeSwitcher,
        #[Autowire(value: '%kernel.enabled_locales%')]
        private array $enabledLocales
    ) {
    }

    public function __invoke(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $session = $request->getSession();

        $locale = $request->get('locale')
            ?? $session->get('_locale')
            ?? $request->getPreferredLanguage($this->enabledLocales)
            ?? $this->localeSwitcher->getLocale();

        if (in_array($locale, $this->enabledLocales, true)) {
            $this->localeSwitcher->setLocale($locale);
            $session->set('_locale', $locale);
        }
    }
}