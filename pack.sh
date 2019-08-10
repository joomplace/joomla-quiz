#!/usr/bin/env bash

PWD=`pwd`
grep -E -o '<file.*>(.*?)</file>' ./pkg_quiz.xml | \
sed -E 's/<file[^>]+>|.zip<\/file>//g' | \
while read in;
do
if [ "$in" != 'plg_installer_joomplaceupdater' ]; then
    cd ./"$in";
    zip -r $PWD/"$in".zip ./*;
    cd $PWD;
fi;
done
