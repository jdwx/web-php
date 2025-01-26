#!/bin/sh

Host=localhost
Port=9000
echo "Starting PHP built-in web server at http://${Host}:${Port}"
BaseDir=$(dirname "${0}")
SrcDir="${BaseDir}/src"
/usr/bin/env php -S "${Host}:${Port}" "${SrcDir}/example.php"
