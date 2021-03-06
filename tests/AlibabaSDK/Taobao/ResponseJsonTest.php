<?php

namespace AlibabaSDK\Taobao;

/**
 * ResponseJson基础测试
 * @author Horse Luke
 *
 */
class ResponseJsonTest extends \PHPUnit_Framework_TestCase{
    
    public function testCreate(){
        $code = 200;
        $rawResult = '{"time_get_response":{"time":"2015-09-16 15:53:37","request_id":"alibabaalibabaalibaba"}}';
        
        $response = new ResponseJson();
        $response->create($code, $rawResult);
        
        $this->assertTrue($response->isOk());
        $this->assertEquals($rawResult, $response->getRawResult());
        
        $result = $response->getResult();
        $this->assertArrayHasKey('time', $result);
    }
    
    public function testCreateWithError(){
        $code = 403;
        $rawResult = '{"error_response":{"code":40,"msg":"Missing required arguments:content","request_id":"alibabaalibabaalibaba"}}';
        
        $response = new ResponseJson();
        $response->create($code, $rawResult);
        
        $this->assertFalse($response->isOk());
        $this->assertEquals($rawResult, $response->getRawResult());
    
        $error = $response->getError();
        $this->assertEquals('API_RETURN_ERROR_CODE', $error);
    }
    
    public function testCreateWithErrorSubCode(){
        $code = 403;
        $rawResult = '{"error_response":{"code":11,"msg":"Insufficient isv permissions","sub_code":"isv.permission-api-package-limit","sub_msg":"scope ids is 274 287","request_id":"alibabaalibaba"}}';
    
        $response = new ResponseJson();
        $response->create($code, $rawResult);
    
        $this->assertFalse($response->isOk());
        $this->assertEquals($rawResult, $response->getRawResult());
    
        $error = $response->getError(true);
        $this->assertEquals('API_RETURN_ERROR_CODE', $error['error']);
        $this->assertTrue(stripos($error['errorDetail'], 'Insufficient isv permissions') !== false);
    }
    
    public function testCreateWithErrorJSONErrorRootNode(){
        $code = 200;
        $rawResult = '{"code":400,"msg":"11","request_id":"22"}';
    
        $response = new ResponseJson();
        $response->create($code, $rawResult);

        
        $this->assertFalse($response->isOk());
        $this->assertEquals($rawResult, $response->getRawResult());
    
        $error = $response->getError();
        $this->assertEquals('PARSE_ERROR_RESPONSE_JSON_ROOT_NODE', $error);
    }
    
    public function testCreateWithErrorJSONError(){
        $code = 200;
        $rawResult = 'callback({"code":400,"msg":"11","request_id":"22"})';
    
        $response = new ResponseJson();
        $response->create($code, $rawResult);
    
    
        $this->assertFalse($response->isOk());
        $this->assertEquals($rawResult, $response->getRawResult());
    
        $error = $response->getError();
        $this->assertEquals('PARSE_ERROR_RESPONSE_JSON', $error);
    }
    
}