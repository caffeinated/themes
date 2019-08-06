# Changelog
All notable changes to this package will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this package adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [6.1.0] - 2019-08-06
### Added
- Laravel 6 support
- Laravel 5.7 support

## [6.0.0] - 2019-03-31
### Added
- Laravel 5.8 support

### Removed
- Laravel 5.7 support

## [5.0.4] - 2019-02-25
### Fixed
- Service provider properly extends the Laravel service provider base class

## [5.0.3] - 2019-02-04
### Removed
- Caffeinated Manifest Composer dependency. This has been merged in and now fully utilizes Laravel's collection class rather than my framework agnostic collection package.

## [5.0.2] - 2018-12-13
### Fixed
- Composer dependencies

## [5.0.1] - 2018-12-12
### Fixed
- Composer dependencies

## [5.0.0] - 2018-12-12
### Added
- `make:theme` Artisan command
- `theme_path()` helper method
- Automated symlinking of public assets

### Changed
- Themes are now namespaced
- Themes can now extend Laravel through Service Providers
- Themes are properly autoloaded through composer automatically
- Themes can be registered and distributed through Composer
- Themes have a fixed structure

### Removed
- Customizable theme paths, themes sit at the root of your application now

## [4.1.0] - 2018-09-06
### Added
- Laravel 5.7 support

## [4.0.4] - 2018-04-01
### Added
- Laravel 5.6 support

## [4.0.3] - 2017-11-22
### Fixed
- Service registration will no longer fail when themes directory doesn't exist

## [4.0.2] - 2017-09-18
### Added
- Laravel 5.5 support

## [4.0.1] - 2017-08-10
### Fixed
- Properly resolve namespace path for themes

## [4.0.0] - 2017-08-10
### Added
- Native support for Laravel's FileViewFinder

## [3.0.1] - 2017-02-08
### Added
- Laravel 5.4 support

## [3.0.0] - 2016-08-23
### Added
- Laravel 5.3 support

### Removed
- Components feature
- Caffeinated Sapling package (Twig)

## [2.0.5] - 2016-02-13
### Changed
- Pass path through `url()` method in `asset()` method

## [2.0.4] - 2016-01-01
### Changed
- updated reference from `bindShared` to `singleton`

## [2.0.3] - 2015-12-31
### Changed
- Support both Laravel 5.1 and 5.2

## [2.0.2] - 2015-12-29
### Changed
- Added `url()` to `Theme::asset()` method

## [2.0.1] - 2015-07-06
### Fixed
- Laravel 5.2 support

## [2.0.0] - 2015-06-23
### Added
- Laravel 5.1 support

## [1.2.3] - 2015-06-12
### Fixed
- Laravel dependencies
