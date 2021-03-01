CREATE TABLE IF NOT EXISTS fe_users (
	uid int AUTO_INCREMENT NOT NULL UNIQUE,
	email varchar(100) NOT NULL UNIQUE,
	firstName varchar(40),
	lastName varchar(40),
	username varchar(100) NOT NULL UNIQUE,
	password_hash varchar(128) NOT NULL,
	permLevel int NOT NULL DEFAULT 0,
	isBanned BOOLEAN NOT NULL DEFAULT false,
	isVerified BOOLEAN NOT NULL DEFAULT false,

	KEY (email),
	KEY (username),
	PRIMARY KEY (uid)
);

CREATE TABLE IF NOT EXISTS ses_ids (
	sesId varchar(128) NOT NULL,
	accountId int NOT NULL,

	time_created int NOT NULL,
	time_expires int NOT NULL,

	PRIMARY KEY (sesId),
	FOREIGN KEY (accountId) REFERENCES fe_users(uid)
);
