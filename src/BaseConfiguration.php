<?php

namespace Semibreve;

use Spyc;

/**
 * Represents the application base configuration file.
 *
 * @package Semibreve
 * @author Saul Johnson
 * @since 01/01/2017
 */
class BaseConfiguration
{
    private $config;

    /**
     * Creates a new instance of the application base configuration file.
     *
     * @param string $path  the path to read the base configuration file from
     */
    public function __construct($path)
    {
        $this->config = Spyc::YAMLLoad($path);
    }

    /**
     * Gets the secret key used by the application for symmetric encryption purposes.
     *
     * @return string
     */
    public function getSecretKey()
    {
        return $this->config['secret_key'];
    }

    /**
     * Gets the length configured for login tokens.
     *
     * @return int
     */
    public function getTokenLength()
    {
        return $this->config['token_length'];
    }

    /**
     * Gets the time to live for login tokens, in seconds.
     *
     * @return int
     */
    public function getTokenTtl()
    {
        return $this->config['token_ttl'];
    }

    /**
     * Gets the name of the cookie configured to hold the login auth token.
     *
     * @return string
     */
    public function getCookieName()
    {
        return $this->config['cookie_name'];
    }

    /**
     * Gets the name of the config folder.
     *
     * @return string
     */
    public function getConfigFolderName()
    {
        return $this->config['config_folder_name'];
    }

    /**
     * Gets the name of the user folder.
     *
     * @return string
     */
    public function getUserFolderName()
    {
        return $this->config['user_folder_name'];
    }

    /**
     * Gets the name of the session folder.
     *
     * @return string
     */
    public function getSessionFolderName()
    {
        return $this->config['session_folder_name'];
    }

    /**
     * Gets whether or not the login cookie is enabled for HTTPS only.
     *
     * @return bool
     */
    public function getCookieSslOnly()
    {
        return $this->config['cookie_ssl_only'];
    }

    /**
     * Gets whether or not the login cookie is enabled for HTTP(S) only and not client-side script.
     *
     * @return bool
     */
    public function getCookieHttpOnly()
    {
        return $this->config['cookie_http_only'];
    }
}
