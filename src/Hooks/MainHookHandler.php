<?php

namespace Liquipedia\Extension\LiquidHacksTwitch\Hooks;

use MediaWiki\Hook\ParserFirstCallInitHook;
use MediaWiki\Hook\BeforePageDisplayHook;
use OutputPage;
use Parser;
use Skin;

class MainHookHandler implements
	BeforePageDisplayHook,
	ParserFirstCallInitHook
{

	/**
	 * @param OutputPage $out
	 * @param Skin $skin
	 * @return bool
	 */
	public function onBeforePageDisplay( $out, $skin ): void {
		$out->addModuleStyles( 'ext.LiquidHacksTwitch.styles' );
	}

	/**
	 * @param Parser $parser
	 */
	public function onParserFirstCallInit( $parser ) {
		$parser->setFunctionHook( 'stream', [ MainHookHandler::class, 'stream' ] );
	}

	/**
	 * @param Parser $parser
	 * @param string $channel
	 */
	public static function stream( $parser, $channel = '' ) {
		$stream = '';
		$dbr = wfGetDB( DB_REPLICA );
		$res = $dbr->select( 'twitchstreams', '*', [ 'channel' => $channel ] );

		if ( $res->numRows() > 0 ) {
			$row = $res->fetchObject();
			$stream .= '<h3>' . htmlspecialchars( $row->name ) . '</h3>';
			$stream .= '<iframe class="stream-border" src="https://player.twitch.tv/?channel=' . htmlspecialchars( $row->channel ) . '&parent=localhost" frameborder="0" allowfullscreen="true" scrolling="no" height="378" width="620"></iframe>';
		}

		return [ $stream, 'noparse' => true, 'isHTML' => true ];
	}

}
