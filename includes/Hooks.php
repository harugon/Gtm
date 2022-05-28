<?php

namespace MediaWiki\Extension\Gtm;

use Config;
use Html;
use MediaWiki\Hook\BeforePageDisplayHook;
use MediaWiki\Hook\SkinAfterBottomScriptsHook;
use OutputPage;
use Skin;

class Hooks implements BeforePageDisplayHook, SkinAfterBottomScriptsHook {

	/** @var Config */
	public $config;

	/**
	 * @param Config $config
	 */
	public function __construct( Config $config ) {
		$this->config = $config;
	}

	/**
	 * @param $GtmData array
	 * @param $JSVars array
	 * @return array
	 */
	public function getDataLayer( array $GtmData, array $JSVars ): array {
		$dataLayer = [];
		foreach ( $GtmData as $key ) {
			 if ( isset( $JSVars[$key] ) ) {
				 $dataLayer[$key] = $JSVars[$key];
			 }
		}
		return $dataLayer;
	}

	/**
	 * @param OutputPage $out
	 * @param Skin $skin
	 * @return void
	 */
	public function onBeforePageDisplay( $out, $skin ): void {
		$containerId = $this->config->get( 'GtmId' );
		$GtmData = $this->config->get( 'GtmData' );
		$GtmAttribs = $this->config->get( 'GtmAttribs' );
		$GtmBeforeTag = $this->config->get( 'GtmBeforeTag' );
		$GtmAfterTag = $this->config->get( 'GtmAfterTag' );

		// Google Tag Manager Container ID
		if ( $containerId !== "" ) {

			// DataLayer
			$DataLayerTag = "";
			if ( isset( $GtmData[0] ) ) {

				$DataLayer = $this->getDataLayer( $GtmData, $out->getJSVars() );
				$DataLayerPush = '';

				if ( $DataLayer ) {
					$DataLayerPush = json_encode( $DataLayer );
				}
				$DataLayerTag = PHP_EOL . 'window.dataLayer = window.dataLayer || []; dataLayer = [' . $DataLayerPush . '];';
			}

			// BeforeTag
			if ( $GtmBeforeTag !== "" ) {
				$out->addHeadItem( "gtm-before", $GtmBeforeTag );
			}

			// Google Tag Manager Tag
			$html = Html::element(
				'script',
				$GtmAttribs, <<<TXT
{$DataLayerTag}
(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{$containerId}');
TXT );
			$out->addHeadItem( "gtm-script", $html );

			// AfterTag
			if ( $GtmAfterTag !== "" ) {
				$out->addHeadItem( "gtm-after", $GtmAfterTag );
			}

		}
	}

	/**
	 * @param Skin $skin
	 * @param &$text
	 * @return bool
	 */
	public function onSkinAfterBottomScripts( $skin, &$text ): bool {
		$containerId = $this->config->get( 'GtmId' );
		$GtmData = $this->config->get( 'GtmData' );
		$GtmNoScript = $this->config->get( 'GtmNoScript' );

		if ( !$GtmNoScript ) {
			return true;
		}

		if ( $containerId === "" ) {
			return true;
		}

		// DataLayer
		$DataLayerPush = '';
		if ( isset( $GtmData[0] ) ) {
			$DataLayer = $this->getDataLayer( $GtmData, $skin->getOutput()->getJSVars() );
			if ( $DataLayer ) {
				$DataLayerPush = '&' . http_build_query( $DataLayer );
			}
		}

		$noscript = <<<TXT
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={$containerId}{$DataLayerPush}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
TXT;
		$text .= $noscript;

		return true;
	}
}
