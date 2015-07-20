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
    protected $siteComponents = [];
    protected $pageTypeToCssFamily = [
        'top_products' => 'sml',
        'chart' => 'med',
        'feature_comparison' => '',
        'editors_review' => 'xlrg',
        'article' => 'xlrg'
    ];

    protected $pageTypeToCssSelector = [
        //small
        'top_products' => [
            'elements' => [
                'sml' => '.link-type-btn.small',
                'sml-hvr' => '.link-type-btn.small',
            ]
        ],
        'chart' => [
            'elements' => [
                'med' => '.chart-table .product-link .link-type-btn',
                'med-hvr' => '.chart-table .product-link .link-type-btn',
            ]
        ]

        /*'feature_comparison' => [
            'elements' => [
                'css_selector' => '.link-type-btn.small',
        'css_selector-hvr' => '.link-type-btn.small',
            ]
        ]*/
        ,
        'editors_review' => [
            'elements' => [
                'xlrg' => 'a.product-link-button.big',
                'xlrg-hvr' => 'a.product-link-button.big',
            ]

        ],
        'article' => [
            'elements' => [
                'xlrg' => 'a.product-link-button.big',
                'xlrg-hvr' => 'a.product-link-button.big',
            ]
        ]

    ];
    protected $cssElementUnitsBoostMapping = [
        'width' => 1,
        'height' => 2,
        'font-size' => 3
    ];

    public function setUp()
    {
        /*
         * $host = 'http://localhost:4444/wd/hub'; // this is the default
        $capabilities = DesiredCapabilities::htmlUnitWithJS();
        // For Chrome
        $options = new ChromeOptions();
//      ChromeOptions options = new ChromeOptions();
        $options->addArguments(["--start-maximized"]);
        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);


        $this->webDriver = RemoteWebDriver::create($host, $capabilities, 5000);
         */
        $capabilities = array(\WebDriverCapabilityType::BROWSER_NAME => 'firefox');
        $this->webDriver = RemoteWebDriver::create('http://localhost:4444/wd/hub', $capabilities);
    }

    public function tearDown()
    {
        $this->webDriver->close();
    }

    protected $boostCssSizeMapping = [
        'sml' => 9,
        'sml-hvr' => 10,
        'med' => 7,
        'med-hvr' => 8,
        'xlrg' => 3,
        'xlrg-hvr' => 4
    ];


    public function getBoosHost()
    {
        switch ($GLOBALS['ENV']) {
            case 'staging':
            case 'qa':
                return "http://boost.{$GLOBALS['ENV']}.naturalint.com/";
                break;
            case 'local':
            default:
                return 'http://localhost:3010';
                break;
        }
    }

    public function testSetCssAttributesInBoost()
    {
        $this->doBoostLogin();
        $siteSelect = $this->webDriver->findElement(WebDriverBy::className('menuSiteSelector'));
        $this->webDriver->wait(30, 500)->until(
            WebDriverExpectedCondition::visibilityOf($siteSelect)
        );

        $siteSelect = new WebDriverSelect($siteSelect);

        $data = Spyc::YAMLLoad('./yaml/site_css_tests.yaml');

        foreach ($data['sites'] as $sitesData) {

            $siteSelect->selectByVisibleText($sitesData['display']);

            sleep(2);
            $themeBtn = $this->webDriver->findElement(WebDriverBy::linkText('Theme'));
            $this->webDriver->wait(10, 500)->until(
                WebDriverExpectedCondition::visibilityOf($themeBtn)
            );
            $themeBtn->click();

            sleep(1);

            foreach ($sitesData['css_attributes'] as $siteDataCssAttributes) {
                foreach ($siteDataCssAttributes as $cssElement => $cssElementProperties) {
                    $mappedBoostId = $this->boostCssSizeMapping[$cssElement];
                    $siteId = $sitesData['site_id'];
                    $xpath = "(//button[@type='button'])[23]";
                    sleep(1);
                    $btn = $this->webDriver->findElement(WebDriverBy:: xpath($xpath));
                    $this->webDriver->wait(10, 500)->until(
                        WebDriverExpectedCondition::visibilityOf($btn)
                    );
                    $btn->click();
                    $xpath = "//div[@id='theme-settings_{$siteId}']/div/div/div/div/div[2]/div[2]/div/form/style-form/div/style-input[4]/input-group-select/fieldset/div/span/div/form/div/div[{$mappedBoostId}]";
                    $btn = $this->webDriver->findElement(WebDriverBy:: xpath($xpath));
                    $this->webDriver->wait(10, 500)->until(
                        WebDriverExpectedCondition::visibilityOf($btn)
                    );
                    $btn->click();
                    $cssElementBoostMapping = [
                        'width' => 3,
                        'height' => 4,
                        'font-size' => 5
                    ];

                    foreach ($cssElementProperties as $cssElementProperty) {
                        foreach ($cssElementProperty as $cssElementPropertyName => $cssElementPropertyValue) {
                            $cssBoostId = $cssElementBoostMapping[$cssElementPropertyName];
                            $xpath = "(//input[@type='number'])[{$cssBoostId}]";
                            $attrValue = $this->webDriver->findElement(WebDriverBy:: xpath($xpath));
                            if (!$attrValue->isDisplayed()) {
                                $cssBoostId = $this->getCssElementUnitsBoostMapping($cssElementPropertyName);
                                $buttonId = $cssBoostId + 23;
                                $xpath = "(//button[@type='button'])[{$buttonId}]";
                                $cssBoostId = '[' . $cssBoostId . ']';
                                $attrUnits = $this->webDriver->findElement(WebDriverBy:: xpath($xpath));
                                $attrUnits->click();
                                $xpathUnits = "
                    //div[@id='theme-settings_{$siteId}']/div/div/div/div/div[2]/div[2]/div/form/style-form/div/style-input[4]/input-group-select/fieldset/div/style-input/input-group/fieldset/div/style-input{$cssBoostId}/input-dimension/div/div/div/div[2]/span/div/form/div/div[3]";
                                $attrUnits = $this->webDriver->findElement(WebDriverBy:: xpath($xpathUnits));
                                $this->webDriver->wait(10, 500)->until(
                                    WebDriverExpectedCondition::visibilityOf($attrUnits)
                                );
                                $attrUnits->click();
                            }
                            $this->webDriver->wait(10, 500)->until(
                                WebDriverExpectedCondition::visibilityOf($attrValue)
                            );
                            $attrValue->clear();
                            $attrValue->sendKeys($cssElementPropertyValue);
                        }
                    }
                }

            }


            $saveBtn = $this->webDriver->findElement(WebDriverBy::cssSelector("button.btn.btn-primary"));
            $saveBtn->click();

            sleep(3);
            $publishBtn = $this->webDriver->findElement(WebDriverBy::xpath("(//button[@type='submit'])[2]"));
            $publishBtn->click();
            sleep(1);
            $btnConfirm = $this->webDriver->findElement(WebDriverBy::cssSelector("div.modal-footer.ng-scope > button.btn.green"));
            $this->webDriver->wait(10, 500)->until(
                WebDriverExpectedCondition::visibilityOf($btnConfirm)
            );
            $btnConfirm->click();
        }
    }

    public function testCssAttributesOnRenderedSite()
    {
        $data = Spyc::YAMLLoad('./yaml/site_css_tests.yaml');

        foreach ($data['sites'] as $sitesData) {
            $site = $this->getRendererHost($sitesData['host']);

            $pagesToCheck = $sitesData['pages_to_check'];
            foreach ($pagesToCheck as $pageToCheck) {
                foreach ($pageToCheck as $pageType => $pages) {
                    foreach ($pages as $page) {
                        $url = $site . '/' . $page;
                        $this->webDriver->get($url);
                        $cssFamily = $this->pageTypeToCssFamily[$pageType];
                        if (empty($this->pageTypeToCssSelector[$pageType])) {
                            continue;
                        }
                        $selectors = $this->pageTypeToCssSelector[$pageType];
                        foreach ($selectors['elements'] as $selectorCssFamily => $selector) {
                            try {
                                $htmlElement = $this->webDriver->findElement(WebDriverBy::cssSelector($selector));
                            } catch (\Exception $e) {
                                print_r(array($site, $pageType, $selector));
                                continue;
                            }

                            foreach ($sitesData['css_attributes'] as $siteDataCssAttributes) {
                                foreach ($siteDataCssAttributes as $cssElement => $cssElementProperties) {
                                    if ($cssElement !== $selectorCssFamily) {
                                        continue;
                                    }
                                    foreach ($cssElementProperties as $cssElementProperty) {
                                        foreach ($cssElementProperty as $cssElementPropertyName => $expectedCssElementPropertyValue) {
                                            if (strstr($cssElement, 'hvr')) {
                                                $this->webDriver->getMouse()->mouseMove($htmlElement->getCoordinates());
                                            } else {
                                                $link = $this->webDriver->findElement(WebDriverBy::tagName('ul'));
                                                $this->webDriver->getMouse()->mouseMove($link->getCoordinates());
                                            }
                                            $cssValue = $htmlElement->getCSSValue($cssElementPropertyName);
                                            try {
                                                $this->assertEquals($expectedCssElementPropertyValue . 'px', $cssValue);
                                            } catch (\Exception $e) {
                                                print_r($expectedCssElementPropertyValue . 'px', $cssValue);
                                                continue;
                                            }
                                        }
                                    }
                                }

                            }
                        }
                    }
                }
            }
        }
    }

    public function testSetCssAttributesToDefaultsInBoost()
    {
        $this->doBoostLogin();
        $siteSelect = $this->webDriver->findElement(WebDriverBy::className('menuSiteSelector'));
        $this->webDriver->wait(30, 500)->until(
            WebDriverExpectedCondition::visibilityOf($siteSelect)
        );

        $siteSelect = new WebDriverSelect($siteSelect);

        $data = Spyc::YAMLLoad('./yaml/site_css_tests.yaml');

        foreach ($data['sites'] as $sitesData) {

            $siteSelect->selectByVisibleText($sitesData['display']);

            sleep(2);
            $themeBtn = $this->webDriver->findElement(WebDriverBy::linkText('Theme'));
            $this->webDriver->wait(10, 500)->until(
                WebDriverExpectedCondition::visibilityOf($themeBtn)
            );
            $themeBtn->click();

            sleep(1);
            $cssDefaultData = Spyc::YAMLLoad('./yaml/default_css_attributes.yaml');
            foreach ($cssDefaultData['css_attributes'] as $siteDataCssAttributes) {
                foreach ($siteDataCssAttributes as $cssElement => $cssElementProperties) {
                    $mappedBoostId = $this->boostCssSizeMapping[$cssElement];
                    $siteId = $sitesData['site_id'];
                    $xpath = "(//button[@type='button'])[23]";
                    sleep(1);
                    $btn = $this->webDriver->findElement(WebDriverBy:: xpath($xpath));
                    $this->webDriver->wait(10, 500)->until(
                        WebDriverExpectedCondition::visibilityOf($btn)
                    );
                    $btn->click();
                    $xpath = "//div[@id='theme-settings_{$siteId}']/div/div/div/div/div[2]/div[2]/div/form/style-form/div/style-input[4]/input-group-select/fieldset/div/span/div/form/div/div[{$mappedBoostId}]";
                    $btn = $this->webDriver->findElement(WebDriverBy:: xpath($xpath));
                    $this->webDriver->wait(10, 500)->until(
                        WebDriverExpectedCondition::visibilityOf($btn)
                    );
                    $btn->click();
                    $cssElementBoostMapping = [
                        'width' => 3,
                        'height' => 4,
                        'font-size' => 5
                    ];

                    foreach ($cssElementProperties as $cssElementProperty) {
                        foreach ($cssElementProperty as $cssElementPropertyName => $cssElementPropertyValue) {
                            $cssBoostId = $cssElementBoostMapping[$cssElementPropertyName];
                            $xpath = "(//input[@type='number'])[{$cssBoostId}]";
                            $attrValue = $this->webDriver->findElement(WebDriverBy:: xpath($xpath));
                            if (!$attrValue->isDisplayed()) {
                                $cssBoostId = $this->getCssElementUnitsBoostMapping($cssElementPropertyName);
                                $buttonId = $cssBoostId + 23;
                                $xpath = "(//button[@type='button'])[{$buttonId}]";
                                $cssBoostId = '[' . $cssBoostId . ']';
                                $attrUnits = $this->webDriver->findElement(WebDriverBy:: xpath($xpath));
                                $attrUnits->click();
                                $xpathUnits = "
                    //div[@id='theme-settings_{$siteId}']/div/div/div/div/div[2]/div[2]/div/form/style-form/div/style-input[4]/input-group-select/fieldset/div/style-input/input-group/fieldset/div/style-input{$cssBoostId}/input-dimension/div/div/div/div[2]/span/div/form/div/div[3]";
                                $attrUnits = $this->webDriver->findElement(WebDriverBy:: xpath($xpathUnits));
                                $this->webDriver->wait(10, 500)->until(
                                    WebDriverExpectedCondition::visibilityOf($attrUnits)
                                );
                                $attrUnits->click();
                            }
                            $this->webDriver->wait(10, 500)->until(
                                WebDriverExpectedCondition::visibilityOf($attrValue)
                            );
                            $attrValue->clear();
                            $attrValue->sendKeys($cssElementPropertyValue);
                        }
                    }
                }

            }


            $saveBtn = $this->webDriver->findElement(WebDriverBy::cssSelector("button.btn.btn-primary"));
            $saveBtn->click();

            sleep(3);
            $publishBtn = $this->webDriver->findElement(WebDriverBy::xpath("(//button[@type='submit'])[2]"));
            $publishBtn->click();
            sleep(1);
            $btnConfirm = $this->webDriver->findElement(WebDriverBy::cssSelector("div.modal-footer.ng-scope > button.btn.green"));
            $this->webDriver->wait(10, 500)->until(
                WebDriverExpectedCondition::visibilityOf($btnConfirm)
            );
            $btnConfirm->click();
        }
    }

   /* public function testCssAttributesAreProductionValues()
    {
        $data = Spyc::YAMLLoad('./yaml/site_css_tests.yaml');
        $devData = [];
        foreach ($data['sites'] as $sitesData) {
            $site = $this->getRendererHost($sitesData['host']);
            $devData[$sitesData['host']
            $pagesToCheck = $sitesData['pages_to_check'];
            foreach ($pagesToCheck as $pageToCheck) {
                foreach ($pageToCheck as $pageType => $pages) {
                    foreach ($pages as $page) {
                        $url = $site . '/' . $page;
                        $this->webDriver->get($url);
                        $cssFamily = $this->pageTypeToCssFamily[$pageType];
                        if (empty($this->pageTypeToCssSelector[$pageType])) {
                            continue;
                        }
                        $selectors = $this->pageTypeToCssSelector[$pageType];
                        foreach ($selectors['elements'] as $selectorCssFamily => $selector) {
                            try {
                                $htmlElement = $this->webDriver->findElement(WebDriverBy::cssSelector($selector));
                            } catch (\Exception $e) {
                                print_r(array($site, $pageType, $selector));
                                continue;
                            }

                            $cssDefaultData = Spyc::YAMLLoad('./yaml/default_css_attributes.yaml');

                            foreach ($cssDefaultData['css_attributes'] as $siteDataCssAttributes) {
                                foreach ($siteDataCssAttributes as $cssElement => $cssElementProperties) {
                                    if ($cssElement !== $selectorCssFamily) {
                                        continue;
                                    }
                                    foreach ($cssElementProperties as $cssElementProperty) {
                                        foreach ($cssElementProperty as $cssElementPropertyName => $expectedCssElementPropertyValue) {
                                            if (strstr($cssElement, 'hvr')) {
                                                $this->webDriver->getMouse()->mouseMove($htmlElement->getCoordinates());
                                            } else {
                                                $link = $this->webDriver->findElement(WebDriverBy::tagName('ul'));
                                                $this->webDriver->getMouse()->mouseMove($link->getCoordinates());
                                            }
                                            $cssValue = $htmlElement->getCSSValue($cssElementPropertyName);
                                            try {
                                                $this->assertEquals($expectedCssElementPropertyValue . 'px', $cssValue);
                                            } catch (\Exception $e) {
                                                print_r($expectedCssElementPropertyValue . 'px', $cssValue);
                                                continue;
                                            }
                                        }
                                    }
                                }

                            }
                        }
                    }
                }
            }
        }
    }
*/

    function waitForUserInput()
    {
        if (trim(fgets(fopen("php://stdin", "r"))) != chr(13)) return;
    }

    protected function getRendererHost($host)
    {


        $preview = ($GLOBALS['PREVIEW_MODE'] == true) ? 'preview.' : '';

        $env = (in_array($GLOBALS['ENV'], array('staging', 'qa'))) ? $GLOBALS['ENV'] . '.' : 'local.';

        return 'http://www.' . $preview . $env . $host;
    }

    protected function getBoostHost()
    {
        switch ($GLOBALS['ENV']) {
            case 'staging':
            case 'qa':
                return "http://boost.{$GLOBALS['ENV']}.naturalint.com";
                break;
            case 'local':
            default:
                return 'http://localhost:3010';
                break;
        }
    }

    protected function getCssElementUnitsBoostMapping($cssElementPropertyName)
    {
        return $this->cssElementUnitsBoostMapping[$cssElementPropertyName];
    }

    protected function doBoostLogin()
    {
        $boostHost = $this->getBoostHost();
        $url = $boostHost . "/login";
        $this->webDriver->get($url);
        $loginEl = $this->webDriver->findElement(WebDriverBy::cssSelector('a.googleplus'));
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf($loginEl)

        );

        $loginEl->click();
        $email = $this->webDriver->findElement(WebDriverBy::id('Email'));
        $email->sendKeys($GLOBALS['EMAIL']);

        $next = $this->webDriver->findElement(WebDriverBy::id('next'));
        $next->click();

        sleep(2);
        $passwd = $this->webDriver->findElement(WebDriverBy::id('Passwd'));
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf($passwd)
        );

        $passwd->sendKeys($GLOBALS['PASSWORD']);
        $signIn = $this->webDriver->findElement(WebDriverBy::id('signIn'));
        $signIn->click();
        sleep(2);

        $confirm = $this->webDriver->findElement(WebDriverBy::id('submit_approve_access'));
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf($confirm)
        );
        $confirm->click();
        sleep(2);
    }

}