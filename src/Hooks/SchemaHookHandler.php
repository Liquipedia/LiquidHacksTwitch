<?php

namespace Liquipedia\Extension\LiquidHacksTwitch\Hooks;

use DatabaseUpdater;
use MediaWiki\Installer\Hook\LoadExtensionSchemaUpdatesHook;
use MediaWiki\MediaWikiServices;

class SchemaHookHandler implements
	LoadExtensionSchemaUpdatesHook
{

	/**
	 * @param DatabaseUpdater $updater
	 */
	public function onLoadExtensionSchemaUpdates( $updater ) {
		$updater->output( "Add table for twitch streams\n" );
		$updater->addExtensionTable( 'twitchstreams', __DIR__ . '/../../sql/twitchstreams.sql' );
	}

}
