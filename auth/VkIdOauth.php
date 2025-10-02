<?php

namespace app\auth;

use Throwable;
use Xvlvv\DTO\VkCodeVerifierDTO;
use Yii;
use yii\authclient\OAuth2;
use yii\authclient\OAuthToken;
use yii\base\NotSupportedException;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;

class VkIdOauth extends OAuth2
{
    public $tokenUrl = 'https://id.vk.ru/oauth2/auth';
    public $authUrl = 'https://id.vk.ru/authorize';
    public string $userAttrUrl = 'https://id.vk.ru/oauth2/user_info';
    private ?OAuthToken $token = null;
    private ?string $_returnUrl = null;
    public $scope = 'email';

    public function getUserData(): array
    {
        return $this->initUserAttributes();
    }

    /**
     * @inheritDoc
     */
    protected function initUserAttributes()
    {
        try {
            $request = $this->createRequest()
                ->setMethod('POST')
                ->setUrl($this->userAttrUrl)
                ->setData([
                    'client_id' => $this->clientId,
                    'access_token' => $this->token->getParam('access_token'),
                ]);

            $response = $request->send();

            $result = json_decode($response->getContent(), true);
        } catch (Throwable) {
            throw new BadRequestHttpException();
        }

        return $result['user'] ?? [];
    }
    private function generatePkce(): VkCodeVerifierDTO
    {
        $codeVerifier = Yii::$app->security->generateRandomString(64);
        $codeChallenge = base64_encode(hash('sha256', $codeVerifier, true));
        $codeChallenge = rtrim(strtr($codeChallenge, '+/', '-_'), '=');

        return new VkCodeVerifierDTO(
            $codeVerifier,
            $codeChallenge
        );
    }

    public function buildAuthUrl(array $params = []): string
    {
        $defaultParams = [
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->getReturnUrl(),
            'code_challenge_method' => 'S256',
        ];

        if (!empty($this->scope)) {
            $defaultParams['scope'] = $this->scope;
        }

        if ($this->validateAuthState) {
            $authState = $this->generateAuthState();
            $this->setState('authState', $authState);
            $defaultParams['state'] = $authState;
        }

        $pkce = $this->generatePkce();
        $this->saveCodeVerifier($pkce->codeVerifier);
        $defaultParams['code_challenge'] = $pkce->codeChallenge;

        return $this->composeUrl($this->authUrl, array_merge($defaultParams, $params));
    }

    public function fetchAccessToken($authCode, array $params = [])
    {
        $codeVerifier = $this->getCodeVerifier();
        $deviceId = $this->getDeviceId();

        if (null === $codeVerifier
        || null === $deviceId) {
            throw new HttpException(400);
        }

        $authParams = [
            'code_verifier' => $codeVerifier,
            'device_id' => $deviceId,
            'scope' => $this->scope,
        ];

        $params = array_merge($authParams, $params);

        $this->token = parent::fetchAccessToken($authCode, $params);
        return $this;
    }

    protected function defaultReturnUrl(): string
    {
        return Url::to(['/oauth/callback'], true);
    }

    public function saveCodeVerifier(string $codeVerifier): void
    {
        Yii::$app->session->set('vk_oauth_verifier', $codeVerifier);
    }

    public function getCodeVerifier(): ?string
    {
        return Yii::$app->session->get('vk_oauth_verifier');
    }

    public function saveDeviceId(string $deviceId): void
    {
        Yii::$app->session->set('vk_oauth_device_id', $deviceId);
    }

    public function getDeviceId(): ?string
    {
        return Yii::$app->session->get('vk_oauth_device_id');
    }

    protected function setState($key, $value)
    {
        Yii::$app->session->set($key, $value);
    }

    protected function getState($key)
    {
        return Yii::$app->session->get($key);
    }

    protected function applyClientCredentialsToRequest($request): void
    {
        $request->addData([
            'client_id' => $this->clientId,
        ]);
    }

    public function authenticateUserJwt($username, $signature = null, $options = [], $params = [])
    {
        throw new NotSupportedException('Method is not supported.');
    }
}