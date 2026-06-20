#!/bin/bash
#
# add_license.sh
#
# Dodaje blok licencyjny na początku każdego pliku .php w katalogu src/
# (zaraz po <?php, przed namespace), jeśli plik jeszcze go nie ma.
#
# Użycie:
#   chmod +x add_license.sh
#   ./add_license.sh

set -e

TARGET_DIR="${1:-src}"

if [ ! -d "$TARGET_DIR" ]; then
    echo "Katalog '$TARGET_DIR' nie istnieje."
    exit 1
fi

LICENSE_BLOCK="/*
 * This file is part of the EPI project.
 */
"

COUNT_UPDATED=0
COUNT_SKIPPED=0

while IFS= read -r -d '' file; do
    if grep -q "This file is part of the EPI project" "$file"; then
        COUNT_SKIPPED=$((COUNT_SKIPPED + 1))
        continue
    fi

    if ! head -n 1 "$file" | grep -q '^<?php'; then
        echo "OSTRZEŻENIE: $file nie zaczyna się od <?php — pomijam."
        COUNT_SKIPPED=$((COUNT_SKIPPED + 1))
        continue
    fi

    TMP_FILE=$(mktemp)

    {
        echo "<?php"
        echo ""
        echo "$LICENSE_BLOCK"
        tail -n +2 "$file" | sed '/./,$!d'
    } > "$TMP_FILE"

    mv "$TMP_FILE" "$file"
    COUNT_UPDATED=$((COUNT_UPDATED + 1))
    echo "Zaktualizowano: $file"
done < <(find "$TARGET_DIR" -type f -name "*.php" -print0)

echo ""
echo "Gotowe. Zaktualizowano: $COUNT_UPDATED plik(ów). Pominięto: $COUNT_SKIPPED plik(ów)."
