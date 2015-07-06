-- Dropping and creating a table (note PRIMARY KEY)
DROP TABLE appuser;
DROP TABLE tasktable;
CREATE TABLE appuser (
	id SERIAL,
	usernames VARCHAR(20) PRIMARY KEY NOT NULL,
	userpasswords VARCHAR(20) NOT NULL,
	birthdays DATE,
	gender VARCHAR(10),
	age INTEGER
);
CREATE TABLE tasktable (
	usernames VARCHAR(20) NOT NULL,
	taskname VARCHAR(50) NOT NULL,
	totalWorkUnits integer NOT NULL,
	progressedUnits integer,
	dateCreated timestamp,
	notes VARCHAR(9999),
	minutesProgressed integer,
	PRIMARY KEY (usernames, taskname)
);