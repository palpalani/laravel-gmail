<?php

declare(strict_types=1);

namespace Dacastro4\LaravelGmail\Traits;

use Google_Service_Gmail;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

/**
 * Trait Configurable
 * @package Dacastro4\LaravelGmail\Traits
 */
trait Configurable
{
    protected $additionalScopes = [];
    private $_config;

    public function __construct($config)
    {
        $this->_config = $config;
    }

    abstract public function setScopes($scopes);

    abstract public function setAccessType($type);

    abstract public function setApprovalPrompt($approval);

    abstract public function setPrompt($prompt);

    public function config($string = null)
    {
        $disk = Storage::disk('local');
        $fileName = $this->getFileName();
        $file = "gmail/tokens/{$fileName}.json";
        $allowJsonEncrypt = $this->_config['gmail.allow_json_encrypt'];

        if ($disk->exists($file)) {
            if ($allowJsonEncrypt) {
                $config = json_decode(decrypt($disk->get($file)), true);
            } else {
                $config = json_decode($disk->get($file), true);
            }

            if ($string) {
                if (isset($config[$string])) {
                    return $config[$string];
                }
            } else {
                return $config;
            }

        }

        return null;
    }

    /**
     * @return array
     */
    public function getConfigs()
    {
        return [
            'client_secret' => $this->_config['gmail.client_secret'],
            'client_id' => $this->_config['gmail.client_id'],
            'redirect_uri' => url($this->_config['gmail.redirect_url']),
            'state' => $this->_config['gmail.state'] ?? null,
        ];
    }

    public function setAdditionalScopes(array $scopes)
    {
        $this->additionalScopes = $scopes;

        return $this;
    }

    private function getFileName()
    {
        if (property_exists(get_class($this), 'userId') && $this->userId) {
            $userId = $this->userId;
        } elseif (auth()->user()) {
            $userId = auth()->user()->id;
        }

        $credentialFilename = $this->_config['gmail.credentials_file_name'];
        $allowMultipleCredentials = $this->_config['gmail.allow_multiple_credentials'];

        if (isset($userId) && $allowMultipleCredentials) {
            return sprintf('%s-%s', $credentialFilename, $userId);
        }

        return $credentialFilename;
    }

    private function configApi(): void
    {
        $type = $this->_config['gmail.access_type'];
        $approval_prompt = $this->_config['gmail.approval_prompt'];
        $prompt = $this->_config['gmail.prompt'];


        $this->setScopes($this->getUserScopes());

        $this->setAccessType($type);

        $this->setApprovalPrompt($approval_prompt);

        $this->setPrompt($prompt);

    }

    private function getUserScopes()
    {
        return $this->mapScopes();
    }

    private function mapScopes()
    {
        $scopes = array_merge($this->_config['gmail.scopes'] ?? [], $this->additionalScopes);
        $scopes = array_unique(array_filter($scopes));
        $mappedScopes = [];

        if ( ! empty($scopes)) {
            foreach ($scopes as $scope) {
                $mappedScopes[] = $this->scopeMap($scope);
            }
        }

        return array_merge($mappedScopes, $this->_config['gmail.additional_scopes'] ?? []);
    }

    private function scopeMap($scope)
    {
        $scopes = [
            'all' => Google_Service_Gmail::MAIL_GOOGLE_COM,
            'compose' => Google_Service_Gmail::GMAIL_COMPOSE,
            'insert' => Google_Service_Gmail::GMAIL_INSERT,
            'labels' => Google_Service_Gmail::GMAIL_LABELS,
            'metadata' => Google_Service_Gmail::GMAIL_METADATA,
            'modify' => Google_Service_Gmail::GMAIL_MODIFY,
            'readonly' => Google_Service_Gmail::GMAIL_READONLY,
            'send' => Google_Service_Gmail::GMAIL_SEND,
            'settings_basic' => Google_Service_Gmail::GMAIL_SETTINGS_BASIC,
            'settings_sharing' => Google_Service_Gmail::GMAIL_SETTINGS_SHARING,
        ];

        return Arr::get($scopes, $scope);
    }


}
