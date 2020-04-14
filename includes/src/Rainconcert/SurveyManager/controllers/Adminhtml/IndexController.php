<?php
/**
* Rainconcert Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * Rainconcert does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   Rainconcert
 * @package    Rainconcert_SurveyManager
 * @version    1.1.1
 */

class Rainconcert_SurveyManager_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action {

    public function preDispatch() {
        parent::preDispatch();
    }

    protected function _initAction() {
        $this->loadLayout()->_setActiveMenu("surveymanager/surveymanager")
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Surveys'), Mage::helper('adminhtml')->__('Manage Surveys'));
        return $this;
    }

    public function indexAction() {
        
        $this->_initAction();
        $this->renderLayout();
    }
    
    public function editAction() {

        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('surveymanager/survey')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('survey_data', $model);

            $this->loadLayout();

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('surveymanager/adminhtml_survey_edit'))
                    ->_addLeft($this->getLayout()->createBlock('surveymanager/adminhtml_survey_edit_tabs'));

            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('surveymanager')->__('No Survey'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {

        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('surveymanager/survey')->load($id);

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('survey_data', $model);

        $this->loadLayout();
        
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('surveymanager/adminhtml_survey_edit'))
                    ->_addLeft($this->getLayout()->createBlock('surveymanager/adminhtml_survey_edit_tabs'));

        $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);

