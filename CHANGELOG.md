# Changelog

## 10.1 — 2026-11-07

- Enforces some verification before registering module

## 10.0 — 2026-08-03

- Defer loaded script execution (wait for dmHelper)
- Do not try to talk to server if no internet connection
- Avoid using magic methods for user preferences and blog settings (2.39+)
- Use MetaRecord::strField(), MetaRecord::intField() and MetaRecord::boolField() whenever is possible

## 9.0 — 2026-04-29

- Use new MetaRecord::cardinal() method

## 8.5 — 2026-04-09

- DA -&gt; DC

## 8.4 — 2025-12-14

- A11y: add en empty alt attribute to icon image in module title

## 8.3 — 2025-10-25

- Do not look for oldies

## 8.2 — 2025-09-22

- Use backend services rather than static calls

## 8.1 — 2025-09-11

- Review context checking: admin only for all backend

## 8.0 — 2025-09-07

- DC 2.36 compliance

## 7.0 — 2025-06-03

- Use dmHelper 5.0+

## 6.1 — 2025-05-04

- Use Helper\Html\Form\Span rather than Text('span',…)

## 6.0 — 2025-05-04

- Use timestamp Html/Form/Component

## 5.4 — 2025-03-06

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 5.3 — 2025-02-24

- Cope with time zone

## 5.2 — 2025-02-24

- Forgot this one

## 5.1 — 2025-02-24

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 5.0 — 2025-01-09

- 2.33 compliance

## 4.6 — 2024-12-05

- Ensure returned nb by REST method is numeric (int)

## 4.5 — 2024-11-28

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 4.4 — 2024-11-28

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 4.3 — 2024-09-05

- Add an autorefresh option (was implicit before)

## 4.2 — 2024-01-07

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 4.1 — 2023-10-27

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 4.0 — 2023-10-24

- Use instanced item constants rather than interface ones
- 2.28 compliance

## 3.0 — 2023-08-23

- Dotclear 2.27 compliance, PHP 8.1+

## 2.4 — 2023-07-03

- Add refresh interval setting (still 60 seconds by default)

## 2.3 — 2023-06-30

- Switch to svg icon

## 2.2.2 — 2023-06-27

- Add local static $init variable

## 2.2.1 — 2023-06-26

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 2.2 — 2023-06-25

- Remove unused code

## 2.1 — 2023-06-19

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 2.0.2 — 2023-06-01

- Fix type hint

## 2.0.1 — 2023-05-29

- Fix small/large

## 2.0 — 2023-05-29

- fr 3 plural forms
- Switch to namespace
- Prepare 2.26
- Minified version
- New install

## 1.0 — 2022-11-29

- Avoid notice on new installation

## 0.9 — 2022-11-25

- 2.24 compliance

## 0.8.2 — 2022-08-15

- Same player shoot again :-p

## 0.8.1 — 2022-08-15

- Usual oups :-)

## 0.8 — 2022-08-14

- Update default branch in _define.php for alternative repository
- Add alternative repository

## 0.7 — 2021-08-06

- Dotclear 2.19 compliance

## 0.6.1 — 2020-05-02

- Avoid undefined display

## 0.6 — 2020-04-01

- Dotclear 2.16 compliance, no more inline JS
- Create LICENSE

## 0.5 — 2019-09-30

- Switching from hg to git
- use typed form fields
- Use let and const rather than var (ES2015/ES6), rewrite promise
- short notation for array (array() → [])
- Use dotclear.badge() function
- Add preview for posts and comments, needs 2.15

## 0.4 — 2018-08-13

- Cope with no pending posts or comments
- Complete plugin infos
- Use css3 flex rather than &lt;br &gt; to vertical align supplemental labels under dashboard icons
- Insert pending counter only if at least one pending element
- Animate counters of pending posts/comments if displayed (dashboard icons), next release will be 0.4
- Using dcPage::cssLoad, dcPage::jsLoad and dcPage::getPF is a better way to load plugin resources

## 0.3 — 2013-10-04

- new _define.php
- New icon
- No more $GLOBALS at top level, and various
- Cope with dashboard box sizes
- Smaller sentences
- Plural forms
- various (next release will be 0.3)
- 2.6 user pref compliance
- Autres correctifs et améliorations.

## 0.2 — 2012-11-29

- 0.2
- Enhancement idea from JcDenis
- Missing = between for and " in some label tags
- harmonize &lt;label&gt;&lt;/label&gt; in forms
- Add missing files
- Revert to original name and desc in define.php of plugins. Localized versions are not required here.
- Retrocompatibility with 2.4.4 behaviours
- Update for future DC 2.5

## 0.1.2 — 2012-03-28

- Minor change ; use post_dt and comment_dt instead of post_upddt and comment_upddt

## 0.1.1 — 2012-03-25

- Nb of pending posts used for pending comments : fixed

## 0.1 — 2011-07-27

- Fignolages
- Traductions françaises
- Affichages des listes
- Gestion des préférences utilisateur
- Affichages des modules vides
- Préférences par défaut, behaviours non traités
