<?php

namespace Semibreve;

use Spyc;

use Minim\Authenticator;
use Minim\Configuration;

/**
 * Represents a manager (aggregator) of Minim instances.
 *
 * @package Semibreve
 * @author Saul Johnson
 * @since 01/01/2017
 */
class Manager
{
    /**
     * The base configuration of the manager.
     *
     * @var BaseConfiguration
     */
    private $config;

    /**
     * Initialises a new instance of a manager (aggregator) of Minim instances.
     *
     * @param BaseConfiguration $config the base configuration of the manager
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Saves a Minim-compatible configuration using the configured base configuration and given user configuration.
     *
     * @param UserConfiguration $user   the user configuration to use
     * @return String                   the path to the saved Minim-compatible configuration file
     */
    private function saveMinimConfiguration($user)
    {
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

    /**
     * Gets the name of the user configuration file for the specified username.
     *
     * @param string $username  the username to get the configuration filename for
     * @return string           the full path of the configuration file
     */
    private function getUserConfigurationFilename($username)
    {
        $id = md5($username); // Hash username.
        return $this->config->getUserFolderName() . '/' . $id . '.yaml';
    }

    /**
     * Gets the user configuration for the specified username.
     *
     * @param string $username      the username to get the configuration for
     * @return UserConfiguration    the configuration for the username
     */
    private function getUserConfiguration($username)
    {
        return new UserConfiguration($this->getUserConfigurationFilename($username));
    }

    /**
     * Checks that a user configuration exists for the specified username.
     *
     * @param string $username  the username to check
     * @return bool             true if the user configuration exists, otherwise false
     */
    private function userConfigurationExists($username)
    {
        return file_exists($this->getUserConfigurationFilename($username));
    }

    /**
     * Gets the Minim configuration filename for the specified username.
     *
     * @param string $username  the username to get the Minim configuration filename for
     * @return string           the Minim configuration filename
     */
    private function getMinimConfigurationFilename($username)
    {
        $id = md5($username); // Hash username.
        return $this->config->getConfigFolderName() . '/' . $id . '.yaml';
    }

    /**
     * Gets the current authentication context.
     *
     * @return null|AuthenticationContext   the current authentication context if logged in, otherwise null
     */
    private function getCurrentAuthenticationContext() {
        // Scan for all Minim configs.
        $minimConfigs = scandir($this->config->getConfigFolderName());

        // Loop through each.
        foreach ($minimConfigs as $minimConfig) {
            if ($minimConfig != '..' && $minimConfig != '.') { // Ignore dot folders.

                // Load Minim configuration
                $path = $this->config->getConfigFolderName() . '/' . $minimConfig;
                $config = new Configuration($path);

                // See if it's authenticated for this user.
                $auth = new Authenticator($config);
                if ($auth->isAuthenticated()) {
                    $user = $this->getUserConfiguration($config->getAdminEmail()); // Reduce configuration.
                    return new AuthenticationContext($auth, $user);
                }
            }
        }

        // No auth found.
        return null;
    }

    /**
     * Authenticates the user.
     *
     * @param string $username          the username credential
     * @param string $password          the password credential
     * @return null|UserConfiguration   the user configuration if authentication was successful, otherwise null
     */
    public function authenticate($username, $password) {
        // Invalid username.
        if (!$this->userConfigurationExists($username)) {
            return null;
        }

        // Get user configuration.
        $user = $this->getUserConfiguration($username);

        // Save Minim-compatible configuration.
        $path = $this->saveMinimConfiguration($user);

        // Use Minim to authenticate user.
        $minim = new Authenticator(new Configuration($path));
        $authenticated = $minim->authenticate($username, $password);

        // If authentication was successful, pass back user.
        return $authenticated ? $user : null;
    }

    /**
     * Gets the configuration for the currently logged in user.
     *
     * @return null|UserConfiguration   the configuration for the currently logged in user, otherwise null
     */
    public function getAuthenticatedUser()
    {
        // No authentication context means no user.
        $context = $this->getCurrentAuthenticationContext();
        if ($context === null) {
            return null;
        }
        return $context->getUser();
    }

    /**
     * Logs out of the system.
     */
    public function logout()
    {
        // No authentication context means we're logged out anyway.
        $context = $this->getCurrentAuthenticationContext();
        if ($context !== null) {
            $context->getAuthenticator()->logout();
        }
    }
}