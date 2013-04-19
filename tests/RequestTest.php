<?php

namespace DuoAuth;

require_once 'MockClient.php';

class TestableRequest extends \DuoAuth\Request
{
    public function getCanonParams() {
        return parent::getCanonParams();
    }

    public function getCanonRequest() {
        return parent::getCanonRequest();
    }
}


class RequestTest extends \PHPUnit_Framework_TestCase
{
    private $request = null;

    public function setUp()
    {
        $client = new MockClient();
        $this->request = new TestableRequest($client);
    }

    /**
     * Test get/set of secret key
     */
    public function testRequestSetSecretKey()
    {
        $this->request->setSecretKey('test');
        $this->assertEquals(
            'test',
            $this->request->getSecretKey()
        );
    }

    /**
     * Test the get/set of integration key
     */
    public function testRequestSetIntKey()
    {
        $this->request->setIntKey('test');
        $this->assertEquals(
            'test',
            $this->request->getIntKey()
        );
    }

    /**
     * Test get/set of api hostname
     */
    public function testRequestSetApiHostname()
    {
        $this->request->setHostname('test');
        $this->assertEquals(
            'test',
            $this->request->getHostname()
        );
    }

    /**
     * Test the get/set of HTTP method
     */
    public function testRequestSetHttpMethod()
    {
        $this->request->setMethod('POST');
        $this->assertEquals(
            'POST',
            $this->request->getMethod()
        );
    }

    /**
     * Test the get/set of the path for request
     */
    public function testRequestSetPathDefault()
    {
        $this->request->setPath('/foo/bar');
        $this->assertEquals(
            '/foo/bar',
            $this->request->getPath()
        );
    }

    /**
     * Test the get/set of request path with custom extension
     */
    public function testRequestSetPathXml()
    {
        $this->request->setPath('/foo/bar', 'xml');
        $this->assertEquals(
            '/foo/bar.xml',
            $this->request->getPath()
        );
    }

    /**
     * Test the get/set of parameters on the request
     */
    public function testRequestSetParams()
    {
        $params = array('test' => true);
        $this->request->setParams($params);
        $this->assertEquals(
            $params,
            $this->request->getParams()
        );
    }

    /**
     * Test the get/set of additional options to add to the hash
     */
    public function testRequestSetHashOptions()
    {
        $params = array('Date' => date('r'));
        $this->request->setHashOptions($params);
        $this->assertEquals(
            $params,
            $this->request->getHashOptions()
        );
    }

    /**
     * Test the get/set of additonal parameters on request
     */
    public function testRequestSetParamsAdditional()
    {
        $params = array('test' => true);
        $this->request->setParams($params);
        $this->assertEquals(
            $params,
            $this->request->getParams()
        );
        // add another
        $this->request->setParam('foo', 'bar');
        $this->assertEquals(
            array('test' => true, 'foo' => 'bar'),
            $this->request->getParams()
        );
    }

    public function testCanonParamsSimple()
    {
        $params = array('realname' => 'First Last', 'username' => 'root');
        $this->request->setParams($params);
        $this->assertEquals(
            'realname=First%20Last&username=root',
            $this->request->getCanonParams($params)
        );
    }

    public function testCanonParamsZeroParams()
    {
        $params = array();
        $this->request->setParams($params);
        $this->assertEquals(
            '',
            $this->request->getCanonParams($params)
        );
    }

    public function testCanonParamsOneParam()
    {
        $params = array('realname' => 'First Last');
        $this->request->setParams($params);
        $this->assertEquals(
            'realname=First%20Last',
            $this->request->getCanonParams($params)
        );
    }

    public function testCanonParamsPrintableASCIICharacters()
    {
        $params = array(
            'digits' => '0123456789',
            'letters' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'punctuation' => '!"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~',
            'whitespace' => "\t\n\x0b\x0c\r "
        );
        $this->request->setParams($params);
        $this->assertEquals(
            'digits=0123456789&letters=abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ&punctuation=%21%22%23%24%25%26%27%28%29%2A%2B%2C-.%2F%3A%3B%3C%3D%3E%3F%40%5B%5C%5D%5E_%60%7B%7C%7D~&whitespace=%09%0A%0B%0C%0D%20',
            $this->request->getCanonParams($params)
        );
    }

