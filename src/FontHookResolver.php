<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\TwoStageFontLoader;

use Inpsyde\WpContext;

class FontHookResolver
{
    public const HOOK_FRONTEND = 'wp_head';
    public const HOOK_BACKEND = 'admin_head';
    public const HOOK_LOGIN = 'login_head';

    private WpContext $context;

    public function __construct()
    {
        $this->context = WpContext::determine();
    }

    /**
     * @return list<string>
     */
    public function resolve(): array
    {
        $isLogin = $this->context->isLogin();
        $isFront = $this->context->isFrontoffice();

        if (!$isLogin && !$isFront && !$this->context->isBackoffice()) {
            return [];
        }

        if ($isLogin) {
            return [self::HOOK_LOGIN];
        }

        if ($isFront) {
            return [self::HOOK_FRONTEND];
        }

        return [self::HOOK_BACKEND];
    }

    /**
     * @return string|null
     */
    public function lastHook(): ?string
    {
        if ($this->context->isLogin() === true) {
            return self::HOOK_LOGIN;
        }
        if ($this->context->isFrontoffice() === true) {
            return self::HOOK_FRONTEND;
        }
        if ($this->context->isBackoffice() === true) {
            return self::HOOK_BACKEND;
        }

        return null;
    }
}
