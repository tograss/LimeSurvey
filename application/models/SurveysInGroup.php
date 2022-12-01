<?php

/**
 * Class SurveysGroups
 * @inheritdoc
 * Used for Permission on survey inside group :
 *
 */
class SurveysInGroup extends SurveysGroups implements PermissionInterface
{
    use PermissionTrait;

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return SurveysGroups the static model class
     */
    public static function model($className = __CLASS__)
    {
        /** @var self $model */
        $model = parent::model($className);
        return $model;
    }

    /**
     * Get Permission data for Permission object
     * @param string $key
     * @return array
     */
    public static function getPermissionData()
    {
        $aPermission = array(
            'surveys' => array(
                'create' => false,
                'read' => true,
                'update' => true,
                'delete' => true,
                'import' => false,
                'export' => true,
                'title' => gT("Surveys in this group"),
                'description' => gT("Permission to access surveys in this group. To see a survey in the list the read/view permission must be given."),
                'img' => ' fa fa-edit',
            ),
        );
        return $aPermission;
    }

    /**
     * Get the owner id of this Survey group
     * Used for Permission
     * @return integer|null
     */
    public function getOwnerId()
    {
        if (!App()->getConfig('ownerManageAllSurveysInGroup')) {
            return null;
        }
        return $this->owner_id;
    }

    /**
     * @inheritdoc
     * No minimal permission : must be set or get it via owner (or global)
     */
    public static function getMinimalPermissionRead()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function hasPermission($sPermission, $sCRUD = 'read', $iUserID = null)
    {
        /* Have surveys permission : always set to true */
        if (Permission::model()->hasPermission(0, 'global', 'surveys', $sCRUD, $iUserID)) {
            return true;
        }
        /* Specific need gsid */
        if (!$this->gsid) {
            return false;
        }
        /* Create and import need access to SurveysGroup */
        if ($sCRUD == 'create' or $sCRUD == 'import') {
            return !empty(SurveysGroups::model()->withpermission()->findByPk($this->gsid));
        }
        /* Finally : return specific one */
        return Permission::model()->hasPermission($this->gsid, 'surveysingroup', $sPermission, $sCRUD, $iUserID);
    }
}