    public function testCanonParamsUnicodeFuzzValues()
    {
        $params = '{"bar": "\u2815\uaaa3\u37cf\u4bb7\u36e9\ucc05\u668e\u8162\uc2bd\ua1f1", "baz": "\u0df3\u84bd\u5669\u9985\ub8a4\uac3a\u7be7\u6f69\u934a\ub91c", "foo": "\ud4ce\ud6d6\u7938\u50c0\u8a20\u8f15\ufd0b\u8024\u5cb3\uc655", "qux": "\u8b97\uc846-\u828e\u831a\uccca\ua2d4\u8c3e\ub8b2\u99be"}';
        $params = json_decode($params, true);
        $this->request->setParams($params);
        $this->assertEquals(
            'bar=%E2%A0%95%EA%AA%A3%E3%9F%8F%E4%AE%B7%E3%9B%A9%EC%B0%85%E6%9A%8E%E8%85%A2%EC%8A%BD%EA%87%B1&baz=%E0%B7%B3%E8%92%BD%E5%99%A9%E9%A6%85%EB%A2%A4%EA%B0%BA%E7%AF%A7%E6%BD%A9%E9%8D%8A%EB%A4%9C&foo=%ED%93%8E%ED%9B%96%E7%A4%B8%E5%83%80%E8%A8%A0%E8%BC%95%EF%B4%8B%E8%80%A4%E5%B2%B3%EC%99%95&qux=%E8%AE%97%EC%A1%86-%E8%8A%8E%E8%8C%9A%EC%B3%8A%EA%8B%94%E8%B0%BE%EB%A2%B2%E9%A6%BE',
            $this->request->getCanonParams($params)
        );
    }

    public function testCanonParamsUnicodeFuzzKeysAndValues()
    {
        $params = '{"\u2815\uaaa3\u37cf\u4bb7": "\u2815\uaaa3\u37cf\u4bb7\u36e9\ucc05\u668e\u8162\uc2bd\ua1f1", "\u5669\u9985\ub8a4": "\u0df3\u84bd\u5669\u9985\ub8a4\uac3a\u7be7\u6f69\u934a\ub91c", "\ud4ce\ud6d6\u7938\u50c0": "\ud4ce\ud6d6\u7938\u50c0\u8a20\u8f15\ufd0b\u8024\u5cb3\uc655", "\uc846-\u828e\u831a\uccca\ua2d4": "\u8b97\uc846-\u828e\u831a\uccca\ua2d4\u8c3e\ub8b2\u99be"}';
        $params = json_decode($params, true);
        $this->request->setParams($params);
        $this->assertEquals(
            '%E2%A0%95%EA%AA%A3%E3%9F%8F%E4%AE%B7=%E2%A0%95%EA%AA%A3%E3%9F%8F%E4%AE%B7%E3%9B%A9%EC%B0%85%E6%9A%8E%E8%85%A2%EC%8A%BD%EA%87%B1&%E5%99%A9%E9%A6%85%EB%A2%A4=%E0%B7%B3%E8%92%BD%E5%99%A9%E9%A6%85%EB%A2%A4%EA%B0%BA%E7%AF%A7%E6%BD%A9%E9%8D%8A%EB%A4%9C&%EC%A1%86-%E8%8A%8E%E8%8C%9A%EC%B3%8A%EA%8B%94=%E8%AE%97%EC%A1%86-%E8%8A%8E%E8%8C%9A%EC%B3%8A%EA%8B%94%E8%B0%BE%EB%A2%B2%E9%A6%BE&%ED%93%8E%ED%9B%96%E7%A4%B8%E5%83%80=%ED%93%8E%ED%9B%96%E7%A4%B8%E5%83%80%E8%A8%A0%E8%BC%95%EF%B4%8B%E8%80%A4%E5%B2%B3%EC%99%95',
            $this->request->getCanonParams($params)
        );
    }

    public function testCanonRequestV1()
    {
        $this->request->setHostname('foO.BAr52.cOm');
        $this->request->setMethod('PoSt');
        $this->request->setPath('/Foo/BaR2/qux');

        $params = '{"\u2815\uaaa3\u37cf\u4bb7": "\u2815\uaaa3\u37cf\u4bb7\u36e9\ucc05\u668e\u8162\uc2bd\ua1f1", "\u5669\u9985\ub8a4": "\u0df3\u84bd\u5669\u9985\ub8a4\uac3a\u7be7\u6f69\u934a\ub91c", "\ud4ce\ud6d6\u7938\u50c0": "\ud4ce\ud6d6\u7938\u50c0\u8a20\u8f15\ufd0b\u8024\u5cb3\uc655", "\uc846-\u828e\u831a\uccca\ua2d4": "\u8b97\uc846-\u828e\u831a\uccca\ua2d4\u8c3e\ub8b2\u99be"}';
        $params = json_decode($params, true);
        $this->request->setParams($params);

        $this->assertEquals(
            "POST\nfoo.bar52.com\n/Foo/BaR2/qux\n%E2%A0%95%EA%AA%A3%E3%9F%8F%E4%AE%B7=%E2%A0%95%EA%AA%A3%E3%9F%8F%E4%AE%B7%E3%9B%A9%EC%B0%85%E6%9A%8E%E8%85%A2%EC%8A%BD%EA%87%B1&%E5%99%A9%E9%A6%85%EB%A2%A4=%E0%B7%B3%E8%92%BD%E5%99%A9%E9%A6%85%EB%A2%A4%EA%B0%BA%E7%AF%A7%E6%BD%A9%E9%8D%8A%EB%A4%9C&%EC%A1%86-%E8%8A%8E%E8%8C%9A%EC%B3%8A%EA%8B%94=%E8%AE%97%EC%A1%86-%E8%8A%8E%E8%8C%9A%EC%B3%8A%EA%8B%94%E8%B0%BE%EB%A2%B2%E9%A6%BE&%ED%93%8E%ED%9B%96%E7%A4%B8%E5%83%80=%ED%93%8E%ED%9B%96%E7%A4%B8%E5%83%80%E8%A8%A0%E8%BC%95%EF%B4%8B%E8%80%A4%E5%B2%B3%EC%99%95",
            $this->request->getCanonRequest($params)
        );
    }

