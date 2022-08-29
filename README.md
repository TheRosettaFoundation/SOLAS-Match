# SOLAS Match

## What is this?

The Rosetta Foundation SOLAS Match is a web application. It
is designed to be accessed by two groups: NGOs seeking to have content
translated, and volunteer translators who may complete such tasks.
Accessed through the browser, the application displays a list of
translation tasks previously uploaded to the system. A task may have
one resource file attached to it which can be downloaded, translated,
and re-uploaded. Development began in February 2011.

# License notice

This software is licensed under the terms of the GNU LESSER GENERAL PUBLIC LICENSE
Version 3, 29 June 2007
For full terms see License.txt or http://www.gnu.org/licenses/lgpl-3.0.txt

# Contact

**Contact:**

- Reinhard Schäler <reinhard.schaler@ul.ie>

**Coded by:**

- Eoin Ó Conchúir <eoin.oconchuir@ul.ie>
- David O Carroll <david.ocarroll@ul.ie>
- Sean Mooney <Sean.Mooney@ul.ie>
- Manuel Honegger <Manuel.Honegger@ul.ie>
- Phillip O’Duffy
- Raymond Kearney
- Mark Cummins
- Asanka Wasala
- Tadhg O’Flaherty
- Aaron Mason
- Alan Barrett

# Technical Requirements

SOLAS Match is written in PHP 5.4+ code and makes use of
a MySQL 5+ database.

## System Requirements

HTTP server (Apache, for example) with PHP 5.4+ (ideally 5.5+) interpreter MySQL 5+
database

Several additional libraries also need to be installed alongside
SOLAS Match.
See the following installation instructions.

# Release Notes

## Version 11.0

**New features and improvements**

- Change sourcing from loose to strict

  - Updated user qualified pairs to eliminate language-country code combinations that no longer exist
  - Added an alert in the profile page for linguists whose target country code could not be directly mapped, so that they update it themselves

    - When one of those users logs in, they get redirected to their profile with the alert visible so that they can directly update the language pairs

  - Updated the linguist task stream (and email stream) to only display tasks that match the source language (regardless of country code) and the target language-country code combination
  - Added an option for project managers to search for linguists based on the newly-implemented strict sourcing or loose sourcing which matches source and target languages regardless of country code or script
  - Updated the language names
  - Spanish Mexico removed and replaced with Spanish Latin America

- When a project is created or updated, if the post to Asana fails because of an invalid user, post anyway but without an assignee
- Allow blank Facebook, LinkedIn and home page for an Organization
- Add a table and processing for Project Complete Dates
- Convert these languages to have no country in selection lists (and update profiles accordingly):

  'Afar' ('aa'), 'Bemba' ('bem'), 'Bikol' ('bik'), 'Bura-Pabir' ('bwr'), 'Cebuano' ('ceb'), 'Chavacano' ('cbk'), 'Kibaku' ('ckl'), 'Glavda' ('glw'), 'Guaraní' ('gn'), 'Haitian' ('ht'), 'Hiligaynon' ('hil'), "K'iche'" ('quc'), 'Kamwe' ('hig'), 'Pampanga' ('pam'), 'Karen' ('kar'), 'Khmer' ('km'), 'Kikuyu' ('ki'), 'Kinyarwanda' ('rw'), 'Kongo' ('kg'), 'Konkani' ('kok'), 'Krio' ('kri'), 'Lamang' ('hia'), 'Luba-Lulua' ('lua'), 'Maguindanaon' ('mdh'), 'Maranao' ('mrw'), 'Marghi Central' ('mrt'), 'Mongo' ('lol'), 'Nande' ('nnb'), 'Ngombe' ('ngc'), 'Northern Sotho' ('nso'), 'Nuer' ('nus'), 'Pangasinan' ('pag'), 'Pushto, Pakistan' ('ps'), 'Pijin' ('pis'), 'Samoan' ('sm'), 'Sena' ('seh'), 'Shi' ('shr'), 'Shona' ('sn'), 'Sindhi Devenagari' ('sd'), 'Southern Sotho' ('st'), 'Sylheti' ('syl'), 'Tagalog' ('tl'), 'Tatar' ('tt'), 'Tok Pisin' ('tpi'), 'Tonga' ('to'), 'Tsonga' ('ts'), 'Tswana' ('tn'), 'Turkmen' ('tk') 'Waray' ('war'), 'Xhosa' ('xh'), 'Yoruba' ('yo')

## Version 10.1

**New features and improvements**

- Platform name update from Kato platform to TWB platform on the website and all email communications.
- Minor layout enhancements (homepage logo and social media icons, consistent footer on all pages, website favicon)
- "Turkey" was updated to "Türkiye" in the platform
- Removed phone number information for linguists from the UI and the database

**Bug fixes**

- Fixed an issue with the Moodle certificate for names with special characters. Font used is temporary pending finalizing the decision on the font guidelines

## Version 10.0

**SLIM 4 upgrade**

- Upgrade Slim backend framework from v2 to v4

## Version 9.0

**New registration UI**

- Google Captcha - Get exact message if spam is detected
- Registration - removed email2 input field and its associated validation
- Rewrote the checkbox logic on the user profile
- Resized the social media icons on the header

## Version 8.0

**Memsource integration**

- Create user on Memsource automatically
- Automatically login to Memsource using SSO to use it as a translation tool
- Use of webhooks to get project information from Memsource to TWB platform
- List all Memsource projects with the most recent on TWB platform with their respective project owners

## Version 7.0

- Upgraded PHP to v7.2

## Version 6.0

- Integrated Matecat

## Version 5.0

- Added UserQualifiedPairs to manage the language pairs

## Version 4.0

- Integrated Neon CRM integration

## Version 3.0

- Updated the SQL for Task Stream ordering

## Version 2.0

- Updated JavaScript

# Create an Org Account

Create a user by registering with the app. This can be done by supplying a valid email and a password.
Once a user has been created you can create an organisation and begin to create projects and tasks.
For more infomation see the videos tab in the web UI.

# Collaborative source code management

Please see the wiki page [Contributing code](https://github.com/TheRosettaFoundation/SOLAS-Match/wiki/Contributing-code)
for the standards followed by this project (such as git version control, and coding
style).
