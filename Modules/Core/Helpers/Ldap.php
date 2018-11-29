<?php
namespace Modules\Core\Helpers;

use Adldap\Adldap;
use App\Models\User;

/**
 * Class to help ldap operations
 *
 */
class Ldap
{
    /**
     * @var Adldap
     */
    protected $adldap;

    /**
     * Ldap constructor
     *
     * @param Adldap $adldap
     */
    public function __construct(Adldap $adldap)
    {
        $this->adldap = $adldap;
    }

    /**
     * Authenticate user by sAMAccountName
     *
     * @param string $username
     * @param string $password
     *
     * @return User|null
     *
     * @throws \Adldap\Auth\BindException
     * @throws \Adldap\Auth\PasswordRequiredException
     * @throws \Adldap\Auth\UsernameRequiredException
     */
    public function auth(string $username, string $password) : ?User
    {
        $provider = $this->adldap->connect();

        $user = $provider->search()->where('sAMAccountName', '=', $username)->first();

        if ($user && $provider->auth()->attempt($user->getDistinguishedName(), $password)) {
            return User::where('username', $username)->active()->first();
        }

        return null;
    }
}
