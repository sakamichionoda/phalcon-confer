<?php

namespace MicheleAngioni\PhalconConfer;

use MicheleAngioni\PhalconConfer\Models\Permissions;
use MicheleAngioni\PhalconConfer\Models\Roles;
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\User\Component;

class Confer extends Component
{
    /**
     * @var RoleService
     */
    private $roleService;

    /**
     * @var PermissionService
     */
    private $permissionService;

    public function __construct(RoleService $roleService, PermissionService $permissionService)
    {
        $this->roleService = $roleService;

        $this->permissionService = $permissionService;
    }

    /**
     * Return all Roles.
     *
     * @return ResultsetInterface
     */
    public function getRoles(): ResultsetInterface
    {
        return $this->roleService->all();
    }

    /**
     * Retrieve and return the input Role by id or name.
     *
     * @param int|string $name
     * @return Roles|false
     */
    public function getRole($name)
    {
        if (is_int($name)) {
            return $this->roleService->find($name);
        } else {
            return $this->roleService->findByName($name);
        }
    }

    /**
     * Create a new Role.
     *
     * @param string $name
     * @throws \UnexpectedValueException
     *
     * @return Roles
     */
    public function createRole(string $name): Roles
    {
        return $this->roleService->createNew([
            'name' => $name
        ]);
    }

    /**
     * Delete input Role.
     * Return true on success, false on failure or if Role has not been found.
     *
     * @param int|string $role
     * @return bool
     */
    public function deleteRole($role): bool
    {
        $role = $this->getRole($role);

        return $role ? $role->delete() : false;
    }

    /**
     * Return all Permissions.
     *
     * @return ResultsetInterface
     */
    public function getPermissions(): ResultsetInterface
    {
        return $this->permissionService->all();
    }

    /**
     * Retrieve and return the input Permission by id or name.
     *
     * @param int|string $name
     * @return Permissions|false
     */
    public function getPermission($name)
    {
        if (is_int($name)) {
            return $this->permissionService->find($name);
        } else {
            return $this->permissionService->findByName($name);
        }
    }

    /**
     * Create a new Permission.
     *
     * @param  string $name
     * @throws \UnexpectedValueException
     *
     * @return Permissions
     */
    public function createPermission(string $name): Permissions
    {
        return $this->permissionService->createNew([
            'name' => $name
        ]);
    }

    /**
     * Delete input Permission.
     * Return true on success, false on failure or if Permission has not been found.
     *
     * @param int|string $permission
     * @return bool
     */
    public function deletePermission($permission): bool
    {
        $permission = $this->getPermission($permission);

        return $permission ? $permission->delete() : false;
    }
}
