# Changelog

## 7.1 — 2026-11-07

- Enforces some verification before registering module

## 7.0 — 2026-08-03

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 6.3 — 2026-04-07

- DA -&gt; DC

## 6.2 — 2025-10-11

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 6.1 — 2025-09-12

- Replace static call to Ctx by context() — thanks JcDenis for advice

## 6.0 — 2025-09-07

- DC 2.36 compliance

## 5.0 — 2025-03-21

- Use TemplateHelper for template blocks/values

## 4.2 — 2024-09-04

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 4.1 — 2023-10-27

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 4.0 — 2023-10-25

- 2.28 compliance

## 3.0 — 2023-08-25

- Dotclear 2.27 compliance, PHP 8.1+

## 2.1.1 — 2023-06-27

- Add local static $init variable

## 2.1 — 2023-06-15

- Add void Install.php

## 2.0 — 2023-05-26

- Switch to namespace

## 1.0 — 2022-11-27

- Dotclear 2.24 compliance

## 0.4.1 — 2022-08-14

- Usual oups :-)

## 0.4 — 2022-08-13

- Update default branch in _define.php for alternative repository
- Add alternative repository

## 0.3.1 — 2020-09-10

- Cope with full attribute
- Various code formating

## 0.3 — 2020-03-27

- Add support and detail URLs
- Cleanup afert switching from hg
- short notation for array (array() → [])
- gracefulCut now takes place of cut_string filter if possible (no encode_xml nor encode_html in tag's attributes)
- Take place of cut_string if possible
- Signed tag 0.2 for revision 7

## 0.2 — 2017-08-07

- Back to 0.2 as it has never been distributed since 0.1
- Restore initial cut_string if replaced by graceful_cut
- Bugfix and new template IfGracefulCut

## 0.1 — 2016-12-02

- Prettify code and add some comments
- Take advantage of 2.11 opened filter tags Will replace cut_string, but only if encode_xml nor encode_html are no set or equals to 0
- First release, some bugs remain
