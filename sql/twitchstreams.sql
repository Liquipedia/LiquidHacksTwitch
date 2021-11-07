CREATE TABLE /*_*/twitchstreams (
	`channel` varbinary(100) NOT NULL,
	`name` varbinary(100) NOT NULL
) /*$wgDBTableOptions*/;

ALTER TABLE /*_*/twitchstreams
  ADD UNIQUE (`channel`);
