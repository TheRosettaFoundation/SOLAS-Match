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
- Reine Alice Iramurikiye

# Release Notes

## Version 16.2

- Tasks can now be set to require linguists to have matching Native Language and/or Variant or neither. This setting is only set automatically on task creation for a subset of target languages. It is changed automatically for self service tasks to neither after 2 days (in stages). Other tasks are left for the PO to change.
- As part of this when a PO is setting these values in Project View, they will see the number of linguists who could claim the task for each setting value and additionally the smaller number of linguists for each setting value that have claimed a task previously.
- When canceling a task, it is now possible to decided to cancel only the one task or all tasks for the language pair for the file.
- Project description now has rich text editing support.

## Version 16.1

- Ability to create invoices for completed paid tasks on behalf of linguists (which they can download) and store these in Google Drive.
- A new FINANCE role has been added.

## Version 16.0

- New Responsive UI added to Linguist facing parts of the system.
- Expanded "task_type_details" table to include: "unit_rate_pricing_default" (Default Pricing Unit Rate).
- Expanded "TaskPaids" table to include: "unit_rate_pricing", this can be updated in the Task View, but it is initially set to "unit_rate_pricing_default".
- Upgraded "Task" table for "word_count_partner_weighted" field. This is initially set to the standard word count but it can be overridden in Task Alter. It will eventually come from Phrase.
Existing reports have been upgraded to have pricing based on "unit_rate_pricing" and "word_count_partner_weighted".
- A new Partner specific report for pricing has been added.
- A new table "user_paid_eligible_pairs" has been added to indicate who is eligible for paid tasks for a specific language pair. It is displayed in the Search Translators page.
- Security fix (low priority): phpseclib a large prime can cause a denial of service
- Security fix (low priority): phpseclib does not properly limit the ASN1 OID length
- Security fix (medium priority): Follow Redirects improperly handles URLs in the url.parse() function
- Security fix (medium priority): follow-redirects' Proxy-Authorization header kept across hosts

## Version 15.2

- Allow contracts to be sent to Linguists for Paid Work using Docusign.
- Allow "Country of Residence" and  "Google Drive Link" to be saved in a Linguist's profile.
- If a Linguist has some type of limit placed on claiming tasks, such tasks will not appear in their task stream.
- Don't allow "Volunteer Restrictions" "allowed_types" to be "0".

## Version 15.1

- Reports added...
  - All Paid Projects (with at least one paid task)
  - All Paid Projects (with at least one paid task) for Hubspot Deal ID: ...
  - All Deals
  - In Project View (new info on paid projects)
- new Shell Tasks added...
  - "Translation Outside Phrase"
  - "Revision Outside Phrase"
  - "Audiovisual Sign Off"
- Updates to Linguist Certificates and Letter
- Filter on home page only shows language pairs for which the linguist has claimable tasks
- When a Phrase compare analysis is generated for an exact match Job pair, the complementary compare analyses for the reverse pair is copied from that and stored in the TWB Platform database.

## Version 15.0

- A new system of roles has been introduced: SITE_ADMIN, PROJECT_OFFICER, COMMUNITY_OFFICER, LINGUIST, NGO_ADMIN, NGO_PROJECT_OFFICER, NGO_LINGUIST
- These are stored in a bitmap "roles" in the "Admins" table.
- A new system to invite admin/organization users has been introduced (emails are sent which guides them through a registration path which end up giving them the desired roles)...
  - for TWB Platform invites, go to https://twbplatform.org/admin/ and click "Invite New Admin User"
  - for NGO invites, go to https://twbplatform.org/org/{org_id}/profile and click "Invite New User to be Assigned Role"
- It is also possible to change the roles of existing admins (this will be required to give the site admin role, as a security precaution).
- NGO_LINGUISTs (if a user only has that role for an organisation), are limited to seeing tasks for that organisation etc.
- In a user's profile there is a mechanism to limit...
  - Claimed tasks restriction: admins will be able to set restrictions on
  - Tasks number - that volunteer can have only 2 tasks in progress max.
  - Tasks type - the volunteer will be able to claim only indicated tasks
  - Partners - the volunteer won’t be able to claim tasks for that partner
  - Lock a profile so that the person is not able to make changes to the following fields:
    - Native Language
    - Language combinations
    - Subscription to task stream
