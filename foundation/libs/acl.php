<?php

/* 
 * Projects4Me Community Edition is an open source project management software 
 * developed by PROJECTS4ME Inc. Copyright (C) 2015-2016 PROJECTS4ME Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 (GNU AGPL v3) as
 * published be the Free Software Foundation with the addition of the following 
 * permission added to Section 15 as permitted in Section 7(a): FOR ANY PART OF 
 * THE COVERED WORK IN WHICH THE COPYRIGHT IS OWNED BY PROJECTS4ME Inc., 
 * Projects4Me DISCLAIMS THE WARRANTY OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT 
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU AGPL v3 for more details.
 * 
 * You should have received a copy of the GNU AGPL v3 along with this program; 
 * if not, see http://www.gnu.org/licenses or write to the Free Software 
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 * 
 * You can contact PROJECTS4ME, Inc. at email address contact@projects4.me.
 * 
 * The interactive user interfaces in modified source and object code versions 
 * of this program must display Appropriate Legal Notices, as required under 
 * Section 5 of the GNU AGPL v3.
 * 
 * In accordance with Section 7(b) of the GNU AGPL v3, these Appropriate Legal 
 * Notices must retain the display of the "Powered by Projects4Me" logo. If the 
 * display of the logo is not reasonably feasible for technical reasons, the 
 * Appropriate Legal Notices must display the words "Powered by Projects4Me".
 */

namespace Foundation;

use Phalcon\Mvc\Model\Query as Query;

/**
 * @todo Fill in Documentation
 */
class Acl{
    
    /**
     * 
     * @global type $di
     * @param type $requester
     * @param type $resource
     * @param type $control
     * @param type $projectId
     * @return int
     * @todo use objects and queryBuilder instead of query
     */
    public static function hasProjectAccess($userId,$resource,$control,$projectId)
    {
        $access = 0;
        $max = $access;
        
        if (!(is_null($projectId) || empty($projectId) || $projectId === 0))
        {
            if (isset($userId) && !empty($userId))
            {
                $ProjectsRoles = \ProjectsRoles::find(array("projectId = '".$projectId."' AND userId='".$userId."'"));
                foreach($ProjectsRoles as $ProjectRole)
                {
                    $permission = self::roleHasAccess($ProjectRole->roleId, $resource, $control);
                    if ($permission > $max)
                    {
                        $max = $permission;
                    }
                }
                $access = $max;
            }
        }

       return $access;
    }
    
    public static function getProjects($userId,$resource,$control)
    {
        $projects = array();
        
        if (isset($userId) && !empty($userId))
        {
            $ProjectsRoles = \ProjectsRoles::find(array("userId='".$userId."'"));
            foreach($ProjectsRoles as $ProjectRole)
            {
                $permission = self::roleHasAccess($ProjectRole->roleId, $resource, $control);
                if ($permission > 0)
                {
                    $projects[] = $ProjectRole->projectId;
                }
            }
        }
        return $projects;
    }
    
    public static function roleHasAccess($roleId,$resource='list',$control='read')
    {
        global $di;
        $access = 0;
        if (isset($roleId) && !empty($roleId))
        {
            $phql = "SELECT parent.* FROM Resources AS child ".
                    "INNER JOIN Resources as parent ".
                    "ON child.[left] BETWEEN parent.[left] AND parent.[right] ".
                    "WHERE child.entity='".$resource."'";

            $query = new Query($phql,$di);
            $resourceObjs = $query->execute();
            $length = count($resourceObjs);

            if ($length > 0)
            {
                for($i=($length-1);$i>=0;$i--)
                {
                    $phql = "SELECT _".$control." as permission FROM Permissions ".
                            "WHERE resourceId='".($resourceObjs[$i]->id)."' AND roleId='".$roleId."'";

                    $query = new Query($phql,$di);
                    $permissions = $query->execute();
                    if (isset($permissions[0])){
                        $permission = (int) $permissions[0]->permission;
                        if ($permission != 0)
                        {
                            $access = $permission;
                            break;
                        }
                    }
                }
            }
        }
        return $access;
    }
}

