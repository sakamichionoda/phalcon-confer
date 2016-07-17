<?php

namespace MicheleAngioni\PhalconConfer;

use MicheleAngioni\PhalconConfer\Models\Roles;
use MicheleAngioni\PhalconConfer\Models\TeamsRoles;
use MicheleAngioni\PhalconConfer\Tests\Users;

trait ConferTeamTrait
{
    /**
     * Check if input Team User has input Role.
     *
     * @param  int $idUser
     * @param  string $roleName
     *
     * @return bool
     */
    public function userHasRole($idUser, $roleName)
    {
        foreach ($this->getRolesPivot([
            "users_id = :users_id:",
            "bind" => [
                "users_id" => $idUser
            ]
        ]) as $teamRole) {
            $role = $teamRole->getRole();

            if ($role->getName() == $roleName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if input Team User has input Permission.
     *
     * @param  int $idUser
     * @param  string $permissionName
     *
     * @return bool
     */
    public function userCan($idUser, $permissionName)
    {
        foreach ($this->getRolesPivot([
            "users_id = :users_id:",
            "bind" => [
                "users_id" => $idUser
            ]
        ]) as $teamRole) {
            $role = $teamRole->getRole();

            foreach ($role->getPermissions() as $permission) {
                if ($permission->getName() == $permissionName) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Attach input Role to the Team.
     * Return true on success.
     *
     * @param  int $idUser
     * @param  Roles $role
     *
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
     *
     * @return bool
     */
    public function attachUserRole($idUser, Roles $role)
    {
        // Check if input User is already attached to the Team. In case, update it

        foreach ($this->getRolesPivot([
            "users_id = :users_id:",
            "bind" => [
                "users_id" => $idUser
            ]
        ]) as $teamRole) {
            if ($teamRole->getUsersId() == $idUser) {
                $teamRole->setRolesId($role->getId());
                return true;
            }
        }

        // Create the new pivot record

        $teamRole = new TeamsRoles();
        $teamRole->setTeamId($this->getId());
        $teamRole->setUsersId($idUser);
        $teamRole->setRolesId($role->getId());

        try {
            $result = $teamRole->save();
        } catch (\Exception $e) {
            throw new \RuntimeException("Caught RuntimeException in " . __METHOD__ . ' at line ' . __LINE__ . ': ' . $e->getMessage());
        }

        if (!$result) {
            $errorMessages = implode('. ', $teamRole->getMessages());
            throw new \UnexpectedValueException("Caught UnexpectedValueException in " . __METHOD__ . ' at line ' . __LINE__ . ': $role ' . $role->id . ' cannot be attached to user ' . $this->id . '. Error messages: ' . $errorMessages);
        }

        return true;
    }

    /**
     * Detach input Role from the Team.
     * Return true on success.
     *
     * @param  Roles $role
     *
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
     *
     * @return bool
     */
    public function detachRole(Roles $role)
    {
        // Check if input Role is attached to the Team

        $rolesPivot = $this->getRolesPivot();

        foreach ($rolesPivot as $rolePivot) {
            if ($rolePivot->getRolesId() == $role->id) {
                try {
                    $result = $rolePivot->delete();
                } catch (\Exception $e) {
                    throw new \RuntimeException("Caught RuntimeException in " . __METHOD__ . ' at line ' . __LINE__ . ': ' . $e->getMessage());
                }

                if (!$result) {
                    $errorMessages = implode('. ', $this->getMessages());
                    throw new \UnexpectedValueException("Caught UnexpectedValueException in " . __METHOD__ . ' at line ' . __LINE__ . ': $role ' . $role->id . ' cannot be detached from team ' . $this->id . '. Error messages: ' . $errorMessages);
                }
            }
        }

        return true;
    }
}