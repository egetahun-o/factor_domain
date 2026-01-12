
# Factor domain for Multi-Factor Authentication

This plugin is a Moodle Multi-Factor Authentication (MFA) factor that automatically passes MFA when a user’s email domain matches a configured allow‑list of domains.

## Overview

- **Name**: Domain-based MFA factor (`factor_domain`).
- Purpose: Grant MFA points automatically to users whose email address belongs to one of the configured email domains, reducing friction for trusted domains.
- Data storage: The factor itself does not store user-specific data; it only reads user email and configuration.


## Features

- Automatic pass/neutral state based on user’s email domain compared to a configured list of allowed domains.
- No user interaction, setup screens, or extra input fields; the factor is fully automatic and transparent to users.
- Configurable weight (points) contributed to the MFA decision when the factor passes, using standard `tool_mfa` factor interfaces.


## Installation

1. Place the plugin code in: `admin/tool/mfa/factor/domain` so that the main class is available as `factor_domain` within the `tool_mfa` factor system.
2. Visit Site administration → Notifications to trigger the Moodle upgrade and install the factor, ensuring `tool_mfa` is already installed and enabled.
3. After installation, enable and configure the factor under Site administration → Plugins → Admin tools → Multi-factor authentication → Factors.

## Configuration

- **Enable factor**: Toggle the `enabled` setting for `factor_domain` in the factor configuration page to activate or deactivate this factor.
- **Allowed domains**: Configure `alloweddomains` as a newline-separated list (for example, `example.com` on one line, `school.edu` on another); MFA will pass when the user’s email domain exactly matches one of these entries.
- **Weight (points)**: Set the `weight` configuration to define how many MFA points are granted when the factor passes; if not set, a default such as 100 is used.


## Behaviour

- If no allowed domains are configured, or the user’s email is empty/invalid, the factor returns a neutral state and contributes 0 points.
- When the user’s email domain matches one of the configured domains (case-insensitive), the factor returns a pass state and contributes its configured weight as MFA points.
- The factor does not record per-user factor data, cannot be revoked, and exposes no user setup or revoke UI; it is always evaluated automatically for eligible users.


## Privacy

- The factor does not store any personal data of its own and relies on existing user and configuration data in Moodle.
- The privacy provider implements `core_privacy\local\metadata\null_provider` and returns a reason string `privacy:metadata`, indicating no additional data is stored by this plugin.


