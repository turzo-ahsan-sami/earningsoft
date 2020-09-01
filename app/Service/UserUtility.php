<?php

namespace App\Service;

use App\ConstValue;
use App\Traits\ValidateRouteAccess;

/**
 * User Utility Class
 *
 * @author hafij <hafij.to@gmail.com>
 */
class UserUtility
{
    use ValidateRouteAccess;

    /**
     * Check usre is super admin
     *
     * @param User $user
     *
     * @return boolean
     *
     */
    public static function isSuperAdmin($user)
    {
        return ConstValue::USER_ID_SUPER_ADMIN == $user->id;
    }

    /**
     * Get roll id
     *
     * @param User $user
     *
     * @return int
     *
     */
    public static function getRoleIdByUser($user)
    {
        return $user->getRole()->roleId;
    }

    /**
     * This method only for hr module to check all data permission for a function
     *
     * @param User $user
     * @param int $generalFunctionId
     *
     * @return boolean
     *
     */
    public static function hasAccessAllData($user, $generalFunctionId)
    {
        return EasyCode::chkHasAccess($user->id, ConstValue::MODULE_ID_HR, $generalFunctionId, ConstValue::SUB_FUNCTION_ID_ALL_DATA);
    }

    /**
     * Has access permission in particular module, function and sub function
     *
     * @param User $user
     * @param int $moduleId
     * @param int $functionId
     * @param int $subFunctionId
     *
     * @return boolean
     *
     */
    public static function hasAccessPermission($user, $moduleId, $functionId, $subFunctionId)
    {
        return ValidateRouteAccess::hasAccess($user->id, $moduleId, $functionId, $subFunctionId);
    }

    /**
     * Has access permission in particular function
     *
     * @param User $user
     * @param int $moduleId
     * @param string $functionCode
     * @param int $subFunctionId
     *
     * @return boolean
     *
     */
    public static function hasPermissionToAccess($user, $moduleId, $functionCode, $subFunctionId)
    {
        return ValidateRouteAccess::hasAccessByFunctionCode($user->id, $moduleId, $functionCode, $subFunctionId);
    }

}