    public function testCanonRequestV2()
    {
        $this->request->setHostname('foO.BAr52.cOm');
        $this->request->setMethod('PoSt');
        $this->request->setPath('/Foo/BaR2/qux');
        $this->request->setHashOptions(
            array('date' => 'Fri, 07 Dec 2012 17:18:00 -0000')
        );

        $params = '{"\u2815\uaaa3\u37cf\u4bb7": "\u2815\uaaa3\u37cf\u4bb7\u36e9\ucc05\u668e\u8162\uc2bd\ua1f1", "\u5669\u9985\ub8a4": "\u0df3\u84bd\u5669\u9985\ub8a4\uac3a\u7be7\u6f69\u934a\ub91c", "\ud4ce\ud6d6\u7938\u50c0": "\ud4ce\ud6d6\u7938\u50c0\u8a20\u8f15\ufd0b\u8024\u5cb3\uc655", "\uc846-\u828e\u831a\uccca\ua2d4": "\u8b97\uc846-\u828e\u831a\uccca\ua2d4\u8c3e\ub8b2\u99be"}';
        $params = json_decode($params, true);
        $this->request->setParams($params);

        $this->assertEquals(
            "Fri, 07 Dec 2012 17:18:00 -0000\nPOST\nfoo.bar52.com\n/Foo/BaR2/qux\n%E2%A0%95%EA%AA%A3%E3%9F%8F%E4%AE%B7=%E2%A0%95%EA%AA%A3%E3%9F%8F%E4%AE%B7%E3%9B%A9%EC%B0%85%E6%9A%8E%E8%85%A2%EC%8A%BD%EA%87%B1&%E5%99%A9%E9%A6%85%EB%A2%A4=%E0%B7%B3%E8%92%BD%E5%99%A9%E9%A6%85%EB%A2%A4%EA%B0%BA%E7%AF%A7%E6%BD%A9%E9%8D%8A%EB%A4%9C&%EC%A1%86-%E8%8A%8E%E8%8C%9A%EC%B3%8A%EA%8B%94=%E8%AE%97%EC%A1%86-%E8%8A%8E%E8%8C%9A%EC%B3%8A%EA%8B%94%E8%B0%BE%EB%A2%B2%E9%A6%BE&%ED%93%8E%ED%9B%96%E7%A4%B8%E5%83%80=%ED%93%8E%ED%9B%96%E7%A4%B8%E5%83%80%E8%A8%A0%E8%BC%95%EF%B4%8B%E8%80%A4%E5%B2%B3%EC%99%95",
            $this->request->getCanonRequest($params)
        );
    }

    /**
     * Test that the client fetched is valid and the right type
     * @covers \DuoAuth\Request::getClient
     */
    public function testGetClient()
    {
        $client = $this->request->getClient();
        $this->assertTrue(
            $client !== null && $client instanceof MockClient
        );
    }

    /**
     * Test the getter/setter for error messages
     * @covers \DuoAuth\Request::setError
     * @covers \DuoAuth\Request::getErrors
     */
    public function testGetSetErrors()
    {
        $error = 'test message #1';
        $this->request->setError($error);

        $this->assertEquals(
            array($error),
            $this->request->getErrors()
        );
    }

    /**
     * Test the building of the hash headers for the request
     * @covers \DuoAuth\Request::buildHashHeader
     */
    public function testBuildHashHeader()
    {
        $result = $this->request->buildHashHeader();
        $hash = hash_hmac(
            'sha1',
            $this->request->getCanonRequest(),
            $this->request->getSecretKey()
        );
        $this->assertEquals($hash, $result);
    }

}
