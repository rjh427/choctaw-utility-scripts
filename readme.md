choctaw/generateSnakeoilSslCerts.sh  
A utility script to generate and install fake (snakeoil) SSL certificates in a local dev environment, for testing and running sites that default to HTTPS

choctaw/updateChoctaw8Files.sh  
A utility script to syncronize the local uploaded /files/ directory with the Acquia-hosted remote /files/ directory, for a Drupal 8 Website. 

choctaw/updateChoctawFiles.sh  
A utility script to syncronize the local uploaded /files/ directory with the Acquia-hosted remote /files/ directory, for a Drupal 7 Website. 

choctaw/updateChoctaw8Db.sh  
A utility to find the name of, download, extract, and import the current daily SQL backup file for an Acquia-hosted Drupal 8 Website. There are multiple prompts for user-input, which may be used to ABEND the script at any of several junctures (without having to Ctrl-C out of it).

choctaw/updateChoctawDb.sh  
A utility to find the name of, download, extract, and import the current daily SQL backup file for an Acquia-hosted Drupal 7 Website. 

choctaw/migrateTweaks/fixSpaces.php  
To correct a problem with files and directories that were badly named, the custom migration module renamed files to remove spaces and move files out of directories that were redundant or badly-named, such as a recursive instance of a directory that used only a left square bracket for the name.
This script queried the database by table name, to search for inline references to these badly-named files and updated them according to the new & improved naming convention.

choctaw/migrateTweaks/migrateTableCheck.php  
To satisfy a point of curiosity, I whipped up this script to check on migrate_* tables created by the custom migrate module, to see how many of them actually contained content and where they did, how much content.

cycles/importer.php  
This is very-much a work in progress and still a bit messy, it will change as I finish tweaking and improving it - at this writing it is ~75% complete.   
This script uses the [Simple HTML DOM Parser](http://simplehtmldom.sourceforge.net/manual.htm "Simple HTML DOM parser"), to parse through saved HTML pages from a now-defunct blogging platform, via a prepared array of locally-accessible URLs, to turn that content into a well-formed XML document that can be imported directly into WordPress.
It does use a few global variables and while I do believe that global vars are bad, it's also a one-off script that parses known content in a controlled local sandbox.

choctaw/queries  
Some misc. queries. The second one corrects an issue with external URLs being treated like internal URLs under Drupal 8, after they were migrated from Drupal 7.

