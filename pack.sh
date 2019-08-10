#!/usr/bin/env bash

STARTING_PATH=`pwd`
ls -la
ls -la ./module
ls -la ./plugin
grep -E -o '<file.*>(.*?)</file>' ./pkg_quiz.xml | \
sed -E 's/<file[^>]+>|.zip<\/file>//g' | \
while read in;
do
if [ "$in" != 'plg_installer_joomplaceupdater' ]; then
    echo "---------------";
    echo "Packing $in";
    echo "---------------";
    cd ./"$in";
    zip -r $STARTING_PATH/"$in".zip ./*;
    echo "Packed $in ✔";
    cd "$STARTING_PATH";
fi;
done
