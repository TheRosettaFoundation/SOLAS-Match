##!/bin/bash -e
# -e means exit if any command fails
DBHOST=localhost
DBUSER=usolas
DBPASS=rTMnEJwFJZMmJEhU # do this in a more secure fashion
DBNAME=solasmatch
GITREPO=/home/eoin/repos/solasmatch/app/db
cd $GITREPO
mysqldump -h $DBHOST -u $DBUSER -p$DBPASS -d $DBNAME > $GITREPO/schema.sql # the -d flag means "no data"
echo "Dumped database schema to $GITREPO/schema.sql"
git add schema.sql
git commit -m "$DBNAME schema version update" # $(`date`)
echo "Committed schema update to repo."
#git push # assuming you have a remote to push to

# Make this call before each commit for real grok
# http://stackoverflow.com/a/5518976/248220

