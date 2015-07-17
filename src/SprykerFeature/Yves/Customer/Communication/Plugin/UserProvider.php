<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Yves\Customer\Communication\Plugin;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Zed\Ide\AutoCompletion;
use SprykerEngine\Yves\Kernel\Communication\AbstractPlugin;
use SprykerFeature\Client\Customer\Service\CustomerClientInterface;
use SprykerFeature\Client\Session\Service\SessionClientInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider extends AbstractPlugin implements UserProviderInterface
{

    /**
     * @var CustomerClientInterface
     */
    private $customerClient;

    /**
     * @param CustomerClientInterface $customerClient
     *
     * @return $this
     */
    public function setCustomerClient(CustomerClientInterface $customerClient)
    {
        $this->customerClient = $customerClient;

        return $this;
    }

    /**
     * @param string $username
     *
     * @return User
     */
    public function loadUserByUsername($username)
    {
        $customerTransfer = new CustomerTransfer();
        $customerTransfer->setEmail($username);
        $customerTransfer = $this->customerClient->getCustomer($customerTransfer);

        return new User(
            $customerTransfer->getEmail(),
            $customerTransfer->getPassword(),
            ['ROLE_USER']
        );
    }

    /**
     * @param string $username
     */
    public function logout($username)
    {
        $customerTransfer = new CustomerTransfer();
        $customerTransfer->setEmail($username);

        $this->customerClient->logout($customerTransfer);
    }

    /**
     * @param UserInterface $user
     *
     * @return User
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return $class === 'Symfony\Component\Security\Core\User\User';
    }

}
