-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 20, 2026 at 10:38 AM
-- Server version: 8.0.45-0ubuntu0.22.04.1
-- PHP Version: 8.4.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `subscription_management`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `calculate_prorated_amount` (IN `p_old_amount` DECIMAL(20,8), IN `p_new_amount` DECIMAL(20,8), IN `p_days_used` INT, IN `p_total_days` INT, OUT `p_prorated_amount` DECIMAL(20,8))  BEGIN
    DECLARE v_unused_amount DECIMAL(20,8);
    DECLARE v_prorated_new DECIMAL(20,8);

    SET v_unused_amount = p_old_amount * (p_total_days - p_days_used) / p_total_days;
    SET v_prorated_new = p_new_amount * p_days_used / p_total_days;

    SET p_prorated_amount = v_prorated_new - v_unused_amount;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `process_subscription_renewal` (IN `p_subscription_id` INT UNSIGNED)  BEGIN
    DECLARE v_user_id INT UNSIGNED;
    DECLARE v_plan_id INT UNSIGNED;
    DECLARE v_amount DECIMAL(20,8);
    DECLARE v_currency VARCHAR(3);
    DECLARE v_next_period_start TIMESTAMP;
    DECLARE v_next_period_end TIMESTAMP;

    -- Get subscription details
    SELECT user_id, plan_id, amount, currency, current_period_ends_at
    INTO v_user_id, v_plan_id, v_amount, v_currency, v_next_period_start
    FROM subscriptions
    WHERE id = p_subscription_id AND status = 'active';

    -- Calculate next period
    SET v_next_period_end = DATE_ADD(v_next_period_start, INTERVAL 1 MONTH);

    -- Create invoice
    INSERT INTO invoices (
        user_id,
        subscription_id,
        number,
        type,
        status,
        subtotal,
        total,
        amount_due,
        currency,
        issue_date,
        due_date,
        line_items
    ) VALUES (
        v_user_id,
        p_subscription_id,
        CONCAT('INV-', DATE_FORMAT(NOW(), '%Y%m'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0')),
        'subscription',
        'open',
        v_amount,
        v_amount,
        v_amount,
        v_currency,
        NOW(),
        v_next_period_start,
        JSON_ARRAY(
            JSON_OBJECT(
                'description', 'Subscription renewal',
                'amount', v_amount,
                'quantity', 1
            )
        )
    );

    -- Update subscription period
    UPDATE subscriptions
    SET
        current_period_starts_at = v_next_period_start,
        current_period_ends_at = v_next_period_end,
        updated_at = NOW()
    WHERE id = p_subscription_id;

    -- Log event
    INSERT INTO subscription_events (
        subscription_id,
        type,
        occurred_at
    ) VALUES (
        p_subscription_id,
        'invoice_created',
        NOW()
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `record_usage_with_limit_check` (IN `p_subscription_id` INT UNSIGNED, IN `p_feature_code` VARCHAR(100), IN `p_quantity` DECIMAL(20,8), IN `p_unit` VARCHAR(50))  BEGIN
    DECLARE v_feature_id INT UNSIGNED;
    DECLARE v_subscription_item_id INT UNSIGNED;
    DECLARE v_limit_value VARCHAR(255);
    DECLARE v_current_usage DECIMAL(20,8);
    DECLARE v_has_exceeded BOOLEAN;

    -- Get feature ID
    SELECT id INTO v_feature_id FROM features WHERE code = p_feature_code;

    -- Get subscription item
    SELECT id INTO v_subscription_item_id
    FROM subscription_items
    WHERE subscription_id = p_subscription_id AND feature_id = v_feature_id;

    -- Get limit value
    SELECT pf.value INTO v_limit_value
    FROM subscriptions s
    JOIN plan_features pf ON s.plan_id = pf.plan_id
    WHERE s.id = p_subscription_id AND pf.feature_id = v_feature_id;

    -- Get current usage for the period
    SELECT COALESCE(SUM(quantity), 0) INTO v_current_usage
    FROM usage_records
    WHERE subscription_id = p_subscription_id
        AND feature_id = v_feature_id
        AND billing_date >= DATE_SUB(CURDATE(), INTERVAL DAY(CURDATE())-1 DAY)
        AND billing_date <= LAST_DAY(CURDATE());

    -- Check if limit is exceeded
    IF v_limit_value != 'unlimited' AND (v_current_usage + p_quantity) > CAST(v_limit_value AS DECIMAL(20,8)) THEN
        SET v_has_exceeded = TRUE;

        -- Log warning
        INSERT INTO subscription_events (
            subscription_id,
            type,
            data,
            occurred_at
        ) VALUES (
            p_subscription_id,
            'usage_recorded',
            JSON_OBJECT(
                'feature', p_feature_code,
                'quantity', p_quantity,
                'current_usage', v_current_usage,
                'limit', v_limit_value,
                'has_exceeded', TRUE
            ),
            NOW()
        );

        -- Return exceeded status
        SELECT FALSE AS can_proceed, v_has_exceeded AS has_exceeded;
    ELSE
        -- Record usage
        INSERT INTO usage_records (
            subscription_id,
            subscription_item_id,
            feature_id,
            quantity,
            unit,
            billing_date,
            recorded_at
        ) VALUES (
            p_subscription_id,
            v_subscription_item_id,
            v_feature_id,
            p_quantity,
            p_unit,
            CURDATE(),
            NOW()
        );

        SELECT TRUE AS can_proceed, FALSE AS has_exceeded;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_subscription_status` (IN `p_subscription_id` INT UNSIGNED, IN `p_new_status` VARCHAR(50), IN `p_causer_id` INT UNSIGNED, IN `p_causer_type` VARCHAR(100))  BEGIN
    DECLARE v_old_status VARCHAR(50);

    -- Get current status
    SELECT status INTO v_old_status FROM subscriptions WHERE id = p_subscription_id;

    -- Update subscription
    UPDATE subscriptions
    SET status = p_new_status,
        updated_at = NOW(),
        updated_by = p_causer_id
    WHERE id = p_subscription_id;

    -- Log event
    INSERT INTO subscription_events (
        subscription_id,
        type,
        changes,
        causer_id,
        causer_type,
        occurred_at
    ) VALUES (
        p_subscription_id,
        'updated',
        JSON_OBJECT('status', JSON_ARRAY(v_old_status, p_new_status)),
        p_causer_id,
        p_causer_type,
        NOW()
    );
END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `calculate_subscription_end_date` (`p_start_date` TIMESTAMP, `p_billing_period` VARCHAR(20), `p_interval_count` INT) RETURNS TIMESTAMP BEGIN
    DECLARE v_end_date TIMESTAMP;

    CASE p_billing_period
        WHEN 'daily' THEN
            SET v_end_date = DATE_ADD(p_start_date, INTERVAL p_interval_count DAY);
        WHEN 'weekly' THEN
            SET v_end_date = DATE_ADD(p_start_date, INTERVAL p_interval_count WEEK);
        WHEN 'monthly' THEN
            SET v_end_date = DATE_ADD(p_start_date, INTERVAL p_interval_count MONTH);
        WHEN 'quarterly' THEN
            SET v_end_date = DATE_ADD(p_start_date, INTERVAL (p_interval_count * 3) MONTH);
        WHEN 'yearly' THEN
            SET v_end_date = DATE_ADD(p_start_date, INTERVAL p_interval_count YEAR);
        ELSE
            SET v_end_date = DATE_ADD(p_start_date, INTERVAL 1 MONTH);
    END CASE;

    RETURN v_end_date;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `can_use_feature` (`p_user_id` INT UNSIGNED, `p_feature_code` VARCHAR(100)) RETURNS TINYINT(1) READS SQL DATA
BEGIN
    DECLARE v_feature_id INT UNSIGNED;
    DECLARE v_subscription_id INT UNSIGNED;
    DECLARE v_limit_value VARCHAR(255);
    DECLARE v_current_usage DECIMAL(20,8);
    DECLARE v_has_feature BOOLEAN;

    -- Get feature ID
    SELECT id INTO v_feature_id FROM features WHERE code = p_feature_code;

    -- Get active subscription
    SELECT id INTO v_subscription_id
    FROM subscriptions
    WHERE user_id = p_user_id AND status IN ('active', 'trialing')
    LIMIT 1;

    IF v_subscription_id IS NULL THEN
        RETURN FALSE;
    END IF;

    -- Check if feature exists in plan
    SELECT COUNT(*) > 0 INTO v_has_feature
    FROM plan_features pf
    JOIN subscriptions s ON pf.plan_id = s.plan_id
    WHERE s.id = v_subscription_id AND pf.feature_id = v_feature_id;

    IF NOT v_has_feature THEN
        RETURN FALSE;
    END IF;

    -- Get limit value
    SELECT pf.value INTO v_limit_value
    FROM plan_features pf
    JOIN subscriptions s ON pf.plan_id = s.plan_id
    WHERE s.id = v_subscription_id AND pf.feature_id = v_feature_id;

    IF v_limit_value = 'unlimited' THEN
        RETURN TRUE;
    END IF;

    -- Get current usage
    SELECT COALESCE(SUM(quantity), 0) INTO v_current_usage
    FROM usage_records
    WHERE subscription_id = v_subscription_id
        AND feature_id = v_feature_id
        AND billing_date >= DATE_SUB(CURDATE(), INTERVAL DAY(CURDATE())-1 DAY)
        AND billing_date <= LAST_DAY(CURDATE());

    -- Check if usage is within limit
    RETURN v_current_usage < CAST(v_limit_value AS DECIMAL(20,8));
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
-- Comment: Stores user information with subscription preferences
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'personal' COMMENT 'Enum values: personal, business, enterprise',
  `tax_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_tax_exempt` tinyint(1) NOT NULL DEFAULT '0',
  `tax_certificate` json DEFAULT NULL,
  `billing_address` json DEFAULT NULL,
  `shipping_address` json DEFAULT NULL,
  `preferred_currency` char(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `preferred_payment_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auto_renew` tinyint(1) NOT NULL DEFAULT '1',
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `has_used_trial` tinyint(1) NOT NULL DEFAULT '0',
  `account_status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active' COMMENT 'Enum values: active, suspended, closed, fraudulent',
  `account_status_reason` text COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL,
  `preferences` json DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  KEY `user_email_status_idx` (`email`,`account_status`),
  KEY `user_trial_status_idx` (`trial_ends_at`,`account_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores user information with subscription preferences';

-- --------------------------------------------------------

--
-- Table structure for table `features`
-- Comment: Available features that can be included in plans
--

CREATE TABLE `features` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'limit' COMMENT 'Enum values: limit, boolean, tiered',
  `scope` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'global' COMMENT 'Enum values: global, per_user, per_seat, per_team',
  `is_resettable` tinyint(1) NOT NULL DEFAULT '0',
  `reset_period` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'monthly' COMMENT 'Enum values: monthly, yearly, weekly, never',
  `metadata` json DEFAULT NULL,
  `validations` json DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code_UNIQUE` (`code`),
  KEY `feature_code_type_idx` (`code`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Available features that can be included in plans';

-- --------------------------------------------------------

--
-- Table structure for table `plans`
-- Comment: Subscription plans with pricing and features
--

CREATE TABLE `plans` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'recurring' COMMENT 'Enum values: recurring, usage, one_time, hybrid',
  `billing_period` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'monthly' COMMENT 'Enum values: monthly, yearly, quarterly, weekly, daily',
  `billing_interval` int NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_visible` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `metadata` json DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug_UNIQUE` (`slug`),
  UNIQUE KEY `code_UNIQUE` (`code`),
  KEY `plan_type_active_idx` (`type`,`is_active`,`is_visible`),
  KEY `plan_display_idx` (`sort_order`,`is_featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Subscription plans with pricing and features';

-- --------------------------------------------------------

--
-- Table structure for table `plan_features`
-- Comment: Features included in each plan with limits/config
--

CREATE TABLE `plan_features` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `plan_id` int UNSIGNED NOT NULL,
  `feature_id` int UNSIGNED NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `config` json DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_inherited` tinyint(1) NOT NULL DEFAULT '0',
  `parent_feature_id` int UNSIGNED DEFAULT NULL,
  `effective_from` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `effective_to` timestamp NULL DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plan_feature_unique` (`plan_id`,`feature_id`) COMMENT 'WHERE effective_to IS NULL',
  KEY `feature_value_idx` (`feature_id`,`value`),
  CONSTRAINT `fk_plan_features_features` FOREIGN KEY (`feature_id`) REFERENCES `features` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_plan_features_plans` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Features included in each plan with limits/config';

-- --------------------------------------------------------

--
-- Table structure for table `plan_prices`
-- Comment: Pricing details for plans in different currencies
--

CREATE TABLE `plan_prices` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `plan_id` int UNSIGNED NOT NULL,
  `currency` char(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `amount` decimal(20,8) NOT NULL,
  `interval` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Enum values: month, year, quarter, week, day',
  `interval_count` int NOT NULL DEFAULT '1',
  `usage_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'licensed' COMMENT 'Enum values: licensed, metered, tiered',
  `tiers` json DEFAULT NULL,
  `transformations` json DEFAULT NULL,
  `stripe_price_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_from` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active_to` timestamp NULL DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plan_price_unique` (`plan_id`,`currency`,`interval`,`interval_count`),
  UNIQUE KEY `stripe_price_id_UNIQUE` (`stripe_price_id`),
  KEY `active_price_idx` (`plan_id`,`currency`,`interval`) COMMENT 'WHERE active_to IS NULL',
  CONSTRAINT `fk_plan_prices_plans` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Pricing details for plans in different currencies';

-- --------------------------------------------------------

--
-- Table structure for table `payment_gateways`
-- Comment: Configuration for payment gateways
--

CREATE TABLE `payment_gateways` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Enum values: card, wallet, bank, crypto, aggregator, cash',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `is_test_mode` tinyint(1) NOT NULL DEFAULT '1',
  `supports_recurring` tinyint(1) NOT NULL DEFAULT '0',
  `supports_refunds` tinyint(1) NOT NULL DEFAULT '0',
  `supports_installments` tinyint(1) NOT NULL DEFAULT '0',
  `api_key` text COLLATE utf8mb4_unicode_ci,
  `api_secret` text COLLATE utf8mb4_unicode_ci,
  `webhook_secret` text COLLATE utf8mb4_unicode_ci,
  `merchant_id` text COLLATE utf8mb4_unicode_ci,
  `store_id` text COLLATE utf8mb4_unicode_ci,
  `store_password` text COLLATE utf8mb4_unicode_ci,
  `base_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `callback_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `webhook_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supported_currencies` json DEFAULT NULL,
  `supported_countries` json DEFAULT NULL,
  `excluded_countries` json DEFAULT NULL,
  `percentage_fee` decimal(5,2) NOT NULL DEFAULT '0.00',
  `fixed_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fee_currency` char(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `fee_structure` json DEFAULT NULL,
  `config` json DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `settlement_days` int NOT NULL DEFAULT '2',
  `refund_days` int NOT NULL DEFAULT '5',
  `min_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `max_amount` decimal(20,2) NOT NULL DEFAULT '999999.00',
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code_UNIQUE` (`code`),
  KEY `active_gateways_idx` (`code`,`is_active`,`is_test_mode`),
  KEY `gateway_type_idx` (`type`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configuration for payment gateways';

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
-- Comment: User subscriptions to plans
--

CREATE TABLE `subscriptions` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `plan_id` int UNSIGNED NOT NULL,
  `plan_price_id` int UNSIGNED NOT NULL,
  `parent_subscription_id` int UNSIGNED DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'trialing' COMMENT 'Enum values: active, trialing, past_due, canceled, unpaid, incomplete, incomplete_expired, paused, suspended',
  `billing_cycle_anchor` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'creation' COMMENT 'Enum values: creation, billing_cycle',
  `quantity` int NOT NULL DEFAULT '1',
  `unit_price` decimal(20,8) NOT NULL,
  `amount` decimal(20,8) NOT NULL,
  `currency` char(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `trial_starts_at` timestamp NULL DEFAULT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `trial_converted` tinyint(1) NOT NULL DEFAULT '0',
  `current_period_starts_at` timestamp NULL DEFAULT NULL,
  `current_period_ends_at` timestamp NULL DEFAULT NULL,
  `billing_cycle_anchor_date` timestamp NULL DEFAULT NULL,
  `canceled_at` timestamp NULL DEFAULT NULL,
  `cancellation_reason` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Enum values: customer, payment_failed, fraud, business, upgrade, downgrade, other',
  `prorate` tinyint(1) NOT NULL DEFAULT '1',
  `proration_amount` decimal(20,8) DEFAULT NULL,
  `proration_date` timestamp NULL DEFAULT NULL,
  `gateway` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'stripe',
  `gateway_subscription_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_customer_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_metadata` json DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `history` json DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_active_subscription_idx` (`user_id`,`status`,`current_period_ends_at`),
  KEY `expiring_subscriptions_idx` (`status`,`current_period_ends_at`),
  KEY `gateway_subscription_lookup` (`gateway`,`gateway_subscription_id`),
  KEY `child_subscriptions_idx` (`parent_subscription_id`,`status`),
  KEY `fk_subscriptions_plans` (`plan_id`),
  KEY `fk_subscriptions_plan_prices` (`plan_price_id`),
  KEY `idx_subscriptions_user_status` (`user_id`,`status`,`current_period_ends_at`),
  CONSTRAINT `fk_subscriptions_plan_prices` FOREIGN KEY (`plan_price_id`) REFERENCES `plan_prices` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_subscriptions_plans` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_subscriptions_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User subscriptions to plans';

--
-- Triggers `subscriptions`
--
DELIMITER $$
CREATE TRIGGER `log_subscription_status_change` AFTER UPDATE ON `subscriptions` FOR EACH ROW BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO subscription_events (
            subscription_id,
            type,
            data,
            changes,
            occurred_at
        ) VALUES (
            NEW.id,
            'updated',
            JSON_OBJECT(
                'new_status', NEW.status,
                'old_status', OLD.status
            ),
            JSON_OBJECT(
                'status', JSON_ARRAY(OLD.status, NEW.status)
            ),
            NOW()
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `subscription_items`
-- Comment: Individual items within a subscription
--

CREATE TABLE `subscription_items` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `subscription_id` int UNSIGNED NOT NULL,
  `plan_price_id` int UNSIGNED NOT NULL,
  `feature_id` int UNSIGNED NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `unit_price` decimal(20,8) NOT NULL,
  `amount` decimal(20,8) NOT NULL,
  `metadata` json DEFAULT NULL,
  `tiers` json DEFAULT NULL,
  `effective_from` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `effective_to` timestamp NULL DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscription_item_unique` (`subscription_id`,`plan_price_id`,`feature_id`) COMMENT 'WHERE effective_to IS NULL',
  KEY `subscription_pricing_idx` (`subscription_id`,`amount`),
  KEY `fk_subscription_items_plan_prices` (`plan_price_id`),
  KEY `fk_subscription_items_features` (`feature_id`),
  CONSTRAINT `fk_subscription_items_features` FOREIGN KEY (`feature_id`) REFERENCES `features` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_subscription_items_plan_prices` FOREIGN KEY (`plan_price_id`) REFERENCES `plan_prices` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_subscription_items_subscriptions` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Individual items within a subscription';

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
-- Comment: Billing invoices
--

CREATE TABLE `invoices` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `subscription_id` int UNSIGNED DEFAULT NULL,
  `number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `external_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Enum values: subscription, one_time, credit, adjustment',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Enum values: draft, open, paid, void, uncollectible, refunded',
  `subtotal` decimal(20,8) NOT NULL,
  `tax` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `total` decimal(20,8) NOT NULL,
  `amount_due` decimal(20,8) NOT NULL,
  `amount_paid` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `amount_remaining` decimal(20,8) GENERATED ALWAYS AS ((`total` - `amount_paid`)) STORED,
  `currency` char(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `issue_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `due_date` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `finalized_at` timestamp NULL DEFAULT NULL,
  `line_items` json DEFAULT NULL,
  `tax_rates` json DEFAULT NULL,
  `discounts` json DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `history` json DEFAULT NULL,
  `pdf_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `number_UNIQUE` (`number`),
  UNIQUE KEY `external_id_UNIQUE` (`external_id`),
  KEY `user_invoice_status_idx` (`user_id`,`status`,`due_date`),
  KEY `subscription_invoices_idx` (`subscription_id`,`issue_date`),
  KEY `external_invoice_idx` (`external_id`),
  KEY `invoice_analytics_idx` (`type`,`status`,`issue_date`),
  KEY `idx_invoices_user_due` (`user_id`,`due_date`,`status`),
  CONSTRAINT `fk_invoices_subscriptions` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_invoices_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Billing invoices';

-- --------------------------------------------------------

--
-- Table structure for table `payment_masters`
-- Comment: Master payment records (can contain multiple items)
--

CREATE TABLE `payment_masters` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `payment_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Enum values: subscription, order, wallet_topup, refund, adjustment, bulk',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft' COMMENT 'Enum values: draft, pending, processing, partially_paid, paid, failed, refunded, disputed, cancelled, expired',
  `total_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `subtotal` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `tax_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `discount_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `fee_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `net_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `paid_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `due_amount` decimal(20,8) GENERATED ALWAYS AS ((`total_amount` - `paid_amount`)) STORED,
  `currency` char(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `exchange_rate` decimal(12,6) NOT NULL DEFAULT '1.000000',
  `base_currency` char(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `base_amount` decimal(20,8) GENERATED ALWAYS AS ((`total_amount` * `exchange_rate`)) STORED,
  `payment_method` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Enum values: cash, bank_transfer, stripe, paypal, sslcommerz, card, bkash, nagad, rocket, google_pay, apple_pay, crypto, wallet, cheque, installment, custom',
  `payment_method_details` json DEFAULT NULL,
  `payment_gateway` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_installment` tinyint(1) NOT NULL DEFAULT '0',
  `installment_count` int DEFAULT NULL,
  `installment_frequency` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_date` timestamp NULL DEFAULT NULL,
  `due_date` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `customer_reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `custom_fields` json DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `failure_reason` text COLLATE utf8mb4_unicode_ci,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_number_UNIQUE` (`payment_number`),
  KEY `user_payment_status_idx` (`user_id`,`status`,`payment_date`),
  KEY `payment_lookup_idx` (`payment_number`,`type`),
  KEY `payment_method_analytics_idx` (`payment_method`,`status`,`payment_date`),
  KEY `pending_payments_idx` (`due_date`,`status`),
  KEY `idx_payment_masters_user_date` (`user_id`,`payment_date`,`status`),
  FULLTEXT KEY `payment_search_ft_idx` (`payment_number`,`customer_reference`,`notes`),
  CONSTRAINT `fk_payment_masters_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Master payment records (can contain multiple items)';

-- --------------------------------------------------------

--
-- Table structure for table `payment_children`
-- Comment: Line items within a payment master
--

CREATE TABLE `payment_children` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `payment_master_id` int UNSIGNED NOT NULL,
  `item_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_id` int UNSIGNED NOT NULL,
  `subscription_id` int UNSIGNED DEFAULT NULL,
  `plan_id` int UNSIGNED DEFAULT NULL,
  `invoice_id` int UNSIGNED DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_code` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `quantity` int NOT NULL DEFAULT '1',
  `amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `tax_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `discount_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `total_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `period_start` date DEFAULT NULL,
  `period_end` date DEFAULT NULL,
  `billing_cycle` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'Enum values: pending, paid, refunded, cancelled, failed',
  `paid_at` timestamp NULL DEFAULT NULL,
  `allocated_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `is_fully_allocated` tinyint(1) GENERATED ALWAYS AS ((`allocated_amount` >= `total_amount`)) STORED,
  `metadata` json DEFAULT NULL,
  `tax_breakdown` json DEFAULT NULL,
  `discount_breakdown` json DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_item_lookup_idx` (`payment_master_id`,`item_type`,`item_id`),
  KEY `subscription_payments_idx` (`subscription_id`,`status`),
  KEY `invoice_payments_idx` (`invoice_id`,`status`),
  KEY `item_payment_status_idx` (`item_type`,`item_id`,`status`),
  KEY `fk_payment_children_plans` (`plan_id`),
  CONSTRAINT `fk_payment_children_invoices` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_payment_children_payment_masters` FOREIGN KEY (`payment_master_id`) REFERENCES `payment_masters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_payment_children_plans` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_payment_children_subscriptions` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Line items within a payment master';

--
-- Triggers `payment_children`
--
DELIMITER $$
CREATE TRIGGER `update_payment_master_status` AFTER UPDATE ON `payment_children` FOR EACH ROW BEGIN
    DECLARE v_total_amount DECIMAL(20,8);
    DECLARE v_paid_amount DECIMAL(20,8);
    DECLARE v_new_status VARCHAR(50);

    -- Calculate totals
    SELECT
        SUM(total_amount),
        SUM(CASE WHEN status = 'paid' THEN total_amount ELSE 0 END)
    INTO v_total_amount, v_paid_amount
    FROM payment_children
    WHERE payment_master_id = NEW.payment_master_id;

    -- Determine new status
    IF v_paid_amount = 0 THEN
        SET v_new_status = 'pending';
    ELSEIF v_paid_amount < v_total_amount THEN
        SET v_new_status = 'partially_paid';
    ELSEIF v_paid_amount = v_total_amount THEN
        SET v_new_status = 'paid';
    END IF;

    -- Update payment master
    UPDATE payment_masters
    SET
        status = v_new_status,
        paid_amount = v_paid_amount,
        updated_at = NOW()
    WHERE id = NEW.payment_master_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `payment_transactions`
-- Comment: Individual payment transactions
--

CREATE TABLE `payment_transactions` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `payment_master_id` int UNSIGNED NOT NULL,
  `payment_child_id` int UNSIGNED DEFAULT NULL,
  `transaction_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'payment' COMMENT 'Enum values: payment, refund, chargeback, dispute, adjustment, reversal, settlement',
  `payment_method` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Enum values: cash, bank_transfer, stripe, paypal, sslcommerz, card, bkash, nagad, rocket, google_pay, apple_pay, crypto, wallet, cheque, installment',
  `payment_gateway` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gateway_response` json DEFAULT NULL,
  `payment_method_details` json DEFAULT NULL,
  `amount` decimal(20,8) NOT NULL,
  `fee` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `tax` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `net_amount` decimal(20,8) GENERATED ALWAYS AS ((`amount` - `fee`)) STORED,
  `currency` char(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `exchange_rate` decimal(12,6) NOT NULL DEFAULT '1.000000',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'initiated' COMMENT 'Enum values: initiated, authorized, captured, pending, completed, failed, refunded, charged_back, disputed, cancelled, expired',
  `card_last4` char(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `card_brand` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `card_country` char(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `card_exp_month` int DEFAULT NULL,
  `card_exp_year` int DEFAULT NULL,
  `bank_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_last4` char(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_routing_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wallet_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wallet_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wallet_transaction_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `installment_number` int DEFAULT NULL,
  `total_installments` int DEFAULT NULL,
  `initiated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `authorized_at` timestamp NULL DEFAULT NULL,
  `captured_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `failed_at` timestamp NULL DEFAULT NULL,
  `refunded_at` timestamp NULL DEFAULT NULL,
  `fraud_indicators` json DEFAULT NULL,
  `risk_score` decimal(5,2) DEFAULT NULL,
  `requires_review` tinyint(1) NOT NULL DEFAULT '0',
  `metadata` json DEFAULT NULL,
  `custom_fields` json DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `failure_reason` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `location_data` json DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaction_id_UNIQUE` (`transaction_id`),
  KEY `master_transaction_status_idx` (`payment_master_id`,`status`),
  KEY `transaction_lookup_idx` (`transaction_id`,`reference_id`),
  KEY `payment_method_stats_idx` (`payment_method`,`status`,`completed_at`),
  KEY `gateway_stats_idx` (`payment_gateway`,`status`,`completed_at`),
  KEY `card_analytics_idx` (`card_brand`,`status`),
  KEY `revenue_analytics_idx` (`completed_at`,`amount`),
  KEY `fk_payment_transactions_payment_children` (`payment_child_id`),
  CONSTRAINT `fk_payment_transactions_payment_children` FOREIGN KEY (`payment_child_id`) REFERENCES `payment_children` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_payment_transactions_payment_masters` FOREIGN KEY (`payment_master_id`) REFERENCES `payment_masters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Individual payment transactions';

-- --------------------------------------------------------

--
-- Table structure for table `payments`
-- Comment: Payment records for invoices
--

CREATE TABLE `payments` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id` int UNSIGNED DEFAULT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `external_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Enum values: card, bank, wallet, crypto, cash, credit',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Enum values: pending, processing, completed, failed, refunded, disputed',
  `amount` decimal(20,8) NOT NULL,
  `fee` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `net` decimal(20,8) GENERATED ALWAYS AS ((`amount` - `fee`)) STORED,
  `currency` char(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `gateway` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gateway_response` json DEFAULT NULL,
  `payment_method` json DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `refunded_at` timestamp NULL DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `fraud_indicators` json DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `external_id_UNIQUE` (`external_id`),
  KEY `user_payments_idx` (`user_id`,`status`,`processed_at`),
  KEY `gateway_payments_idx` (`gateway`,`external_id`),
  KEY `payment_analytics_idx` (`type`,`status`,`processed_at`),
  KEY `fk_payments_invoices` (`invoice_id`),
  CONSTRAINT `fk_payments_invoices` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_payments_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Payment records for invoices';

-- --------------------------------------------------------

--
-- Table structure for table `payment_allocations`
-- Comment: Allocation of payments to specific items
--

CREATE TABLE `payment_allocations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `payment_master_id` int UNSIGNED NOT NULL,
  `payment_child_id` int UNSIGNED NOT NULL,
  `payment_transaction_id` int UNSIGNED NOT NULL,
  `allocatable_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `allocatable_id` int UNSIGNED NOT NULL,
  `amount` decimal(20,8) NOT NULL,
  `base_amount` decimal(20,8) GENERATED ALWAYS AS ((`amount` * `exchange_rate`)) STORED,
  `exchange_rate` decimal(12,6) NOT NULL DEFAULT '1.000000',
  `currency` char(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `allocation_reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `allocation_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'payment' COMMENT 'Enum values: payment, refund, credit, adjustment',
  `is_reversed` tinyint(1) NOT NULL DEFAULT '0',
  `reversed_at` timestamp NULL DEFAULT NULL,
  `reversal_id` int UNSIGNED DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_allocation` (`payment_transaction_id`,`allocatable_type`,`allocatable_id`),
  KEY `master_allocation_idx` (`payment_master_id`,`allocatable_type`,`allocatable_id`),
  KEY `item_allocations_idx` (`allocatable_type`,`allocatable_id`,`is_reversed`),
  KEY `fk_payment_allocations_payment_children` (`payment_child_id`),
  CONSTRAINT `fk_payment_allocations_payment_children` FOREIGN KEY (`payment_child_id`) REFERENCES `payment_children` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_payment_allocations_payment_masters` FOREIGN KEY (`payment_master_id`) REFERENCES `payment_masters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_payment_allocations_payment_transactions` FOREIGN KEY (`payment_transaction_id`) REFERENCES `payment_transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Allocation of payments to specific items';

-- --------------------------------------------------------

--
-- Table structure for table `refunds`
-- Comment: Refund records
--

CREATE TABLE `refunds` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `payment_master_id` int UNSIGNED NOT NULL,
  `payment_transaction_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `refund_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Enum values: full, partial, chargeback, dispute',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'requested' COMMENT 'Enum values: requested, approved, processing, completed, failed, rejected',
  `initiated_by` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer' COMMENT 'Enum values: customer, merchant, gateway, system',
  `amount` decimal(20,8) NOT NULL,
  `fee` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `net_amount` decimal(20,8) GENERATED ALWAYS AS ((`amount` - `fee`)) STORED,
  `currency` char(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `exchange_rate` decimal(12,6) NOT NULL DEFAULT '1.000000',
  `reason` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other' COMMENT 'Enum values: duplicate, fraudulent, requested_by_customer, credit_not_processed, goods_not_received, goods_defective, subscription_cancelled, other',
  `reason_details` text COLLATE utf8mb4_unicode_ci,
  `customer_comments` text COLLATE utf8mb4_unicode_ci,
  `requested_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` int UNSIGNED DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `failed_at` timestamp NULL DEFAULT NULL,
  `gateway_refund_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_response` json DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `documents` json DEFAULT NULL,
  `processed_by` int UNSIGNED DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `refund_number_UNIQUE` (`refund_number`),
  KEY `user_refunds_idx` (`user_id`,`status`,`requested_at`),
  KEY `payment_refunds_idx` (`payment_master_id`,`status`),
  KEY `fk_refunds_payment_transactions` (`payment_transaction_id`),
  CONSTRAINT `fk_refunds_payment_masters` FOREIGN KEY (`payment_master_id`) REFERENCES `payment_masters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_refunds_payment_transactions` FOREIGN KEY (`payment_transaction_id`) REFERENCES `payment_transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_refunds_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Refund records';

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
-- Comment: User's saved payment methods
--

CREATE TABLE `payment_methods` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Enum values: card, bank_account, digital_wallet, crypto_wallet, cash, custom',
  `gateway` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gateway_customer_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_payment_method_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nickname` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `card_last4` char(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `card_brand` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `card_exp_month` int DEFAULT NULL,
  `card_exp_year` int DEFAULT NULL,
  `card_country` char(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_last4` char(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_routing_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wallet_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wallet_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `crypto_currency` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `crypto_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `encrypted_data` json DEFAULT NULL,
  `fingerprint` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_compromised` tinyint(1) NOT NULL DEFAULT '0',
  `metadata` json DEFAULT NULL,
  `gateway_metadata` json DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `verified_by` int UNSIGNED DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `usage_count` int NOT NULL DEFAULT '0',
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_payment_method` (`user_id`,`fingerprint`),
  KEY `user_payment_methods_idx` (`user_id`,`type`,`is_default`),
  KEY `gateway_method_lookup_idx` (`gateway`,`gateway_payment_method_id`),
  CONSTRAINT `fk_payment_methods_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User''s saved payment methods';

-- --------------------------------------------------------

--
-- Table structure for table `payment_webhook_logs`
-- Comment: Logs of payment gateway webhooks
--

CREATE TABLE `payment_webhook_logs` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `payment_gateway_id` int UNSIGNED DEFAULT NULL,
  `gateway` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `webhook_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_transaction_id` int UNSIGNED DEFAULT NULL,
  `payload` json DEFAULT NULL,
  `headers` json DEFAULT NULL,
  `response_code` int DEFAULT NULL,
  `response_body` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'received' COMMENT 'Enum values: received, processing, processed, failed, ignored',
  `processing_error` text COLLATE utf8mb4_unicode_ci,
  `retry_count` int NOT NULL DEFAULT '0',
  `next_retry_at` timestamp NULL DEFAULT NULL,
  `received_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `processed_at` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `verification_error` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `webhook_events_idx` (`gateway`,`event_type`,`received_at`),
  KEY `webhook_reference_idx` (`reference_id`,`gateway`),
  KEY `pending_webhooks_idx` (`status`,`next_retry_at`),
  KEY `fk_payment_webhook_logs_payment_gateways` (`payment_gateway_id`),
  KEY `fk_payment_webhook_logs_payment_transactions` (`payment_transaction_id`),
  CONSTRAINT `fk_payment_webhook_logs_payment_gateways` FOREIGN KEY (`payment_gateway_id`) REFERENCES `payment_gateways` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_payment_webhook_logs_payment_transactions` FOREIGN KEY (`payment_transaction_id`) REFERENCES `payment_transactions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Logs of payment gateway webhooks';

-- --------------------------------------------------------

--
-- Table structure for table `usage_records`
-- Comment: Records of feature usage for metered billing
--

CREATE TABLE `usage_records` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `subscription_id` int UNSIGNED NOT NULL,
  `subscription_item_id` int UNSIGNED NOT NULL,
  `feature_id` int UNSIGNED NOT NULL,
  `quantity` decimal(20,8) NOT NULL,
  `tier_quantity` decimal(20,8) DEFAULT NULL,
  `amount` decimal(20,8) DEFAULT NULL,
  `unit` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'Enum values: pending, billed, void, disputed',
  `recorded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `billing_date` date NOT NULL,
  `metadata` json DEFAULT NULL,
  `dimensions` json DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscription_usage_idx` (`subscription_id`,`billing_date`,`feature_id`),
  KEY `billing_aggregation_idx` (`billing_date`,`feature_id`,`status`),
  KEY `record_timestamp_idx` (`recorded_at`),
  KEY `fk_usage_records_subscription_items` (`subscription_item_id`),
  KEY `fk_usage_records_features` (`feature_id`),
  KEY `idx_usage_records_subscription_feature` (`subscription_id`,`feature_id`,`billing_date`),
  CONSTRAINT `fk_usage_records_features` FOREIGN KEY (`feature_id`) REFERENCES `features` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_usage_records_subscription_items` FOREIGN KEY (`subscription_item_id`) REFERENCES `subscription_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_usage_records_subscriptions` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Records of feature usage for metered billing';

-- --------------------------------------------------------

--
-- Table structure for table `metered_usage_aggregates`
-- Comment: Pre-aggregated usage data for performance
--

CREATE TABLE `metered_usage_aggregates` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `subscription_id` int UNSIGNED NOT NULL,
  `feature_id` int UNSIGNED NOT NULL,
  `aggregate_date` date NOT NULL,
  `aggregate_period` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Enum values: daily, weekly, monthly, yearly',
  `total_quantity` decimal(20,8) NOT NULL,
  `tier1_quantity` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `tier2_quantity` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `tier3_quantity` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `total_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `record_count` int NOT NULL,
  `last_calculated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usage_aggregate_unique` (`subscription_id`,`feature_id`,`aggregate_date`,`aggregate_period`),
  KEY `rollup_usage_idx` (`subscription_id`,`aggregate_date`,`feature_id`),
  KEY `global_usage_trends_idx` (`aggregate_date`,`feature_id`,`total_quantity`),
  KEY `fk_metered_usage_aggregates_features` (`feature_id`),
  CONSTRAINT `fk_metered_usage_aggregates_features` FOREIGN KEY (`feature_id`) REFERENCES `features` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_metered_usage_aggregates_subscriptions` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Pre-aggregated usage data for performance';

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
-- Comment: Discount codes and promotions
--

CREATE TABLE `discounts` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Enum values: percentage, fixed, trial, usage',
  `amount` decimal(10,4) NOT NULL,
  `currency` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `applies_to` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Enum values: all, plans, features, users, subscriptions',
  `applies_to_ids` json DEFAULT NULL,
  `max_redemptions` int DEFAULT NULL,
  `times_redeemed` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `starts_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `duration` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Enum values: once, forever, repeating, subscription',
  `duration_in_months` int DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `restrictions` json DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code_UNIQUE` (`code`),
  KEY `active_discounts_idx` (`code`,`is_active`,`expires_at`),
  KEY `discount_finder_idx` (`type`,`applies_to`,`starts_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Discount codes and promotions';

-- --------------------------------------------------------

--
-- Table structure for table `subscription_events`
-- Comment: Audit log for subscription changes
--

CREATE TABLE `subscription_events` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `subscription_id` int UNSIGNED NOT NULL,
  `type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Enum values: created, updated, canceled, reactivated, plan_changed, quantity_changed, trial_started, trial_ended, invoice_created, payment_succeeded, payment_failed, usage_recorded, downgrade_scheduled',
  `data` json DEFAULT NULL,
  `changes` json DEFAULT NULL,
  `causer_id` int UNSIGNED DEFAULT NULL,
  `causer_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL,
  `occurred_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscription_timeline_idx` (`subscription_id`,`occurred_at`),
  KEY `event_analytics_idx` (`type`,`occurred_at`),
  KEY `causer_events_idx` (`causer_id`,`causer_type`,`occurred_at`),
  KEY `subscription_state_changes_idx` (`subscription_id`,`type`,`occurred_at`),
  CONSTRAINT `fk_subscription_events_subscriptions` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Audit log for subscription changes';

-- --------------------------------------------------------

--
-- Table structure for table `subscription_orders`
-- Comment: Orders for multiple subscriptions
--

CREATE TABLE `subscription_orders` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `payment_master_id` int UNSIGNED DEFAULT NULL,
  `order_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft' COMMENT 'Enum values: draft, pending, processing, completed, cancelled, failed',
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new' COMMENT 'Enum values: new, renewal, upgrade, downgrade, bulk',
  `subtotal` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `tax_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `discount_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `total_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `currency` char(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `customer_info` json DEFAULT NULL,
  `billing_address` json DEFAULT NULL,
  `ordered_at` timestamp NULL DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `coupon_code` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `applied_discounts` json DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `failure_reason` text COLLATE utf8mb4_unicode_ci,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number_UNIQUE` (`order_number`),
  KEY `user_subscription_orders_idx` (`user_id`,`status`,`ordered_at`),
  KEY `order_lookup_idx` (`order_number`,`type`),
  KEY `order_processing_idx` (`status`,`ordered_at`),
  KEY `fk_subscription_orders_payment_masters` (`payment_master_id`),
  CONSTRAINT `fk_subscription_orders_payment_masters` FOREIGN KEY (`payment_master_id`) REFERENCES `payment_masters` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_subscription_orders_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Orders for multiple subscriptions';

-- --------------------------------------------------------

--
-- Table structure for table `subscription_order_items`
-- Comment: Items within subscription orders
--

CREATE TABLE `subscription_order_items` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `subscription_order_id` int UNSIGNED NOT NULL,
  `plan_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `plan_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `billing_cycle` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `recipient_user_id` int UNSIGNED DEFAULT NULL,
  `recipient_info` json DEFAULT NULL,
  `unit_price` decimal(20,8) NOT NULL,
  `amount` decimal(20,8) NOT NULL,
  `tax_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `discount_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `total_amount` decimal(20,8) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `subscription_id` int UNSIGNED DEFAULT NULL,
  `subscription_status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'Enum values: pending, created, failed',
  `processing_error` text COLLATE utf8mb4_unicode_ci,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_idx` (`subscription_order_id`,`plan_id`),
  KEY `user_order_subscriptions_idx` (`user_id`,`subscription_id`),
  KEY `gifted_subscriptions_idx` (`recipient_user_id`,`subscription_status`),
  KEY `fk_subscription_order_items_plans` (`plan_id`),
  KEY `fk_subscription_order_items_subscriptions` (`subscription_id`),
  KEY `idx_subscription_order_items_status` (`subscription_status`,`processed_at`),
  CONSTRAINT `fk_subscription_order_items_plans` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_subscription_order_items_recipient_users` FOREIGN KEY (`recipient_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_subscription_order_items_subscription_orders` FOREIGN KEY (`subscription_order_id`) REFERENCES `subscription_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_subscription_order_items_subscriptions` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_subscription_order_items_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Items within subscription orders';

--
-- Triggers `subscription_order_items`
--
DELIMITER $$
CREATE TRIGGER `update_subscription_from_order_item` AFTER UPDATE ON `subscription_order_items` FOR EACH ROW BEGIN
    IF NEW.subscription_status = 'created' AND OLD.subscription_status = 'pending' AND NEW.subscription_id IS NOT NULL THEN
        -- Update subscription with order information
        UPDATE subscriptions
        SET
            updated_at = NOW(),
            metadata = JSON_MERGE_PATCH(
                COALESCE(metadata, '{}'),
                JSON_OBJECT('order_item_id', NEW.id)
            )
        WHERE id = NEW.subscription_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `rate_limits`
-- Comment: Rate limiting for subscription features
--

CREATE TABLE `rate_limits` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `subscription_id` int UNSIGNED NOT NULL,
  `feature_id` int UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `max_attempts` int NOT NULL,
  `decay_seconds` int NOT NULL,
  `remaining` int NOT NULL,
  `resets_at` timestamp NOT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rate_limit_unique` (`subscription_id`,`feature_id`,`key`),
  KEY `expired_rate_limits_idx` (`resets_at`),
  KEY `fk_rate_limits_features` (`feature_id`),
  CONSTRAINT `fk_rate_limits_features` FOREIGN KEY (`feature_id`) REFERENCES `features` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_rate_limits_subscriptions` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Rate limiting for subscription features';

-- --------------------------------------------------------

--
-- Stand-in structure for view `monthly_recurring_revenue`
--
DROP VIEW IF EXISTS `monthly_recurring_revenue`;
CREATE TABLE `monthly_recurring_revenue` (
`month` varchar(7),
`currency` char(3),
`active_subscriptions` bigint,
`mrr` decimal(42,8),
`trial_mrr` decimal(42,8),
`churned_mrr` decimal(42,8)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `subscription_usage_summary`
--
DROP VIEW IF EXISTS `subscription_usage_summary`;
CREATE TABLE `subscription_usage_summary` (
`subscription_id` int unsigned,
`user_email` varchar(255),
`plan_name` varchar(255),
`feature_code` varchar(100),
`feature_name` varchar(255),
`total_usage` decimal(42,8),
`limit_value` varchar(255),
`usage_percentage` decimal(57,12)
);

-- --------------------------------------------------------

CREATE TABLE plan_discounts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    plan_id INT UNSIGNED NOT NULL,
    discount_id INT UNSIGNED NOT NULL,
    created_by INT UNSIGNED NULL,
    updated_by INT UNSIGNED NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    deleted_at TIMESTAMP NULL DEFAULT NULL,

    CONSTRAINT fk_plan_discounts_plan
        FOREIGN KEY (plan_id)
        REFERENCES plans(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_plan_discounts_discount
        FOREIGN KEY (discount_id)
        REFERENCES discounts(id)
        ON DELETE CASCADE,

    UNIQUE KEY unique_plan_discount (plan_id, discount_id)
);

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);
--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);


--
-- Structure for view `monthly_recurring_revenue`
--
DROP VIEW IF EXISTS `monthly_recurring_revenue`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `monthly_recurring_revenue`  AS SELECT date_format(`s`.`current_period_starts_at`,'%Y-%m') AS `month`, `s`.`currency` AS `currency`, count(distinct `s`.`id`) AS `active_subscriptions`, sum(`s`.`amount`) AS `mrr`, sum((case when (`s`.`trial_ends_at` > now()) then `s`.`amount` else 0 end)) AS `trial_mrr`, sum((case when (`s`.`status` = 'canceled') then `s`.`amount` else 0 end)) AS `churned_mrr` FROM `subscriptions` AS `s` WHERE ((`s`.`status` in ('active','trialing')) AND (`s`.`current_period_starts_at` >= (now() - interval 1 year))) GROUP BY date_format(`s`.`current_period_starts_at`,'%Y-%m'), `s`.`currency` ;

-- --------------------------------------------------------

--
-- Structure for view `subscription_usage_summary`
--
DROP VIEW IF EXISTS `subscription_usage_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `subscription_usage_summary`  AS SELECT `s`.`id` AS `subscription_id`, `u`.`email` AS `user_email`, `p`.`name` AS `plan_name`, `f`.`code` AS `feature_code`, `f`.`name` AS `feature_name`, coalesce(sum(`ur`.`quantity`),0) AS `total_usage`, `pf`.`value` AS `limit_value`, (case when (`pf`.`value` = 'unlimited') then 100 else ((coalesce(sum(`ur`.`quantity`),0) / cast(`pf`.`value` as decimal(20,8))) * 100) end) AS `usage_percentage` FROM (((((`subscriptions` `s` join `users` `u` on((`s`.`user_id` = `u`.`id`))) join `plans` `p` on((`s`.`plan_id` = `p`.`id`))) join `plan_features` `pf` on((`p`.`id` = `pf`.`plan_id`))) join `features` `f` on((`pf`.`feature_id` = `f`.`id`))) left join `usage_records` `ur` on(((`s`.`id` = `ur`.`subscription_id`) and (`f`.`id` = `ur`.`feature_id`) and (`ur`.`billing_date` >= `s`.`current_period_starts_at`) and (`ur`.`billing_date` <= `s`.`current_period_ends_at`)))) WHERE (`s`.`status` in ('active','trialing')) GROUP BY `s`.`id`, `u`.`email`, `p`.`name`, `f`.`code`, `f`.`name`, `pf`.`value` ;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
