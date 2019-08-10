#!/usr/bin/env bash

STARTING_PATH=`pwd`
grep -E -o '<file.*>(.*?)</file>' ./pkg_quiz.xml | \
sed -E 's/<file[^>]+>|.zip<\/file>//g' | \
while read in;
do
if [ "$in" != 'plg_installer_joomplaceupdater' ]; then
    cd ./"$in";
    if [ ! -f "$STARTING_PATH/packages/$in".zip ]; then
        mkdir -p "$STARTING_PATH/packages/$in";
        rm -rf "$STARTING_PATH/packages/$in";
    fi
    zip -qtr "$STARTING_PATH/packages/$in".zip ./*;
    echo "✔ $in";
    cd "$STARTING_PATH";
fi;
done
