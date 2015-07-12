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
            'elements' => [
                'css_selector' => '.product-link-button.big',
                'attributes' => [
                    'hover' => [
                        'width' => '330px',
                        'height' => '38px',
                        'font-size' => '20px'
                    ],
                    'non_hover' => [
                        'width' => '330px',
                        'height' => '38px',
                        'font-size' => '20px'
                    ],
                ]
            ]
        ],
        'chart' => [
            'elements' => [
                'css_selector' => '.chart-table .product-link .link-type-btn',
                'attributes' => [
                    'hover' => [
                        'width' => '113px',
                        'height' => '40px',
                        'font-size' => '18px'
                    ],
                    'non_hover' => [
                        'width' => '113px',
                        'height' => '40px',
                        'font-size' => '18px'
                    ],
                ]
            ]
        ]
        ,
        'feature_comparison' => [
            'elements' => [
                'css_selector' => '.link-type-btn.small',
                'attributes' => [
                    'hover' => [
                        'width' => '113px',
                        'height' => '30px',
                        'font-size' => '18px'
                    ],
                    'non_hover' => [
                        'width' => '113px',
                        'height' => '30px',
                        'font-size' => '18px'
                    ],
                ]
            ]
        ]
        ,
        'editors_review' => [
            'elements' => [
                'css_selector' => 'a.product-link-button.big',
                'attributes' => [
                    'hover' => [
                        'width' => '330px',
                        'height' => '38px',
                        'font-size' => '20px'
                    ],
                    'non_hover' => [
                        'width' => '330px',
                        'height' => '38px',
                        'font-size' => '20px'
                    ],
                ]
            ]

        ],
        'article' => [
            'elements' => [
                'css_selector' => 'a.product-link-button.big',
                'attributes' => [
                    'hover' => [
                        'width' => '330px',
                        'height' => '38px',
                        'font-size' => '20px'
                    ],
                    'non_hover' => [
                        'width' => '330px',
                        'height' => '38px',
                        'font-size' => '20px'
                    ],
                ]
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
                $elementSet = $this->elementsToTest[$componentName];

                try {
                    foreach ($elementSet as $actionStateAttributes) {
                        $selector = $actionStateAttributes['css_selector'];
                        $htmlElement = $this->webDriver->findElement(WebDriverBy::cssSelector($selector));

                        $cssValue = $cssExpectedValue = '';
                        foreach ($actionStateAttributes['attributes'] as $actionState => $attributes) {
                            foreach ($attributes as $attributeType => $attributeValue) {
                                if ($actionState === 'hover') {
                                    $this->webDriver->getMouse()->mouseMove($htmlElement->getCoordinates());
                                }
                                $cssValue = $htmlElement->getCSSValue($attributeType);
                                $cssExpectedValue = $attributeValue;
                                $this->assertEquals($cssValue, $cssExpectedValue);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    print_r(array($e->getMessage(), $selector, $url, $cssValue, $cssExpectedValue));
                }
            }


        }

        function waitForUserInput()
        {
            if (trim(fgets(fopen("php://stdin", "r"))) != chr(13)) return;
        }


    }
}