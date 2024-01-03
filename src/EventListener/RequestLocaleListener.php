<?php
declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Translation\LocaleSwitcher;

#[AsEventListener(event: KernelEvents::REQUEST)]
final readonly class RequestLocaleListener
{
    // @todo Solve this with config or detection.
    private const array ALLOWED_LOCALES = ['en', 'nl'];

    public function __construct(private LocaleSwitcher $localeSwitcher)
    {
    }

    public static function isAllowed(string $locale): bool
    {
        return in_array($locale, self::ALLOWED_LOCALES, true);
    }

    public static function getAcceptLanguages(Request $request): array
    {
        $acceptLanguages = array_map(
            static function (string $spec): array {
                [$locale, $q] = explode(
                    ';',
                    trim($spec) . ';q=1.0',
                    3
                );

                return [$locale, (float)substr($q, 2)];
            },
            explode(',', $request->headers->get('accept-language'))
        );

        usort(
            $acceptLanguages,
            static function (array $a, array $b): int {
                [, $qa] = $a;
                [, $qb] = $b;

                return $qb <=> $qa;
            }
        );

        return array_column($acceptLanguages, 0);
    }

    public function __invoke(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $session = $request->getSession();

        $acceptLanguages = array_filter(
            self::getAcceptLanguages($request),
            self::isAllowed(...)
        );

        $locale = $request->get('locale')
            ?? $session->get('_locale')
            ?? array_shift($acceptLanguages)
            ?? $this->localeSwitcher->getLocale();

        if (self::isAllowed($locale)) {
            $this->localeSwitcher->setLocale($locale);
            $session->set('_locale', $locale);
        }
    }
}