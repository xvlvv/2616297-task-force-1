<?php

namespace Xvlvv\DTO;

readonly final class VkCodeVerifierDTO
{
    public function __construct(
        public string $codeVerifier,
        public string $codeChallenge,
    ) {
    }
}