- There is a new table "org_TWB_contacts" which contains the user who created an organisation, this may be changed in the future to include a current internal contact for an Organization.
- The Phrase "note" on a project is intelligently truncated to fix this: https://app.asana.com/0/1200233953889285/1205531183622741/f
- Task Type name changed from "Revising" to "Revision" to fix this: https://app.asana.com/0/1205139046449623/1205490391710106/f
- A new feature which stores task resource info into 3 tables is included. Data is stored in these tables...
  - task_resource_infos
  - task_resource_TBs
  - task_resource_TMs
  - They are updated on any job status change and for up to 3 days after the last status change.
- Addressed this security request: https://github.com/TheRosettaFoundation/SOLAS-Match/pull/1294

## Version 14.1

- A Phrase analysis and appropriate compare analyses are generated when a Phrase task is completed and stored in the TWB Platform database.
- Recognition points now include Shell Tasks. There is now also an hours badge for those tasks that do not have word counts.

## Version 14

- Removed Rabbit MQ from the system because our drivers are very old.
- Added a table "emails" which queues outgoing emails. This is read by our SMTP driver and as emails are handed over to SMTP sent they are marked as such, but not immediately deleted so we can resent if there is a major failure.
- Added a table "qxt_smtp_emails" in which the SMTP driver contains a reference to the previous table and are marked as sent when sent over the socket to the remote SMTP server. Likewise these are not deleted so we can work out what to resend if that is necessary.
- Added a table "queue_requests" in which requests to the C++ from the PHP and also from internal schedulers (timed tasks). These are read in 3 different queues with different priorities for action. Likewise these are not deleted immediately when run.
- Addressed this security request: https://github.com/TheRosettaFoundation/SOLAS-Match/pull/1291
- Addressed this security request: https://github.com/TheRosettaFoundation/SOLAS-Match/pull/1292
- Added the following Shell Tasks...
  - ( 7,'Terminology translation'),
  - ( 8,'DTP signoff'),
  - ( 9,'Voice recording'),
  - (10,'Subtitle Translation'),
  - (11,'Subtitle Revision'),
  - (12,'Captioning'),
  - (13,'Transcription'),
  - (14,'Voiceover'),
  - (15,'lexiQA quality assurance'),
  - (16,'Alignment'),
  - (17,'SME review'),
  - (18,'QA on Phrase'),
  - (19,'Language Quality Assessment'),
  - (20,'Monolingual proofreading'),
  - (21,'MTPE'),
  - (22,'Plain Language assessment'),
  - (23,'Plain Language editing'),
  - (24,'Plain Language training');
- Expanded "task_type_details" table and used these fields to support Shell Tasks. Here are the fields...
  - type_enum                         INT UNSIGNED NOT NULL,
  - type_category                     INT UNSIGNED NOT NULL,
  - enabled                           INT UNSIGNED NOT NULL,
  - for_self_managed                  INT UNSIGNED NOT NULL,
  - visible_community                 INT UNSIGNED NOT NULL,
  - shell_task                        INT UNSIGNED NOT NULL, # 1 => Shell Task
  - source_and_target                 INT UNSIGNED NOT NULL, # 1 => Has both Source and Target
  - sourcing                          INT UNSIGNED NOT NULL, # 0 => Pair (strict & loose), 1 => Target only (strict & loose), 2 => Pair and Target only (strict & loose)
  - type_text                         VARCHAR(50)  NOT NULL,
  - type_text_short                   VARCHAR(50)  NOT NULL,
  - colour                            VARCHAR(50)  NOT NULL,
  - claimed_template                  VARCHAR(50)  NOT NULL,
  - show_section                      VARCHAR(50)  NOT NULL,
  - memsource_name                    VARCHAR(50)  NOT NULL,
  - unit_count_text                   VARCHAR(50)  NOT NULL, # e.g. "Word Count" [was common_word_count]
  - unit_count_text_short             VARCHAR(50)  NOT NULL, # e.g. "words" [was project_profile_display_words]
  - pricing_and_recognition_unit_text VARCHAR(50)  NOT NULL,
  - source_unit_for_later_stats       VARCHAR(50)  NOT NULL, # e.g. Words Terms Pages Hours
  - unit_rate                         FLOAT        NOT NULL, # Default Unit Rate ($ Rate for Display in Task View)
  - rate_for_recognition              FLOAT        NOT NULL,
