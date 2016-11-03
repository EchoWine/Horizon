#!/bin/bash

# Check params
if [[ ! -n "$1" || ! -n "$2" || ! -n "$3" ]] ; then

	echo "Params not defined";
	exit;
fi

# Folders
FOLDER="$1"
PROGRESS="$FOLDER""progress.log"

# Is another instance of update still running?
if [[ -f "$PROGRESS" ]] ; then

	echo "Another instance is already running. Exiting...";
	exit

fi

# Add lock
echo " " > "$PROGRESS"

# Download
echo "Elaborating: $2"
youtube-dl "$2" --write-thumbnail -o "$FOLDER""%(id)s/%(title)s.%(ext)s"

# Remove lock
rm "$PROGRESS"

# Call php command in order to update database with new videos
php "$3" "$4"


