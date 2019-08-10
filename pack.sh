#!/usr/bin/env bash

PWD=`pwd`
ls -la
ls -la ./module
ls -la ./plugin
grep -E -o '<file.*>(.*?)</file>' ./pkg_quiz.xml | \
sed -E 's/<file[^>]+>|.zip<\/file>//g' | \
while read in;
do
if [ "$in" != 'plg_installer_joomplaceupdater' ]; then
    echo "---------------";
    echo "$PWD";
    pwd;
    ls -la;
    ls -la "$in";
    cd ./"$in";
#    zip -r $PWD/"$in".zip ./*;
    echo "Packed $in";
    pwd;
    echo "---------------";
    cd "$PWD";
fi;
done
