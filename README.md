Gtm
====
MediaWiki　にGoogle タグ マネージャーを追加します

[Google タグ マネージャー](https://tagmanager.google.com/?hl=ja#/home)

## Description
このMediaWiki拡張はすべてのページにGoogle タグ マネージャーのコンテナスニペットを追加します


* No Script Tgaはfooterで読み込まれます


## Download

### Composer
Composer でインストールします [composer.local.json](https://www.mediawiki.org/wiki/Composer#Using_composer-merge-plugin)
```bash
COMPOSER=composer.local.json composer require harugon/Gtm
```

## Install


LocalSettings.php に下記を追記
Google タグ マネージャー のコンテナIDを```$wgGtmId```に指定します。
```php
wfLoadExtension( 'Gtm' );
$wgGtmId = "";// GTM-XXXXX
```

## Config

| config         | default | Example    |
|----------------|---------|------------|
| $wgGtmId       | ""      | GTM-XXXXXX |

## Licence

MIT

## Author

[harugon](https://github.com/harugon)
