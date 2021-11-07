<?php

namespace Liquipedia\Extension\LiquidHacksTwitch\SpecialPage;

use HTMLForm;
use SpecialPage;
use Status;

class SpecialTwitchStreams extends SpecialPage {

	/**
	 *
	 * @var Output
	 */
	private $output;

	/**
	 *
	 */
	public function __construct() {
		parent::__construct( 'TwitchStreams', 'liquidhackstwitch' );
	}

	/**
	 * @param string $param
	 */
	public function execute( $param ) {
		$user = $this->getUser();
		if ( !$this->userCanExecute( $user ) ) {
			$this->displayRestrictionError();
			return;
		}
		$this->setHeaders();
		$this->output = $this->getOutput();

		// Actual page content
		if ( $param === 'add' ) {
			$this->addStream();
		} elseif ( $param === 'delete' ) {
			$this->deleteStream();
		} else {
			$this->seeStreams();
		}
	}

	private function addStream() {
		$this->output->addWikiTextAsContent( $this->msg( 'liquidhackstwitch-see-streams' )->text() );
		$heading = $this->msg( 'liquidhackstwitch-heading-add-stream' )->text();
		$this->output->addWikiTextAsContent( '==' . $heading . '==' );
		$formDescriptor = [
			'Channel' => [
				'type' => 'text',
				'label-message' => 'liquidhackstwitch-channel',
				'maxlength' => 100,
				'required' => true,
			],
			'Name' => [
				'type' => 'text',
				'label-message' => 'liquidhackstwitch-name',
				'maxlength' => 100,
				'required' => true,
			],
		];
		$htmlForm = HTMLForm::factory( 'ooui', $formDescriptor, $this->getContext() );
		$htmlForm
			->setSubmitTextMsg( 'liquidhackstwitch-button-add-stream' )
			->setFormIdentifier( 'add-stream-form' )
			->setSubmitCallback( [ $this, 'addStreamCB' ] )
			->show();
	}

	/**
	 *
	 * @param array $formData
	 * @return Status
	 */
	public function addStreamCB( $formData ) {
		$status = new Status;
		$channel = $formData[ 'Channel' ];
		if ( empty( trim( $channel ) ) ) {
			$status->error( 'liquidhackstwitch-empty-channel' );
			return $status;
		}
		$name = $formData[ 'Name' ];
		if ( empty( trim( $name ) ) ) {
			$status->error( 'liquidhackstwitch-empty-name' );
			return $status;
		}
		$data = [
			'channel' => $channel,
			'name' => $name,
		];

		try {
			$dbw = wfGetDB( DB_PRIMARY );
			$dbw->insert( 'twitchstreams', $data );
		} catch( \Exception $e ) {
			$status->error( 'liquidhackstwitch-error-duplicate' );
		}

		return $status;
	}

	private function seeStreams() {
		$this->output->addWikiTextAsContent( $this->msg( 'liquidhackstwitch-add-stream' )->text() );
		$this->output->addWikiTextAsContent( $this->msg( 'liquidhackstwitch-delete-stream' )->text() );
		$heading = $this->msg( 'liquidhackstwitch-heading-add-stream' )->text();

		$streams = '';
		$dbr = wfGetDB( DB_REPLICA );
		$res = $dbr->select( 'twitchstreams', '*' );
		foreach ( $res as $row ) {
			$streams .= '<h3>' . htmlspecialchars( $row->name ) . '</h3>';
			$streams .= '<iframe class="stream-border" src="https://player.twitch.tv/?channel=' . htmlspecialchars( $row->channel ) . '&parent=localhost" frameborder="0" allowfullscreen="true" scrolling="no" height="378" width="620"></iframe>';
		}
		$this->output->addHTML( $streams );
	}

	private function deleteStream() {
		$this->output->addWikiTextAsContent( $this->msg( 'liquidhackstwitch-see-streams' )->text() );
		$heading = $this->msg( 'liquidhackstwitch-heading-delete-stream' )->text();
		$this->output->addWikiTextAsContent( '==' . $heading . '==' );
		$formDescriptor = [
			'Channel' => [
				'type' => 'text',
				'label-message' => 'liquidhackstwitch-channel',
				'maxlength' => 100,
				'required' => true,
			],
		];
		$htmlForm = HTMLForm::factory( 'ooui', $formDescriptor, $this->getContext() );
		$htmlForm
			->setSubmitTextMsg( 'liquidhackstwitch-button-delete-stream' )
			->setFormIdentifier( 'delete-stream-form' )
			->setSubmitCallback( [ $this, 'deleteStreamCB' ] )
			->show();
	}

	/**
	 *
	 * @param array $formData
	 * @return Status
	 */
	public function deleteStreamCB( $formData ) {
		$status = new Status;
		$channel = $formData[ 'Channel' ];
		if ( empty( trim( $channel ) ) ) {
			$status->error( 'liquidhackstwitch-empty-channel' );
			return $status;
		}
		$data = [
			'channel' => $channel,
		];

		$dbw = wfGetDB( DB_PRIMARY );
		$dbw->delete( 'twitchstreams', $data );

		return $status;
	}

}
