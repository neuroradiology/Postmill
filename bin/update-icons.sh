#!/bin/bash

# Download icons, create sprite sheet, save to templates/_includes/icons.svg.
# The sprite sheet should be committed to the git repository.

set -e

PROJECT_ROOT=$(dirname $(dirname $(realpath $0)))
PATH="$PROJECT_ROOT/node_modules/.bin:$PATH"
TEMP=$(mktemp -d)
OUT="$PROJECT_ROOT/templates/_includes/icons.svg"

trap 'rm -rf "$TEMP"' EXIT

fontello-cli install --config "$PROJECT_ROOT"/fontello.json --font "$TEMP" --css "$TEMP"
font-blast "$TEMP"/postmill.svg "$TEMP"
cp "$PROJECT_ROOT"/assets/icons/*.svg "$TEMP"/svg

# no webpack svg sprite loaders, they suck and don't work
echo '<svg xmlns="http://www.w3.org/2000/svg"
           xmlns:xlink="http://www.w3.org/1999/xlink"
           display="none"
           width="0"
           height="0"><defs>' > "$TEMP"/icons.svg

perl -pe'$ARGV=~s!.*/|\.svg$!!g;
         s/^<svg ?/<symbol id="$ARGV" /;
         s/ xmlns=".*?" ?/ /g;
         s!</svg>!</symbol>\n!' "$TEMP"/svg/*.svg >> "$TEMP"/icons.svg

echo '</defs></svg>' >> "$TEMP"/icons.svg

mv "$TEMP"/icons.svg "$OUT"
