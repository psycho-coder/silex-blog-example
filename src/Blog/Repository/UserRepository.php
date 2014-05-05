<?php

namespace Blog\Repository;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Doctrine\DBAL\Connection;
use Doctrine\Common\Inflector\Inflector;

use Blog\Entity\User;

class UserRepository implements UserProviderInterface
{
    protected $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function loadUserByUsername($username)
    {
        $sql  = sprintf("SELECT * FROM %s WHERE username = ?", $this->getTableName());
        $stmt = $this->db->executeQuery($sql, array(strtolower($username)));

        if (!$userData = $stmt->fetch()) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        return $this->buildFromArray($userData);
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function save(User $user)
    {
        $data = array(
            'username' => $user->getUsername(),
            'salt'     => $user->getSalt(),
            'password' => $user->getPassword(),
            'roles'    => implode(',', $user->getRoles())
        );

        if ( $user->getId() ) {
            $this->db->update($this->getTableName(), $data, array('id' => $user->getId()));
        } else {
            $this->db->insert($this->getTableName(), $data);
            // set the id of saved user
            $user->setId($this->db->lastInsertId());
        }
    }

    public function supportsClass($class)
    {
        return $class === 'Blog\Entity\User';
    }

    public function getTableName()
    {
        return 'users';
    }    

    /**
     * Make User object from array
     * @param  array  $userData User data (ex: array('username' => foo))
     * @return User             User object
     */
    public function buildFromArray(array $userData)
    {
        $user = new User();

        foreach ($userData as $property => $value) 
        {
            if ( $property == 'roles' ) {
                $user->setRoles(explode(',', $value));
            } else {
                $method = Inflector::camelize('set_'.$property);
                $user->$method($value);                
            }
        }

        return $user;
    }
}