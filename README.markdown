# Survey #

Requirements:

* SQLite
* PHP

Setup:

Create a data.json file, the format for this file is:

	[
	  {
	    "url": "http://www.zoo.com",
	    "title": "Super Fun Zoo",
	    "description": "Our zoo has lions."
	  },
	  ...
	]

Then setup your SQLite file by running the SQL outputted by `php db.php init` and `php db.php add_questions data.json`. To add a user run the SQL outputted by `php db.php add_user QUESTIONS_COUNT`.

Place all files except db.php and README.markdown in your web directory, in relation to these file the SQLite file should be in: ../db/db.sqlite

A user's link to the survey will be: http://www.example.com/path/to/directory/list.php?user_id=<sql_user_id>

Where sql_user_id is a id from a column of the users tables in the SQLite file.
