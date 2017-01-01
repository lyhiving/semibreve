<?php

namespace Semibreve;

use Spyc;

/**
 * Represents an individual user configuration file.
 *
 * @package Semibreve
 * @author Saul Johnson
 * @since 01/01/2017
 */
class UserConfiguration
{
    private $config;

    /**
     * Creates a new instance of the application base configuration file.
     *
     * @param string $path the path to read the base configuration file from
     */
    public function __construct($path)
    {
        $this->config = Spyc::YAMLLoad($path);
    }


    /**
     * Gets the admin email address required for login.
     *
     * @return string
     */
    public function getAdminEmail()
    {
        return $this->config['admin_email'];
    }

    /**
     * Gets the password hash required for login.
     *
     * @return string
     */
    public function getAdminPasswordHash()
    {
        return $this->config['admin_password_hash'];
    }

    /**
     * Gets the role for this user.
     *
     * @return string
     */
    public function getRole()
    {
        return $this->config['role'];
    }
}
