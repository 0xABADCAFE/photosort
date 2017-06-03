# photosort
Simple PHP commandline tool for indexing photo archives.

This code is not remotely PSR standard or composable.

Run ./RebuildArchive <path to your huge directory tree of photos> to build up an index of the existing archive. The archive is saved in data/.
Any duplicates detected are raised for manual resolution.

Run ./ScanBackup <path to your most recent backup> to scan a backup pulled from your camera/phone/whatever to compare against your indexed archive. Duplicates that already exist in the index will be moved into a .duplicates subdirectory within the source. The images that remain can then be copied manually over to your archive.
