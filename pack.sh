#!/usr/bin/env bash

STARTING_PATH=`pwd`
grep -E -o '<file.*>(.*?)</file>' ./pkg_quiz.xml | \
sed -E 's/<file[^>]+>|.zip<\/file>//g' | \
while read in;
do
if [ "$in" != 'plg_installer_joomplaceupdater' ]; then
    cd ./"$in";
    bzip2 -r "$STARTING_PATH/$in".zip ./*;
    echo "✔ $in ";
    cd "$STARTING_PATH";
fi;
done
