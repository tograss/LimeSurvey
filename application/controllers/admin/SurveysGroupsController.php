<?php
/*
* LimeSurvey
* Copyright (C) 2007-2011 The LimeSurvey Project Team / Carsten Schmitz
* All rights reserved.
* License: GNU/GPL License v2 or later, see LICENSE.php
* LimeSurvey is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*
* Surveys Groups Controller
*/

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class SurveysGroupsController extends Survey_Common_Action
{

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function view($id)
    {
        $this->render('view',array(
            'model'=>$this->loadModel($id),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function create()
    {
        $model=new SurveysGroups;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['SurveysGroups']))
        {
            $model->attributes=$_POST['SurveysGroups'];
            $model->name = sanitize_paranoid_string($model->name);
            $model->created_by = $model->owner_uid = Yii::app()->user->id;
            if($model->save())
                $this->getController()->redirect(array('admin/survey/sa/listsurveys '));
        }

        $aData['model'] = $model;
        $aData['fullpagebar']['savebutton']['form'] = 'surveys-groups-form';

        $this->_renderWrappedTemplate('surveysgroups', 'create', $aData);
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function update($id)
    {
        $model=$this->loadModel($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['SurveysGroups']))
        {
            $model->attributes=$_POST['SurveysGroups'];
            if($model->save())
                $this->getController()->redirect($this->getController()->createUrl('admin/survey/sa/listsurveys').'#surveygroups');
        }
        $aData['model'] = $model;
        $oSurveySearch = new Survey('search');
        $oSurveySearch->gsid = $model->gsid;
        $aData['oSurveySearch'] = $oSurveySearch;

        $oTemplateOptions = TemplateConfiguration::getInstance(null, null, $model->gsid);
        $oTemplateOptions->bUseMagicInherit = false;
        $oTemplateOptionsReplacement = TemplateConfiguration::model()->findByPk($oTemplateOptions->id);
        $templateOptionPage           = $oTemplateOptionsReplacement->optionPage;

        $aData['templateOptionsModel'] = $oTemplateOptions;
        $aData['templateOptionPage'] = $templateOptionPage;

        $this->_renderWrappedTemplate('surveysgroups', 'update', $aData);
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function delete($id)
    {
        $oGroupToDelete = $this->loadModel($id);
        $sGroupTitle    = $oGroupToDelete->title;

        if (! $oGroupToDelete->hasSurveys){
            $oGroupToDelete->delete();

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if(!isset($_GET['ajax'])){
                Yii::app()->setFlashMessage( $sGroupTitle .' '. gT("was deleted."),'success');
                $this->getController()->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin/survey/sa/listsurveys '));
            }

        }else{
            Yii::app()->setFlashMessage(gT("You can't delete a group if it's not empty!"),'error');
            $this->getController()->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin/survey/sa/listsurveys '));
        }

    }

    /**
     * Lists all models.
     */
    public function index()
    {
        $model = new SurveysGroups('search');
        $aData['model'] = $model;
        $this->_renderWrappedTemplate('surveysgroups', 'index', $aData);
    }

    /**
     * Manages all models.
     */
    public function admin()
    {
        $model=new SurveysGroups('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['SurveysGroups']))
            $model->attributes=$_GET['SurveysGroups'];

        $this->render('admin',array(
            'model'=>$model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return SurveysGroups the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model=SurveysGroups::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param SurveysGroups $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if(isset($_POST['ajax']) && $_POST['ajax']==='surveys-groups-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}
