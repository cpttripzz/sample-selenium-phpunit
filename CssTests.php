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
        'top10antivirussoftware.com' => [
            'top_products' => ['mcafeeavreview'],
            'chart' => '',
            'feature_comparison' => ['featurecomparison', 'mcafeeavreview'],
            'editors_review' => 'mcafeeavreview',
            'article' => 'mcafeeavreview'
        ],
        'top10antivirussoftware.co.uk' => [
            'top_products' => 'mcafeeavreview',
            'chart' => '',
            'feature_comparison' => ['featurecomparison', 'mcafeeavreview'],
            'editors_review' => 'mcafeeavreview',
            'article' => 'mcafeeavreview'
        ]
    ];
    protected $elementsToTest = [
        //small
        'top_products' => [
            'elements' => [
                'css_selector' => '.link-type-btn.small',
                'attributes' => [
                    'hover' => [
                        'width' => '93px',
                        'height' => '33px',
                        'font-size' => '18px'
                    ],
                    'non_hover' => [
                        'width' => '95px',
                        'height' => '35px',
                        'font-size' => '15px'
                    ],
                ]
            ]
        ],
        'chart' => [
            'elements' => [
                'css_selector' => '.chart-table .product-link .link-type-btn',
                'attributes' => [
                    'hover' => [
                        'width' => '117px',
                        'height' => '32px',
                        'font-size' => '20px'
                    ],
                    'non_hover' => [
                        'width' => '124px',
                        'height' => '35px',
                        'font-size' => '24px'
                    ],
                ]
            ]
        ]
        /*,
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
        ]*/
        ,
        'editors_review' => [
            'elements' => [
                'css_selector' => 'a.product-link-button.big',
                'attributes' => [
                    'hover' => [
                        'width' => '334px',
                        'height' => '42px',
                        'font-size' => '24px'
                    ],
                    'non_hover' => [
                        'width' => '333px',
                        'height' => '41px',
                        'font-size' => '23px'
                    ],
                ]
            ]

        ],
        'article' => [
            'elements' => [
                'css_selector' => 'a.product-link-button.big',
                'attributes' => [
                    'hover' => [
                        'width' => '334px',
                        'height' => '42px',
                        'font-size' => '24px'
                    ],
                    'non_hover' => [
                        'width' => '333px',
                        'height' => '41px',
                        'font-size' => '23px'
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

            foreach ($components as $componentName => $componentSet) {
                if(!is_array($componentSet)){
                    $componentSet = array($componentSet);
                }
                foreach ($componentSet as $componentUrl) {
                    $url = 'http://' . 'www.' . $site . '/' . $componentUrl;
                    $this->webDriver->get($url);

                    // checking that page title contains word 'GitHub'
                    if (!empty($this->elementsToTest[$componentName])) {
                        $elementSet = $this->elementsToTest[$componentName];

                        try {
                            foreach ($elementSet as $actionStateAttributes) {
                                $selector = $actionStateAttributes['css_selector'];
                                $htmlElement = $this->webDriver->findElement(WebDriverBy::cssSelector($selector));

                                $cssValue = $cssExpectedValue = $state = '';
                                foreach ($actionStateAttributes['attributes'] as $actionState => $attributes) {
                                    $state = $actionState;
                                    foreach ($attributes as $attributeType => $attributeValue) {
                                        if ($actionState === 'hover') {
                                            $this->webDriver->getMouse()->mouseMove($htmlElement->getCoordinates());
                                        } else {
                                            $link = $this->webDriver->findElement(WebDriverBy::tagName('ul'));
                                            $this->webDriver->getMouse()->mouseMove($link->getCoordinates());

                                        }

                                        $cssValue = $htmlElement->getCSSValue($attributeType);
                                        $cssExpectedValue = $attributeValue;
                                        $this->assertEquals($cssValue, $cssExpectedValue);
                                    }
                                }
                            }

                        } catch (\Exception $e) {
                            print_r(array($e->getMessage(), $selector, $url, 'state' => $state, 'real', $cssValue, 'exp', $cssExpectedValue));
                        }
                    }
                }
            }

        }

        function waitForUserInput()
        {
            if (trim(fgets(fopen("php://stdin", "r"))) != chr(13)) return;
        }


    }
}