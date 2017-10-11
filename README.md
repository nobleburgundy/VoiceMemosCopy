# VoiceMemosCopy
Copys iPhone VoiceMemos post iTunes sync to desitnation folder with titles!!! Requires Library.xml be exported first.

## Prerequists
+ ! Requires PHP !
+ Need to have VoiceMemos syncd via iTunes (or other) to computer.
+ From iTunes, Export Library.xml file that contains data on files. 

!!! **WARNING** !!! I've only used this on my own system that doesn't have other music, so you will want to modify the script to work for your system and setup.  Will copy all .m4a files to new directory as is.

## Steps
1. Download php file.
2. From directory run...
```php
php copyVoiceMemosWithTitles -x {path to Library.xml including file name} -d {destination dir}
```
