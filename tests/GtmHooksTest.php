<?php


namespace Gtm\Tests;


use Gtm\Hooks;
use Gtm;
use OutputPage;
use RequestContext;
use SkinTemplate;

class GtmHooksTest extends \MediaWikiTestCase
{

    public function testBeforePageDisplay() {
        $skin = new SkinTemplate();
        $out = new OutputPage(new RequestContext());

        Hooks::onBeforePageDisplay($out,$skin);
        $header = $out->hasHeadItem('gtm');
        $this->assertTrue($header);

        $out = new OutputPage(new RequestContext());
        $this->setMwGlobals( [
            'wgGtmId' => "GTM-5555555",
        ] );
        Hooks::onBeforePageDisplay($out,$skin);
        $header_item = $out->getHeadItemsArray()['gtm'];
        $this->assertRegExp('/GTM-5555555/',$header_item);

        $out = new OutputPage(new RequestContext());
        $this->setMwGlobals( [
            'wgGtmScript' => "<script>dataLayer.push({'wiki':'gtm555'})</script>",
        ] );
        Hooks::onBeforePageDisplay($out,$skin);
        $header_item = $out->getHeadItemsArray()['gtm'];
        $this->assertRegExp("/gtm555/",$header_item);

    }

    public function testSkinAfterBottomScripts() {
        $skin = new SkinTemplate();

        $text = "";
        Hooks::onSkinAfterBottomScripts($skin,$text);
        $this->assertRegExp('/googletagmanager/', $text);

        $text = "";

        $this->setMwGlobals( [
            'wgGtmNoScript' => "かしこまっ!",
        ] );

        Hooks::onSkinAfterBottomScripts($skin,$text);
        $this->assertEquals('<noscript>かしこまっ!<noscript>', $text);
    }
}