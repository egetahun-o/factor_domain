<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

namespace factor_domain;

use tool_mfa\local\factor\object_factor_base;

defined('MOODLE_INTERNAL') || die();

/**
 * Domain bypass factor class.
 *
 * @package    factor_domain
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class factor extends object_factor_base {

    /**
     * Domain Factor implementation.
     * This factor allows users from specified email domains to bypass MFA.
     *
     * @param \stdClass $user the user to check against.
     * @return array Array of user factor records
     */
    public function get_all_user_factors($user): array {
        global $DB;
        $records = $DB->get_records('tool_mfa', ['userid' => $user->id, 'factor' => $this->name]);
        
		if (!empty($records)) {
            return $records;
        }

        // Null records returned, build new record.
        $record = [
            'userid' => $user->id,
            'factor' => $this->name,
            'timecreated' => time(),
            'createdfromip' => $user->lastip,
            'timemodified' => time(),
            'revoked' => 0,
        ];
        $record['id'] = $DB->insert_record('tool_mfa', $record, true);
        return [(object) $record];
    }

    /**
     * Check if the domain factor is enabled.
     *
     * @return bool True if enabled, false otherwise
     */
    public function is_enabled(): bool {
        $enabled = get_config('factor_domain', 'enabled');
        return (bool) $enabled;
    }

    /**
     * Gets the configured weight for this factor.
     *
     * @return int The weight value
     */
    public function get_weight(): int {
        $weight = get_config('factor_domain', 'weight');
        return (int) ($weight ?: 100);
    }

    /**
     * Check if this factor requires user input.
     *
     * @return bool Always false for domain factor
     */
    public function has_input(): bool {
        return false;
    }

    /**
     * Check if this factor requires user setup.
     *
     * @return bool Always false for domain factor
     */
    public function has_setup(): bool {
        return false;
    }

    /**
     * Get the current state of this factor.
     *
     * @return string The current state
     */
    public function get_state(): string {
        return $this->check_state();
    }

    /**
     * Get possible states for this factor.
     *
     * @param \stdClass $user the user to check against.
     * @return array Array of possible states
     */
    public function possible_states($user): array {
        return [
            \tool_mfa\plugininfo\factor::STATE_PASS,
            \tool_mfa\plugininfo\factor::STATE_NEUTRAL
        ];
    }

    /**
     * Get the summary condition for this factor.
     *
     * @return string The summary condition text
     */
    public function get_summary_condition(): string {
        return get_string('summarycondition', 'factor_domain');
    }

    /**
     * Checks the current state of this factor for the logged in user.
     *
     * @return string STATE_PASS if domain matches, STATE_NEUTRAL otherwise
     */
    public function check_state(): string {
        global $USER;

        // Get configured domains.
        $alloweddomains = get_config('factor_domain', 'alloweddomains');
        if (empty($alloweddomains)) {
            return \tool_mfa\plugininfo\factor::STATE_NEUTRAL;
        }

        // Parse domains from configuration.
        $domains = array_filter(array_map('trim', explode("\n", $alloweddomains)));
        if (empty($domains)) {
            return \tool_mfa\plugininfo\factor::STATE_NEUTRAL;
        }

        // Get user's email domain.
        $useremail = $USER->email ?? '';
        if (empty($useremail)) {
            return \tool_mfa\plugininfo\factor::STATE_NEUTRAL;
        }

        $emailparts = explode('@', $useremail);
        if (count($emailparts) !== 2) {
            return \tool_mfa\plugininfo\factor::STATE_NEUTRAL;
        }

        $userdomain = $emailparts[1];

        // Check if user's domain is in allowed list.
        foreach ($domains as $domain) {
            $domain = trim($domain);
            if (empty($domain)) {
                continue;
            }

            if (strtolower($userdomain) === strtolower($domain)) {
                return \tool_mfa\plugininfo\factor::STATE_PASS;
            }
        }

        return \tool_mfa\plugininfo\factor::STATE_NEUTRAL;
    }

    /**
     * Setup user factor data.
     *
     * @param \stdClass $user the user to setup factor for
     * @return bool Always true as no setup required
     */
  //  public function setup_user_factor($user): bool {
  //      // This factor doesn't require user setup.
  //      return true;
  //  }

    /**
     * Validate the factor for a user.
     *
     * @param \stdClass $user the user to validate factor for
     * @return bool Always true as validation is automatic
     */
    public function validate_factor($user): bool {
        // This factor doesn't require validation as it's automatic.
        return true;
    }

    /**
     * Get the display name for this factor.
     *
     * @return string The display name
     */
    public function get_display_name(): string {
        return get_string('pluginname', 'factor_domain');
    }

    /**
     * Check if setup buttons should be shown.
     *
     * @return bool Always false for domain factor
     */
    public function show_setup_buttons(): bool {
        return false;
    }

    /**
     * Get information about this factor.
     *
     * @return string The info text
     */
    public function get_info(): string {
        return get_string('info', 'factor_domain');
    }

    /**
     * Function to revoke the factor for a user
     *
     * @param int $factorid The factor ID to revoke
     * @return bool Always true as this factor cannot be revoked
     */
    public function revoke_user_factor(int $factorid = null): bool {
        // This factor cannot be revoked as it's domain-based.
        return true;
    }

    /**
     * Gets the string for setup button on preferences page.
     *
     * @return string Empty string as no setup required
     */
    public function get_setup_string(): string {
        return '';
    }

    /**
     * Returns true if factor class has factor records that might be revoked.
     *
     * @return bool Always false as domain factor doesn't store user records
     */
    public function has_revoke(): bool {
        return false;
    }

    /**
     * Check if this factor is available for a user.
     *
     * @param \stdClass $user The user to check availability for
     * @return bool True if available, false otherwise
     */
    public function is_available($user): bool {
        return $this->is_enabled();
    }

    /**
     * Gets the points this factor contributes when passed.
     *
     * @return int The points value (weight if passed, 0 if neutral)
     */
    public function get_points(): int {
        $state = $this->get_state();
        if ($state === \tool_mfa\plugininfo\factor::STATE_PASS) {
            return $this->get_weight();
        }
        return 0;
    }

    /**
     * Defines factor data to be saved to the database.
     *
     * @param \stdClass $user The user to define factor data for
     * @return \stdClass|null Always null as no user-specific data needed
     */
    public function define_factor_data($user): ?\stdClass {
        // Domain factor doesn't need to store user-specific data.
        return null;
    }

    /**
     * Returns condition for passing this factor.
     *
     * @return string The condition description
     */
    public function get_condition(): string {
        $weight = $this->get_weight();
        $domains = get_config('factor_domain', 'alloweddomains') ?? '';
        $domainlist = array_filter(array_map('trim', explode("\n", $domains)));
        $domaincount = count($domainlist);

        return get_string('summarycondition', 'factor_domain') . " (Weight: {$weight}, Domains: {$domaincount})";
    }
}
