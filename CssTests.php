<?php

/**
 * Created by PhpStorm.
 * User: zach
 * Date: 7/12/15
 * Time: 9:50 AM
 */
class CssTests extends PHPUnit_Framework_TestCase
{
    /**
     * @var \RemoteWebDriver
     */
    protected $webDriver;

    protected $environments = ['dev', 'qa'];
    protected $siteComponents = [
        'top10antivirussoftware.co.uk' => [
            'top_products' => 'bullguardukreview',
            'chart' => '',
            'feature_comparison' => 'featurecomparison',
            'editors_review' => '10-ways-to-improve-security-on-your-computer-article',
            'article' => 'bullguardukreview'
        ]
    ];
    protected $elementsToTest = [
        'top_products' => [
            'css_selector' => '.product-link-button.big',
            'attributes' => [
                'width' => '330px',
                'height' => '38px',
                'font-size' => '20px'
            ]
        ],
        'chart' => [
            'css_selector' => '.chart-table .product-link .link-type-btn',
            'attributes' => [
                'width' => '113px',
                'height' => '40px',
                'font-size' => '18px'
            ]
        ],
        'feature_comparison' => [
            'css_selector' => '.link-type-btn.small',
            'attributes' => [
                'width' => '113px',
                'height' => '30px',
                'font-size' => '18px'
            ]
        ],
        'editors_review' => [
            'css_selector' => 'a.product-link-button.big',
            'attributes' => [
                'width' => '330px',
                'height' => '38px',
                'font-size' => '20px'
            ]
        ],
        'article' => [
            'css_selector' => 'a.product-link-button.big',
            'attributes' => [
                'width' => '330px',
                'height' => '38px',
                'font-size' => '20px'
            ]
        ]
    ];

    public function setUp()
    {
        $capabilities = array(\WebDriverCapabilityType::BROWSER_NAME => 'firefox');
        $this->webDriver = RemoteWebDriver::create('http://localhost:4444/wd/hub', $capabilities);
    }

    public function tearDown()
    {
        $this->webDriver->close();
    }


    public function testCssAttributes()
    {


        foreach ($this->siteComponents as $site => $components) {

            foreach ($components as $componentName => $componentUrl) {
                $url = 'http://' . 'www.' . $site . '/' . $componentUrl;
                $this->webDriver->get($url);

                // checking that page title contains word 'GitHub'
                $element = $this->elementsToTest[$componentName];

                try {
                    foreach(['',':hover'] as $hoverState) {
                        $selector = $element['css_selector'];
                        $htmlElement = $this->webDriver->findElement(WebDriverBy::cssSelector($selector));
                        if($hoverState){

                            $this->webDriver->getMouse()->mouseMove( $htmlElement->getCoordinates() );
                        }
                        $cssValue = $cssExpectedValue = '';
                        foreach ($element['attributes'] as $attributeType => $attributeValue) {

                            $cssValue = $htmlElement->getCSSValue($attributeType);
                            $cssExpectedValue = $attributeValue;
                            $this->assertEquals($cssValue, $cssExpectedValue);
                        }
                    }
                } catch (\Exception $e){
                    print_r(array($e->getMessage(),$selector, $url,$cssValue,$cssExpectedValue));
                }
            }


        }

        function waitForUserInput()
        {
            if (trim(fgets(fopen("php://stdin", "r"))) != chr(13)) return;
        }


    }
}