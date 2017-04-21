[![Build Status](https://scrutinizer-ci.com/g/gplcart/export/badges/build.png?b=master)](https://scrutinizer-ci.com/g/gplcart/export/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gplcart/export/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/gplcart/export/?branch=master)

Exporter is a powerful tool for [GPL Cart](https://github.com/gplcart/gplcart) powered sites that intended to export products to a CSV file

**Features**

- Can handle *thousands* of products on a cheap hosting without any server configuration
- Configurable CSV format/columns
- Simple settings and UI


**Installation**

1. Download and extract to `system/modules` manually or using composer `composer require gplcart/export`. IMPORTANT: If you downloaded the module manually, be sure that the name of extracted module folder doesn't contain a branch/version suffix, e.g `-master`. Rename if needed.
2. Go to `admin/module/list` end enable the module
3. Adjust settings at `admin/module/settings/export`
4. Allow administrators to use Exporter by giving them permissions `Exporter: export products` at `admin/user/role`

**Usage**

- Go to `admin/tool/export` and export your products