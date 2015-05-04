<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 14.04.15
 * Time: 15:34
 */

/**
 * Class PhonexaReportRequest
 * @property int type
 * @property int pageNumber
 * @property int rowsOnPage
 * @property string filterDateFrom
 * @property string filterDateTo
 * @property mixed data
 */
class PhonexaReportRequest extends PhonexaAbstractApiRequest
{
    const REQUEST_TYPE_SUMMARY_REPORT_BY_DATE = 1;
    const REQUEST_TYPE_SUMMARY_REPORT_BY_PHONE = 2;
    const REQUEST_TYPE_SUMMARY_REPORT_BY_LENDER = 3;

    private $_api = array(
        'api1' => array(
            'server_domain' => 'http://localhost',
            'routes' => array(
                self::REQUEST_TYPE_SUMMARY_REPORT_BY_DATE => array('url' => '/getStat/report'),
                self::REQUEST_TYPE_SUMMARY_REPORT_BY_PHONE => array('url' => '/getStat/report'),
                self::REQUEST_TYPE_SUMMARY_REPORT_BY_LENDER => array('url' => '/getStat/report')
            )
        )
    );

    public function __construct($secretKey, $apiVersion = 'api1', $requestId = null){
        $this->_fields = [
            'type' => self::REQUEST_TYPE_SUMMARY_REPORT_BY_DATE,
            'pageNumber' => 1,
            'rowsOnPage' => 20,
            'responseToAssocArray' => false
        ];
        parent::__construct($secretKey, $apiVersion, $requestId);
    }

    protected function _getApiParams(){
        return $this->_api;
    }

    protected function _checkRequiredParams(){
        if(!$this->type){
            return false;
        }
        return true;
    }

    /**
     * @return string 'api_key=<api_key>&report_type=<report_type>&request_id=<request_id>&request=<json_data>'
     */
    protected function _prepareRequest(){
        $requestData = array(
            'page_number' => $this->_fields['pageNumber'],
            'rows_on_page' => $this->_fields['rowsOnPage'],
            'filters' => array(
                'date' => array(
                    array(
                        'from' => $this->_fields['filterDateFrom'],
                        'to' => $this->_fields['filterDateTo']
                    )
                )
            )
        );

        $query = [
            'api_key' => $this->_getAPIKey(),
            'report_type' => $this->_fields['type'],
            'request_id' => $this->_requestId,
            'request' => json_encode($requestData)
        ];

        $request = http_build_query($query);
        return $request;
    }
} 