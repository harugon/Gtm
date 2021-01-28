<?php

namespace Gtm;

use MediaWiki\MediaWikiServices;
use MWException;
use OutputPage;
use Skin;

class Hooks {

	/**
	 * onBeforePageDisplay
	 *
	 * @param OutputPage $out
	 * @param Skin $skin
	 * @throws MWException
	 */
	public static function onBeforePageDisplay( OutputPage $out, Skin $skin ) {
		$conf = MediaWikiServices::getInstance()->getMainConfig();

		/** @var string $container_id Container ID */
		$container_id = $conf->get( 'GtmId' );
		/** @var string $add_script script */
		$script = $conf->get( 'GtmScript' );
		$html = "";

		if ($script === "" ) {
            $html .= <<<TXT
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{$container_id}');</script>
<!-- End Google Tag Manager -->
TXT;
		}else{
            $html .= $script;
        }

		$out->addHeadItem( "gtm", $html );
	}

	/**
	 * onSkinAfterBottomScripts
	 *
	 * @param Skin $skin
	 * @param &$text
	 * @return bool
	 */
	public static function onSkinAfterBottomScripts( Skin $skin, &$text ) {
		$conf = MediaWikiServices::getInstance()->getMainConfig();

        $container_id = $conf->get( 'GtmId' );
        $noscript = $conf->get( 'GtmNoScript' );

		if ($noscript === "" ) {
			$noscript = <<<TXT
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={$container_id}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
TXT;
			$text .= $noscript;
		} else {
			$text .= "<noscript>".$noscript."<noscript>";
		}
		return true;
	}
}
