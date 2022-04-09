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
				 // @todo userID null
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
		$html = "";

		$containerId = $this->config->get( 'GtmId' );
		$GtmData = $this->config->get( 'GtmData' );
		$GtmAddTag = $this->config->get( 'GtmAddTag' );

		// Google Tag Manager Container ID
		if ( $containerId !== "" ) {

			// DataLayer
			if ( isset( $GtmData[0] ) ) {

				$DataLayer = $this->getDataLayer( $GtmData, $out->getJSVars() );
				$DataLayerPush = '';

				if ( $DataLayer ) {
					$DataLayerPush = json_encode( $DataLayer );
				}

				$html .= Html::element(
					'script',
					[],
					'dataLayer =[' . $DataLayerPush . '];'
				) . PHP_EOL;
			}

			// Google Tag Manager Tag
			$html .= <<<TXT
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{$containerId}');</script>
<!-- End Google Tag Manager -->
TXT;

			// Custom Tag
			$html .= $GtmAddTag;

			// Add
			$out->addHeadItem( "gtm", $html );
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



		if ( $containerId !== "" ) {

            // DataLayer
            $DataLayerPush = '';
            if ( isset( $GtmData[0] ) ) {

                $DataLayer = $this->getDataLayer( $GtmData, $skin->getOutput()->getJSVars() );


                if ( $DataLayer ) {
                    $DataLayerPush =  '&'.http_build_query($DataLayer);
                }
            }

			$noscript = <<<TXT
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={$containerId}{$DataLayerPush}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
TXT;
			$text .= $noscript;
		}
		return true;
	}
}
