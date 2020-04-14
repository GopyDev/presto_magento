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

class Rainconcert_SurveyManager_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function getSurveyRelations( $surveyId)
    {
        $surveyQnRelations = Mage::getModel('surveymanager/surveyqnrelation')
                                            ->getRelationBySurveyId( $surveyId);
        
        return $surveyQnRelations;
    }
    
    public function getSurveyQnById( $qId){
        $surveyQn = Mage::getModel('surveymanager/surveyquestions')
                                            ->getQuestionById( $qId);
        
        return $surveyQn;
    }
    
    public function getSurveyAnswersByQid( $qId){
        $surveyAnswers = Mage::getModel('surveymanager/surveyanswer')
                                                ->getAnswerByQnId( $qId);
        
        return $surveyAnswers;
    }
    
    public function getAnsTypeByQid( $qId){
        
        $surveyAnsType = $this->getSurveyQnById( $qId)
                              ->getSurveyAnsType();
        
        return $surveyAnsType;
    }
    
    public function getNewLayoutBySurveyId( $surveyId){

        $survey = Mage::getModel('surveymanager/survey')->getSurveyDetailsById( $surveyId);
        $page_layout = $survey->getSurveyLayoutTemplate();
        
        switch($page_layout)
        {
            case 'empty':
                $page_layout = 'page/empty.phtml';
                break;
            case 'one_column':
                $page_layout = "page/1column.phtml";
                break;
            case 'two_columns_left':
                $page_layout = 'page/2columns-left.phtml';
                break;
            case 'two_columns_right':
                $page_layout = 'page/2columns-right.phtml';
                break;
            case 'three_columns':
                $page_layout = 'page/3columns.phtml';
                break;
            default:
                $page_layout = 'page/2columns-right.phtml';
        }

        return $page_layout;

    }
}
