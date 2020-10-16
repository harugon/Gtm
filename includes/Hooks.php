<?php


namespace Gtm;

use MediaWiki\MediaWikiServices;
use MWException;
use OutputPage;
use Skin;

class Hooks
{
    /**
     * onBeforePageDisplay
     *
     * @param OutputPage $out
     * @param Skin $skin
     * @throws MWException
     */
    public static function onBeforePageDisplay( OutputPage $out, Skin $skin ) {

        $conf =  MediaWikiServices::getInstance()->getMainConfig();

        $gtm_id = $conf->get( 'GtmId' );
        $noscript = $conf->get( 'Gtm-noscript' );

        if ( $gtm_id  == "" ) {
            throw new MWException( "Please update your LocalSettings.php with the correct Gtm configurations" );
        }

        $html = <<<TXT
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{$gtm_id}');</script>
<!-- End Google Tag Manager -->
TXT;
        $out->addHeadItem("gtm", $html );



        if (! $noscript  == "" ) {
            //<body>直下はできないので上の方
            $noscript = <<<TXT
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MGRSTX"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
TXT;
            $out->prependHTML($noscript);

        }



    }

}

