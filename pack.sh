#!/usr/bin/env bash

STARTING_PATH=`pwd`
grep -E -o '<file.*>(.*?)</file>' ./${1} | \
sed -E 's/<file[^>]+>|.zip<\/file>//g' | \
while read in;
do
if [ "$in" != 'plg_installer_joomplaceupdater' ]; then
    cd ./"$in";
    mkdir -p "$STARTING_PATH/packages/$in";
    rm -rf "$STARTING_PATH/packages/$in";
    zip -qr "$STARTING_PATH/packages/$in".zip ./*;
    echo "✔ $in";
    cd "$STARTING_PATH";
fi;
done
