# photosort
Simple PHP commandline tool for indexing and deduplicating photo archives.

This code is not remotely PSR standard or composable. Requires PHP 7.0 or higher, solely due to the the fact that that's what's installed on my distro by default and if I have to write another line of 5.3.x compatible code in my life again, it will be too soon.

Run ./RebuildArchive `--path <path to your huge directory tree of photos>` `[--index <path to output index>]` to build up an index of the existing archive.
- The --path parameter is mandatory.
- The --index parameter is optional. If not provided, the default is data/archive.index.
Any duplicates detected are raised for manual resolution.

Run ./ScanBackup `--path <path to your most recent backup>` `[--index <path to output index>]` to scan a backup pulled from your camera/phone/whatever to compare against your index.
- The --path parameter is mandatory.
- The --index parameter is optional. If not provided, the default is data/archive.index.

Duplicates that already exist in the index will be moved into a .duplicates subdirectory within the source. The images that remain can then be copied manually over to your archive.

Lots of bugs and issues. However it shouldn't do anything destructive.
