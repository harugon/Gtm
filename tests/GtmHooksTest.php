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
    }

    public function testSkinAfterBottomScripts() {
        $skin = new SkinTemplate();
        $text = "";
        Hooks::onSkinAfterBottomScripts($skin,$text);
        $this->assertRegExp('/googletagmanager/', $text);
    }
}