<?php

declare(strict_types=1);

namespace MediaWiki\Extension\Gtm;

use Config;
use Html;
use MediaWiki\Hook\BeforePageDisplayHook;
use MediaWiki\Hook\SkinAfterBottomScriptsHook;
use OutputPage;
use Skin;

class Hooks implements BeforePageDisplayHook, SkinAfterBottomScriptsHook
{

	/** @var Config */
	private $config;

	public function __construct(Config $config)
	{
		$this->config = $config;
	}

	/**
	 * 指定キーのみを JSVars から抽出して dataLayer に渡すペイロードを構築
	 * @param string[] $gtmDataKeys
	 * @param array $jsVars
	 * @return array
	 */
	private function buildDataLayerPayload(array $gtmDataKeys, array $jsVars): array
	{
		$payload = [];
		foreach ($gtmDataKeys as $key) {
			if ($key !== '' && array_key_exists($key, $jsVars)) {
				$payload[$key] = $jsVars[$key];
			}
		}
		return $payload;
	}

	/**
	 * head に GTM を挿入
	 * @param OutputPage $out
	 * @param Skin $skin
	 * @return void
	 */
	public function onBeforePageDisplay($out, $skin): void
	{
		$containerId = (string)$this->config->get('GtmId');
		// コンテナ未設定 エラー出さずに何もしない
		if ($containerId === '') {
			return;
		}

		$gtmDataKeys         = (array)$this->config->get('GtmData');
		$gtmAttribs          = (array)($this->config->get('GtmAttribs') ?? []);
		$gtmBeforeTag        = (string)$this->config->get('GtmBeforeTag');
		$gtmAfterTag         = (string)$this->config->get('GtmAfterTag');
		$gatewayPath         = (string)$this->config->get('GtmTagGatewayPath');
		$gateway     = (bool)$this->config->get('GtmTagGateway');

		// dataLayer ペイロード構築
		$payload = [];
		if (isset($gtmDataKeys[0])) {
			$payload = $this->buildDataLayerPayload($gtmDataKeys, $out->getJSVars());
		}

		// スクリプト属性に CSP nonce を付与 
		$csp = $out->getCSP();
		if ($csp) {
			$nonce = $csp->getNonce();
			if ($nonce) {
				$gtmAttribs['nonce'] = $nonce;
			}
		}


		// ゲートウェイを使う場合だけ差し替え
		$gtmScriptSrc = 'https://www.googletagmanager.com/gtm.js';
		if ($gateway && $gatewayPath !== '') {
			// Google タグ ゲートウェイ用に予約されている Web サイト上のパス。
			///metrics、/securemetric、/analytics などの単語またはその他の単語を選択します。
			$gtmScriptSrc = $gatewayPath;
		}

		// 事前タグ
		if ($gtmBeforeTag !== '') {
			$out->addHeadItem('gtm-before', $gtmBeforeTag);
		}

		// GTM 
		$inlined = <<<JS
window.dataLayer = window.dataLayer || [];
%s
(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='{$gtmScriptSrc}?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{$containerId}');
JS;

		$pushLine = '';
		if (!empty($payload)) {
			$pushLine = 'window.dataLayer.push(' . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ');';
		}

		$html = Html::element(
			'script',
			$gtmAttribs,
			sprintf($inlined, $pushLine)
		);
		$out->addHeadItem('gtm-script', $html);

		// 事後タグ
		if ($gtmAfterTag !== '') {
			$out->addHeadItem('gtm-after', $gtmAfterTag);
		}
	}

	/**
	 * body 末尾に noscript　を挿入
	 * @param Skin $skin
	 * @param string &$text
	 * @return bool
	 */
	public function onSkinAfterBottomScripts($skin, &$text): bool
	{
		$containerId    = (string)$this->config->get('GtmId');
		$noScript = (bool)$this->config->get('GtmNoScript');
		$gatewayEnabled = (bool)$this->config->get('GtmTagGatewayEnabled');

		// コンテナ未設定 or noscript 無効 追加しない
		if ($containerId === '' || !$noScript) {
			return true;
		}

		// ゲートウェイ有効時は 3rd-party 呼び出しを避けるため noscript  追加しない
		if ($gatewayEnabled) {
			return true;
		}

		// ns.html 追加
		$noscript = <<<HTML
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={$containerId}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
HTML;
		$text .= $noscript;

		return true;
	}
}
