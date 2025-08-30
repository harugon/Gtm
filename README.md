# Gtm (MediaWiki Extension)

MediaWiki に **Google Tag Manager (GTM)** のコンテナスニペットを追加する拡張機能です。

[Google タグ マネージャー](https://tagmanager.google.com/?hl=ja#/home)

![MediaWiki Google Tag Manager](https://repository-images.githubusercontent.com/304636384/d496c1b9-b259-4b2b-bb3e-318484b52dcb)



## 概要

この MediaWiki 拡張は、すべてのページに **Google タグ マネージャーのコンテナスニペット**を自動挿入します。

- `<script>` タグは `<head>` 内に追加されます  
- `<noscript>` タグは `<body>` の末尾に追加されます（※無効化可能）  
- 1st-party Tag Gateway 経由の配信にも対応  



## ダウンロード

[Releases · harugon/Gtm](https://github.com/harugon/Gtm/releases)  
から `Gtm-vX.X.X.tar.gz` をダウンロードし、`extensions/` フォルダに展開してください。



## インストール

`LocalSettings.php` に以下を追記してください。

```php
wfLoadExtension( 'Gtm' );

// GTM コンテナ ID
$wgGtmId = "GTM-XXXXXXX";

// dataLayer に追加する MediaWiki 変数
$wgGtmData = [
    'wgPageName',
    'wgUserId',
];
````



## 設定項目

| Config名                      | 説明                                             | 例                                                    |
| ---------------------------- | ---------------------------------------------- | ---------------------------------------------------- |
| **\$wgGtmId**                | GTM コンテナ ID                                    | `$wgGtmId = "GTM-XXXXXX";`                           |
| **\$wgGtmBeforeTag**         | gtm.js の **上**に追加する任意タグ                        | `$wgGtmBeforeTag = "<script>/* custom */</script>";` |
| **\$wgGtmAfterTag**          | gtm.js の **下**に追加する任意タグ                        | `$wgGtmAfterTag = "<script>/* custom */</script>";`  |
| **\$wgGtmData**              | `OutputPage::getJSVars()` から dataLayer に追加するキー | `$wgGtmData = ['wgPageName','wgUserId'];`            |
| **\$wgGtmNoScript**          | `<noscript>` タグを追加するかどうか                       | `$wgGtmNoScript = false;`                            |
| **\$wgGtmAttribs**           | `<script>` タグの属性（連想配列で指定）                      | `$wgGtmAttribs = ["data-cookieconsent"=>"ignore"];`  |
| **\$wgGtmTagGatewayPath**    | Google タグゲートウェイのパス（例 `/metrics`）               | `$wgGtmTagGatewayPath = "/metrics";`                 |
| **\$wgGtmTagGatewayEnabled** | タグゲートウェイを有効化するかどうか（true で有効）                   | `$wgGtmTagGatewayEnabled = true;`                    |



## 関連資料

* [Manual\:Interface/JavaScript - MediaWiki](https://www.mediawiki.org/wiki/Manual:Interface/JavaScript/ja#All_pages_%28user/page-specific%29)
* [Google Tag Manager 公式ヘルプ](https://support.google.com/tagmanager/)



## ライセンス

MIT



## Author

[harugon](https://github.com/harugon)