- A new table "task_urls" which contains an optional URL link which will be displayed in the Task View (for admins), Claimed Tasks and Claimed email and can be used for working on Shell Tasks.
- There is a new individual button to mark a claimed shell task as complete in the project view.
- If a Partner cancels a task, they must enter a reason (not just in the "other" case).
- More information is given before confirming task cancellation about what exactly happens: "Note: when you cancel a task all tasks in the same language pair/file combination will also be cancelled. Additionally an email will be sent to any linguists working on the tasks."
- Once assigned, task "unit_rate" is updated to the linguist rate for the task's type and language pair (which is set for linguists whose rate differs from the default, these are contained in the table "user_rate_pairs").
- If a linguist has a non default unit rate corresponding to a task, it is displayed in search translators.
- The box with "Unit Rate for Labor minutes" etc. has been added to search_tranlators (if paid).
- A Partner now cannot create a revision only project.

## Version 13.1

- Project view now allows...
  - Updating Tasks as ready for payment.
  - Updating Tasks as pending documentation.
  - Updating Tasks as settled.
  - Updating Tasks with purchase order number.
- It is now possible to add the HubSpot Deal ID to a Project (stored in the "project_complete_dates"). This is currently done in Project Edit.
- The Allocated budget for the project can also be set there.
- The HubSpot Google Sheet is imported into the "hubspot_deals" table when a project with a non zero Deal ID is edited OR if the new "Sync HubSpot" feature on the Admin page is used,
- A new value for the "status" field has been added to "project_complete_dates". This is in addition to "0" (New) and "1" (Complete). The new "status" is "2" (In Progress) and is set when all non-cancelled Tasks are In Progress (but not all Complete).
- A table "zahara_purchase_orders" now contains data from the Zahara Google Sheet.
- The "Sync" button under "Sync PO Status with Paid Tasks" (in the Admin page) updates this table (as well as updating the "payment_status" for Paid Tasks).
- In task view it is possible to set Purchase Order, Payment Status and Unit Rate and see Default Unit Rate and Total Expected Cost.

## Version 13.0

- Move to twbplatform.org server.
- Upgrade code and database tables to work with PHP8, MySQL 8 and QT6 (C++).
- Had to handle errors and do retrys for SMTP because of remote SMTP server (kato.translatorswb.org).
- Updated Google tracking codes for twbplatform.org.
- There is now logging in place for all emails sent in "email_sents" table: recipient_id, task_id, project_id, org_id, translator_id, admin_id, badge_id, topic, logged_time as appropriate.
- Added cleaner "start" and "stop" commands for backend C++.
- Volunteer letters and certificate requests can be generated by site admins (from the volunteer's profile)
- The Project Create button is disabled when submit happens to avoid a duplicate project creation
- Phrase sometimes passes '/' in a filename, this is changed to '_'.
- Fixed a bug causing the Task Type in Task Stream emails to be often incorrect.
- "message_id", "message_date" added to "post_login_messages" table.
- Added checkbox for emergency response to project create page
  - This allows partners to set a shorter (if clicked)
  - Those projects automatically get a tag "2023-turkeysyria"
- If linguists subscribe to the above tag they will get emailed within an hour if new tasks from a project with that tag become claimable, are published and match their language pairs. There is a new template for this.

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
- Claimed tasks "any task status" filter now working

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
