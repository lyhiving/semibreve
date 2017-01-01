<?php

namespace Semibreve;

use Spyc;

use Minim\Authenticator;
use Minim\Configuration;

class Manager {

    private $config;

    /**
     * Manager constructor.
     * @param BaseConfiguration $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     *
     *
     * @param UserConfiguration $user
     * @return String
     */
    private function saveMinimConfiguration($user) {
        $id = md5($user->getAdminEmail()); // Hash username.
        $sessionFilename = $this->config->getSessionFolderName() . '/' . $id . '.dat'; // Create session file path.

        // Save Minim configuration.
        $userConfigFilename = $this->getMinimConfigurationFilename($user->getAdminEmail());
        $minimConfig = array(
            'admin_email' => $user->getAdminEmail(),
            'admin_password_hash' => $user->getAdminPasswordHash(),
            'secret_key' => $this->config->getSecretKey(),
            'token_length' => $this->config->getTokenLength(),
            'token_ttl' => $this->config->getTokenTtl(),
            'cookie_name' => $this->config->getCookieName(),
            'session_file_name' => $sessionFilename,
            'cookie_ssl_only' => $this->config->getCookieSslOnly(),
            'cookie_http_only' => $this->config->getCookieHttpOnly()
        );
        file_put_contents($userConfigFilename, Spyc::YAMLDump($minimConfig));

        // Pass filename back.
        return $userConfigFilename;
    }

    private function getUserConfigurationFilename($username) {
        $id = md5($username); // Hash username.
        return $this->config->getUserFolderName() . '/' . $id . '.yaml';
    }

    private function getMinimConfigurationFilename($username) {
        $id = md5($username); // Hash username.
        return $this->config->getConfigFolderName() . '/' . $id . '.yaml';
    }

    public function authenticate($username, $password) {
        // Attempt to get user configuration.
        $user = new UserConfiguration($this->getUserConfigurationFilename($username));
var_dump($user);


        // Invalid username.
        if ($user == null) {
            return null;
        }

        // Save Minim-compatible configuration.
        $path = $this->saveMinimConfiguration($user);

        // Use Minim to authenticate user.
        $minim = new Authenticator(new Configuration($path));
        $authenticated = $minim->authenticate($username, $password);

        // If authentication was successful, pass back user.
        return $authenticated ? $user : null;
    }
}