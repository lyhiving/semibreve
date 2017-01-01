<?php

namespace Semibreve;

use Minim\Authenticator;

/**
 * Represents an authentication context.
 *
 * @package Semibreve
 * @author Saul Johnson
 * @since 01/01/2017
 */
class AuthenticationContext
{
    /**
     * The authenticator that is valid for this session.
     *
     * @var Authenticator
     */
    private $authenticator;

    /**
     * The user that is currently authenticated.
     *
     * @var UserConfiguration
     */
    private $user;

    /**
     * Initialises an authentication context.
     *
     * @param Authenticator $authenticator  the authenticator that is valid for this session
     * @param UserConfiguration $user       the user that is currently authenticated
     */
    public function __construct($authenticator, $user)
    {
        $this->authenticator = $authenticator;
        $this->user = $user;
    }

    /**
     * The authenticator that is valid for this session.
     *
     * @return Authenticator    the authenticator that is valid for this session
     */
    public function getAuthenticator()
    {
        return $this->authenticator;
    }

    /**
     * The user that is currently authenticated.
     *
     * @return UserConfiguration    the user that is currently authenticated.
     */
    public function getUser()
    {
        return $this->user;
    }
}