        $this->renderLayout();
    }

    

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('surveymanager/survey');
            
            $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));
            
            try {

                $format = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
                if (isset($data['created_time']) && $data['created_time']) {
                    $dateFrom = Mage::app()->getLocale()->date($data['created_time'], $format);
                    $model->setCreatedTime(Mage::getModel('core/date')->gmtDate(null, $dateFrom->getTimestamp()));
                    $model->setUpdateTime(Mage::getModel('core/date')->gmtDate());
                } else {
                    $model->setCreatedTime(Mage::getModel('core/date')->gmtDate());
                }
                $model->save();
                
                $i=1;
                if($model->getId()){
                    
                    $url = Mage::getModel('core/url_rewrite')
                                                        ->getCollection()
                                                        ->addFieldToFilter('is_system',0)   
                                                        ->addFieldToFilter('id_path',$data['survey_url']);
                    
                    if( count($url)==0){
                         Mage::getModel('core/url_rewrite')
                                      ->setIsSystem(0)
                                      ->setStoreId(1)
                                      ->setIdPath($data['survey_url'])
                                      ->setTargetPath('surveymanager/index/index/survey_id/'.$model->getId())
                                      ->setRequestPath($data['survey_url'].'.html')
                                      ->save();
                    }
                    
                    $modelSurveyQnRelations = Mage::getModel('surveymanager/surveyqnrelation')
                                                        ->getCollection()
                                                        ->addFieldToFilter('survey_id',$model->getId());
                    
                    foreach($modelSurveyQnRelations as $modelSurveyQnRelation){
                        $modelSurveyQnRelation->delete();
                    }
                    
                    while($i<=$data['highestID']){
                        if( $data['survey_qn_id_'.$i]!=0)
                        {
                            $modelSurvey = Mage::getModel('surveymanager/surveyqnrelation');

                            $modelSurvey->setData('survey_id',$model->getId());
                            $modelSurvey->setData('survey_qn_id',$data['survey_qn_id_'.$i]);
                            $modelSurvey->save();
                        }                        
                        $i++;
                    }
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('surveymanager')->__('Survey was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }         
            
        }
        
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('surveymanager')->__('Unable to find survey to save'));
        $this->_redirect('*/*/');
    }
    
    protected function _initQuestionAction() {
        $this->loadLayout()->_setActiveMenu("surveymanager/surveymanager")
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Survey Questions'), Mage::helper('adminhtml')->__('Manage Survey Questions'));
        return $this;
    }

    public function questionsAction() {
        
        $this->_initQuestionAction();
        $this->renderLayout();
    }
    
    public function addQuestionsAction(){
        $id = $this->getRequest()->getParam('id');

        $model = Mage::getModel('surveymanager/surveyquestions')->load($id);
        
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('survey_question_data', $model);

        $this->loadLayout();
        
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('surveymanager/adminhtml_questions_edit'))
                    ->_addLeft($this->getLayout()->createBlock('surveymanager/adminhtml_questions_edit_tabs'));

        $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);

        $this->renderLayout();
    } 
    
    public function editQuestionsAction() {

        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('surveymanager/surveyquestions')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('survey_question_data', $model);

            $this->loadLayout();

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('surveymanager/adminhtml_questions_edit'))
                    ->_addLeft($this->getLayout()->createBlock('surveymanager/adminhtml_questions_edit_tabs'));

            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('surveymanager')->__('No Survey Questions'));
            $this->_redirect('*/*/');
        }
    }
    
    public function saveQnAnsAction() {
        if ($data = $this->getRequest()->getPost()) {
            
            $model = Mage::getModel('surveymanager/surveyquestions');
            
            $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));

            try {

                $format = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
                if (isset($data['created_time']) && $data['created_time']) {
                    $dateFrom = Mage::app()->getLocale()->date($data['created_time'], $format);
                    $model->setCreatedTime(Mage::getModel('core/date')->gmtDate(null, $dateFrom->getTimestamp()));
                    $model->setUpdateTime(Mage::getModel('core/date')->gmtDate());
                } else {
                    $model->setCreatedTime(Mage::getModel('core/date')->gmtDate());
                }
                $model->save();                
                
                if($model->getId()){
                    
                    $modelAnsQnIds = Mage::getModel('surveymanager/surveyanswer')
                                                            ->getCollection()
                                                            ->addFieldToFilter('survey_ans_qn_id',$model->getId());
                    
                    foreach($modelAnsQnIds as $modelAnswer){
                        $modelAnswer->delete();
                    }
                    
                    $i=1;
                    while($i<=$data['highestID']){
                        $modelSurveyAnswer = Mage::getModel('surveymanager/surveyanswer');

                        if($data['survey_ans_id_'.$i]!='')
                        {
                            $modelSurveyAnswer->setSurveyAnsQnId($model->getId())
                                        ->setSurveyAnsTitle($data['survey_ans_id_'.$i])
                                        ->setIsActive(1);

                            if (isset($data['created_time']) && $data['created_time']) {
                                $dateFrom = Mage::app()->getLocale()->date($data['created_time'], $format);
                                $modelSurveyAnswer->setCreatedTime(Mage::getModel('core/date')->gmtDate(null, $dateFrom->getTimestamp()));
                                $modelSurveyAnswer->setUpdateTime(Mage::getModel('core/date')->gmtDate());
                            } else {
                                $modelSurveyAnswer->setCreatedTime(Mage::getModel('core/date')->gmtDate());
                            }
                            $modelSurveyAnswer->save();
                        }
                        $i++;
                    }
                }    
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('surveymanager')->__('Question was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/editQuestions', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/questions');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/editQuestions', array('id' => $this->getRequest()->getParam('id')));
                return;
            }         
            
        }
        
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('surveymanager')->__('Unable to find question to save'));
        $this->_redirect('*/*/');
    }
    
    public function deleteQuestionAction() {
        $questionId = (int) $this->getRequest()->getParam('id');
        if ($questionId) {
            try {
                $model = Mage::getModel('surveymanager/surveyquestions')->load($questionId);
                $model->delete(); 
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Question was successfully deleted'));
                $this->_redirect('*/*/questions');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/editQuestions', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
    
    public function deleteAction() {
        $postId = (int) $this->getRequest()->getParam('id');
        if ($postId) {
            try {
                $this->_postDelete($postId);
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Survey was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $surveyIds = $this->getRequest()->getParam('survey');
        if (!is_array($surveyIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select surveys'));
        } else {
            try {
                foreach ($surveyIds as $surveyId) {
                    $this->_postDelete($surveyId);
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($surveyIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    protected function _postDelete($surveyId) {
        $model = Mage::getModel('surveymanager/survey')->load($surveyId);
        $model->delete();        
    }

    public function massStatusAction() {
        $surveyIds = $this->getRequest()->getParam('survey');
        if (!is_array($surveyIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select survey'));
        } else {
            try {

                foreach ($surveyIds as $surveyId) {
                    $survey = Mage::getModel('surveymanager/survey')
                            ->load($surveyId)
                            ->setIsActive($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($surveyIds))
                );
            } catch (Exception $e) {

                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    

}
