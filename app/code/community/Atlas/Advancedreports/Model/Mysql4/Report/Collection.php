<?php

/**
 * Atlas Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Atlas Commercial License
 * that is available through the world-wide-web at this URL:
 *
 * @copyright   Copyright (c) 2015 Atlas Extensions
 * @license     Commercial
 */
class Atlas_Advancedreports_Model_Mysql4_Report_Collection
{

    protected $_from;
    protected $_to;
    protected $_period;
    protected $_timezone;
    protected $_model;
    protected $_intervals;
    protected $_pageSize;
    protected $_storeIds;

    protected function _construct()
    {
        
    }

    public function setPeriod($period)
    {

        $this->_period = $period;
    }

    public function setInterval($from, $to)
    {
        $this->_from = $from;
        $this->_to = $to;
    }

    public function getIntervals()
    {
        if (!$this->_intervals)
        {
            $this->_intervals = array();
            if (!$this->_from && !$this->_to)
            {
                return $this->_intervals;
            }
            $dateStart = new Zend_Date($this->_from);
            $dateEnd = new Zend_Date($this->_to);


            $t = array();
            $firstInterval = true;
            while ($dateStart->compare($dateEnd) <= 0) {

                switch ($this->_period) {
                    case 'day' :
                        $t['title'] = $dateStart->toString(Mage::app()->getLocale()->getDateFormat());
                        $t['start'] = $dateStart->toString('yyyy-MM-dd HH:mm:ss');
                        $t['end'] = $dateStart->toString('yyyy-MM-dd 23:59:59');
                        $dateStart->addDay(1);
                        break;
                    case 'month':
                        $t['title'] = $dateStart->toString('MM/yyyy');
                        $t['start'] = ($firstInterval) ? $dateStart->toString('yyyy-MM-dd 00:00:00') : $dateStart->toString('yyyy-MM-01 00:00:00');

                        $lastInterval = ($dateStart->compareMonth($dateEnd->getMonth()) == 0);

                        $t['end'] = ($lastInterval) ? $dateStart->setDay($dateEnd->getDay())
                                        ->toString('yyyy-MM-dd 23:59:59') : $dateStart->toString('yyyy-MM-' . date('t', $dateStart->getTimestamp()) . ' 23:59:59');

                        $dateStart->addMonth(1);

                        if ($dateStart->compareMonth($dateEnd->getMonth()) == 0)
                        {
                            $dateStart->setDay(1);
                        }

                        $firstInterval = false;
                        break;
                    case 'year':
                        $t['title'] = $dateStart->toString('yyyy');
                        $t['start'] = ($firstInterval) ? $dateStart->toString('yyyy-MM-dd 00:00:00') : $dateStart->toString('yyyy-01-01 00:00:00');

                        $lastInterval = ($dateStart->compareYear($dateEnd->getYear()) == 0);

                        $t['end'] = ($lastInterval) ? $dateStart->setMonth($dateEnd->getMonth())
                                        ->setDay($dateEnd->getDay())->toString('yyyy-MM-dd 23:59:59') : $dateStart->toString('yyyy-12-31 23:59:59');
                        $dateStart->addYear(1);
                        if ($dateStart->compareYear($dateEnd->getYear()) == 0)
                        {
                            $dateStart->setMonth(1)->setDay(1);
                        }
                        $firstInterval = false;
                        break;
                }
                $this->_intervals[$t['title']] = $t;
            }
        }
        return $this->_intervals;
    }

    /**
     * Return date periods
     *
     * @return array
     */
    public function getPeriods()
    {
        return array(
            'day' => Mage::helper('reports')->__('Day'),
            'month' => Mage::helper('reports')->__('Month'),
            'year' => Mage::helper('reports')->__('Year')
        );
    }

    public function setStoreIds($storeIds)
    {

        $this->_storeIds = $storeIds;
    }

    public function getStoreIds()
    {
        return $this->_storeIds;
    }

    public function getSize()
    {
        return count($this->getIntervals());
    }

    public function setPageSize($size)
    {
        $this->_pageSize = $size;
        return $this;
    }

    public function getPageSize()
    {
        return $this->_pageSize;
    }

    public function initReport($modelClass)
    {
        $this->_model = Mage::getModel('reports/report')
                ->setPageSize($this->getPageSize())
                ->setStoreIds($this->getStoreIds())
                ->initCollection($modelClass);
    }

    public function getReportFull($from, $to)
    {
        return $this->_model->getReportFull($this->timeShift($from), $this->timeShift($to));
    }

    public function getReport($from, $to)
    {
        return $this->_model->getReport($this->timeShift($from), $this->timeShift($to));
    }

    public function timeShift($datetime)
    {
        if ($this->_timezone == 'local')
        {
            $dateObj = new Zend_Date($datetime, null, Mage::app()->getLocale()->getLocaleCode());
            $dateObj->setTimezone('America/Los_Angeles');
            $dateObj->set($datetime, Varien_Date::DATETIME_INTERNAL_FORMAT);
            $dateObj->setTimezone('UTC');
            return $dateObj->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        }
        elseif ($this->_timezone == 'PST4PM')
        {
            $dateObj = new Zend_Date($datetime, null, Mage::app()->getLocale()->getLocaleCode());
            $dateObj->setTimezone('EST');
            $dateObj->set($datetime, Varien_Date::DATETIME_INTERNAL_FORMAT);
            $dateObj->setTimezone('America/Denver');
            return $dateObj->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        }
        else
        {//UTC
            return $datetime;
        }
    }

    public function setIsUTC($timeZone)
    {
        $this->_timezone = $timeZone;
    }

    public function getIsUTC()
    {
        return array(
            'local' => Mage::helper('reports')->__('Local Time (PST)'),
            'UTC' => Mage::helper('reports')->__('UTC'),
            'PST4PM' => Mage::helper('reports')->__('PST 4PM')
        );
    }

}
