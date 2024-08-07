# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased] - yyyy-mm-dd
### Added
### Changed
### Fixed

## [2.8.0] - 2024-04-08
### Changed
- Updated package laminas/laminas-diactoros to 2.26.0

## [2.7.0] - 2024-03-04
### Added
- Allow custom way to connect to redis

## [2.6.0] - 2023-06-14
### Added
- Add CC option to SES

## [2.5.0] - 2023-06-08
### Added
- Add endpoints for databases and tables
- Fixed issues found with PHPStan level 8

## [2.4.0] - 2023-06-01
### Added
- Add DSE service and unit tests
### Changed
- updated composer packages
- updated readme

## [2.3.0] - 2023-05-15
### Changed
- use custom domain for notify

## [2.2.0] - 2023-04-06
### Changed
- Expire the token in Redis 10 minutes before the token actually expires

## [2.1.0] - 2023-03-23
### Added
- Support for PHP 8

## [2.0.5] - 2022-10-25
### Updated
- Url for Notify prod

## [2.0.4] - 2022-10-19
### Updated
- Url for Notify stage

## [2.0.3] - 2022-10-18
### Updated
- Add bcc to email services
- Include code coverage badge

## [2.0.2] - 2022-10-17
### Updated
- Updated local and dev endpoints for Notify
- Updated readme

## [2.0.1] - 2022-10-14
### Updated
- Updated readme

## [2.0.0] - 2022-10-13
### Added
- Notify service

### Changed
- Updated logging tests
- Refactored service file structure

## [1.8.2] - 2022-09-14
### Added
- Add DataDog context to log record
- 
## [1.8.1] - 2022-09-07
### Changed
- Updated environment label from staging to stage

## [1.8.0] - 2022-05-16
### Changed
- Handle token request errors
- Catch logging request errors

## [1.7.0] - 2022-05-11
### Changed
- Auth service endpoint change for staging

## [1.6.1] - 2022-05-11
### Changed
- Catch error on put() method

## [1.6.0] - 2022-05-11
### Changed
- Logging service endpoint change for staging

## [1.5.0] - 2022-03-31
### Add
- coverage report
### Changed
- Upgrade packages for php 7.4

## [1.4.1] - 2022-03-28
### Changed
- Remove specific ext-json version

## [1.4.0] - 2022-03-03
### Updated
- Downgrade more packages for php 7.2

## [1.3.1] - 2022-03-03
### Updated
- Downgrade laminas/laminas-diactoros to 2.4

## [1.3.0] - 2022-03-03
### Updated
- Remove property type declarations

## [1.2.0] - 2022-02-24
### Updated
- Updated Logging arguments with context array
- Updated readme

## [1.1.1] - 2022-02-24
### Updated
- Updated default endpoints
- Add env based endpoint for Logging
- Add missing PHPDoc

## [1.1.0] - 2022-02-23
### Added
- Added tests

## [1.0.0] - 2022-02-22
### Added
- Create sdk
- Add  Logging Service