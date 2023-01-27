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
- Ian Magero

# Release Notes

## Version 12.4

- When an email is changed in TWB Platform...
  - In Phrase, the email is changed.
  - In Moodle, the email is changed.
  - If any of the emails was not successfully changed, it will be reported back to the user.
- When a user is deleted in TWB Platform...
  - In Phrase, the email will be set to the same randomised email as the one in TWB Platform. Other names will also be overwritten/randomised.
  - The TWB Community account will be deleted.
  - The Moodle user will be deleted.
- A link to "TWB Learning Center" has been added to TWB Platform pages
- When a "temporary" error occurs when a post is made to Asana for project tasks, a retry occurs a minute later.
- The dev TWB Platform now connected to dev Community
- A situation could occur that the API layer was logged out, but not the front end TWB Platform layer. If this happens the front end layer is logged out.
- Deadline reminders are sent even for unpublished tasks.

## Version 12.3

- The password reset mechanism has been simplified.
  - Multiple password reset emails will now be sent when requested (not just one).
  - These will all be identical, so any of the identical links can be used (this is to avoid user confusion). THE C++ code has been updated to allow identical emails to go out in this case.
  - However there will be a limit of 4 emails a day to avoid possible spamming.
  - If a user requests a password reset before they have acted on the verification email from registration (and users seem to do this), completing password reset will act as a substitute (verifying the user's email).
  - There is a button in user's profile (visible only to admins and only if the user has requested a reset) which says "Link emailed to User for Password Reset". Clicking on this will bring up the link which can be sent to a user having trouble. Or could be used to reset a password on a user's behalf.
- Task Stream emails were being processed in a way that caused queues to overflow. Now the decision to send or not is made earlier so there is no risk of overflow.
- Links to the dev instance of Phrase from dev.translatorswb.org will now work (project and task pages).

## Version 12.2

- Edited project deadlines are now sent to Phrase.
- Upgraded Task table for cancelled and word_count_original fields.
- Upgraded tasks_status_audit_trail for cancelled field.
- There is UI to set Cancelled in Project View (and also to display it in Task View):
  - When cancelled, propagate to all tasks for same file.
  - Don't cancel something that is already cancelled.
  - Set cancelled for all the tasks in TWB Platform.
  - Set the CANCELLED status in Phrase for all the jobs.
  - For CLAIMED and IN_PROGRESS tasks get the confirmed wordcount from Phrase and store in wordcount for the task (for recognition).
  - Send email to linguist for all tasks.
  - Set the audit trail to show cancelled for all tasks.
- If un-cancelled (via the Project View UI):
  - Don't un-cancel something that is already un-cancelled.
  - Set un-cancelled for the task in TWB Platform.
  - Set back the status in Phrase to whatever it was before (based on TWB status).
  - Set the TWB wordcount to whatever it was before (word_count_original).
  - Set the audit trail to show un-cancelled.
- https://kato.translatorswb.org/org/dashboard/ has a new % complete calculation based on words completed.
- When a user's email is changed on TWB Platform, the next time the user logs into TWB Community, their email will be updated there.
- Fixed a bug: "Email Verification" was not working since March 2022

## Version 12.1

- Self service projects are automatically split if they have more than 2000 words (translation only is split).
- Added a minimum week deadline for self service projects (including limits on changes).
- The four Task view, Task id, Org feedback and Task complete pages have been merged into one.
- The language names used when creating Asana and TWB Community entries for projects (language pairs) are aligned with the names in selection lists.
- Fixed a bug that would not allow a user to remove a URL from their profile.

## Version 12.0

- Added support for the Proofreading and Approval Phrase TMS Workflow and generalised adding new workflows.
- Moved to system of allowing all tasks to be claimed when previous level in workflow is all claimed.
- Added new task status of CLAIMED (but not yet IN_PROGRESS, because prerequisite tasks are not complete).
- This expanded status is stored in a new table (tasks_status).
- Added an audit trail of task status changes (tasks_status_audit_trail which also includes, where appropriate, the linguist, changed time and a comment).
- Changed UI references from "Memsource" to "Phrase TMS".
- Removed support for Matecat (old projects remain in system).
- Removed code supporting pre-Matecat file based download and uploads.
- Removed User verification system (where a user could create a project as a test to be verified).
- Added new wording for registration related emails.
- Removed Tweeting from TWB Platform.
- Security fix (low priority): Bump smarty/smarty from 4.1.1 to 4.2.1 in /ui (PR #1288)
- Security fix (low priority): Bump twig/twig from 3.3.8 to 3.4.3 in /ui
- Updated the project view UI:
  - Included selection of tasks based on task types(translation,revision,proofreading...)
  - Added actions which include - Marking tasks as paid or unpaid,Marking tasks as claimed or Waiting
  - Updated the task table to include relevant information and make it leaner by use of icons and tooltips
  - Added selection on individual languages for the task selection to be more specific

## Version 11.1

- Change the language mapping from selection lists and from and to Memsource to use a database table for flexibility
- Added new four letter Country codes: LATN, CYRL, 419, HANS, HANT, ARAB, BENG
- Changed the registration error message for spam from "Spam Detected!" to "Oops! something went wrong, please try again." and keep the user's names and email to make resubmitting easier.
It is not clear why a User gets this message (we use Google spam detection), it is intended for Robots.

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

# Collaborative source code management

Please see the wiki page [Contributing code](https://github.com/TheRosettaFoundation/SOLAS-Match/wiki/Contributing-code)
for the standards followed by this project (such as git version control, and coding
style).
