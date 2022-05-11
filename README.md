Gtm
====
MediaWiki　にGoogleTagManagerを追加します

[Google タグ マネージャー](https://tagmanager.google.com/?hl=ja#/home)

![MediaWiki Google Tag Manager](https://repository-images.githubusercontent.com/304636384/d496c1b9-b259-4b2b-bb3e-318484b52dcb)
## Description
このMediaWiki拡張はすべてのページにGoogle タグ マネージャーのコンテナスニペットを追加します


* No Script Tgaはfooterで読み込まれます


## Download

[Releases · harugon/Gtm](https://github.com/harugon/Gtm/releases) 
から ``Gtm-vxx.tar.gz``をダウンロードしextensionsフォルダに展開


## Install


LocalSettings.php に下記を追記
Google タグ マネージャー のコンテナIDを```$wgGtmId```に指定します。
```php
wfLoadExtension( 'Gtm' );
$wgGtmId = "";// GTM-XXXXX

$wgGtmData = [
    'wgPageName',
    'wgUserId',
];
```

## Config

| config         |                                           | Example                                  |
|----------------|-------------------------------------------|------------------------------------------|
| $wgGtmId       | コンテナID     　                              | $wgGtmId = "GTM-XXXXXX";                 |
| $wgGtmAddTag　　 | 追加タグ(gtm.jsの下に追加されるタグ)  　                 | $wgGtmAddTag = "<sctipt></script>";　　　   |
| $wgGtmData     | getJsVars() で取得できるデータをdataLayerにmapします　   | $wgGtmData = ['wgPageName','wgUserId',]; |


[Manual:Interface/JavaScript \- MediaWiki](https://www.mediawiki.org/wiki/Manual:Interface/JavaScript/ja#All_pages_(user/page-specific))

## Licence

MIT

## Author

[harugon](https://github.com/harugon)
