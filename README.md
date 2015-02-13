# Code Quality Threshold

This extension is purposed to run every necessary tool to check the code against violation from composer 
(ex. tools - PHP Mess Detector, PHP Code Sniffer, PHP Dead Code Detector, PHP Copy Paste).

## Installation

    composer require piotrpasich/code-quality-threshold:0.2
   
## Configuration

To your composer.json file you might add 

    "scripts": {
        "quality-test": [
            "piotrpasich\\CodeQualityThreshold\\Composer\\ScriptHandler::checkThresholds"
        ]
    },
    
And execute the command

    composer quality-test
    
You can also add this script to already existing scripts like `post-update-cmd`.

## Reports

If the test throws an exception you can always ask for the report:

    composer quality-test -v

## Advanced configuration

This plugin checks the app folder by default, but you can always change this behavior by creating yml file. This might be
placed wherever you want, ex. app/Config/cqt.yml

Then you need to add to your composer.json file the extra option:

    "extra": {
        "cqt-parameters": {
            "file": "app/Config/cqt.yml"
        }
    }
 
The example yml file might look like:

    phpmd:
        class: piotrpasich\CodeQualityThreshold\Tool\Phpmd
        options:
            threshold: 42
            directory: src
            rules: Config/Phpmd/ruleset.xml
    
    phpcs:
        class: piotrpasich\CodeQualityThreshold\Tool\Phpcs
        options:
            threshold: 42
            rules: Config/Phpcs/ruleset.xml
            directory: src
    
    phpcpd:
        class: piotrpasich\CodeQualityThreshold\Tool\Phpcpd
        options:
            threshold: 42
            directory: src
    
    Phpdcd:
        class: piotrpasich\CodeQualityThreshold\Tool\Phpdcd
        options:
            directory: src
            threshold: 21
            
## Adding new tools

To add new tool you need to specify new record in yml file (look at the Advanced configuration chapter) and create a class
extending Tool abstract class from the plugin.
