<?php

namespace Xvlvv\DTO;

/**
 * DTO для хранения пары code_verifier и code_challenge для OAuth 2.0 PKCE.
 */
readonly final class VkCodeVerifierDTO
{
    /**
     * @param string $codeVerifier Секретная строка, генерируемая на стороне клиента.
     * @param string $codeChallenge Преобразованная и закодированная строка для отправки на сервер авторизации.
     */
    public function __construct(
        public string $codeVerifier,
        public string $codeChallenge,
    ) {
    }
}