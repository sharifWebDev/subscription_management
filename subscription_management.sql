-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 21, 2026 at 04:09 PM
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
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('subscription-management-cache-a75f3f172bfb296f2e10cbfc6dfc1883', 'i:2;', 1771689314),
('subscription-management-cache-a75f3f172bfb296f2e10cbfc6dfc1883:timer', 'i:1771689314;', 1771689314),
('subscription-management-cache-c525a5357e97fef8d3db25841c86da1a', 'i:4;', 1771688821),
('subscription-management-cache-c525a5357e97fef8d3db25841c86da1a:timer', 'i:1771688821;', 1771688821),
('subscription-management-cache-c78d911da844c8f63868d169a5d6002e', 'i:1;', 1771689325),
('subscription-management-cache-c78d911da844c8f63868d169a5d6002e:timer', 'i:1771689325;', 1771689325),
('subscription-management-cache-e9b6cc1432541b9ceebf113eee05eeba', 'i:1;', 1771689969),
('subscription-management-cache-e9b6cc1432541b9ceebf113eee05eeba:timer', 'i:1771689969;', 1771689969),
('subscription-management-cache-f1f70ec40aaa556905d4a030501c0ba4', 'i:11;', 1771689757),
('subscription-management-cache-f1f70ec40aaa556905d4a030501c0ba4:timer', 'i:1771689757;', 1771689757);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

CREATE TABLE `discounts` (
  `id` int UNSIGNED NOT NULL,
  `code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Enum values: percentage, fixed, trial, usage',
  `amount` decimal(10,4) NOT NULL,
  `currency` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `applies_to` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Enum values: all, plans, features, users, subscriptions',
  `applies_to_ids` json DEFAULT NULL,
  `max_redemptions` int DEFAULT NULL,
  `times_redeemed` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `starts_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `duration` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Enum values: once, forever, repeating, subscription',
  `duration_in_months` int DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `restrictions` json DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Discount codes and promotions';

--
-- Dumping data for table `discounts`
--

INSERT INTO `discounts` (`id`, `code`, `name`, `type`, `amount`, `currency`, `applies_to`, `applies_to_ids`, `max_redemptions`, `times_redeemed`, `is_active`, `starts_at`, `expires_at`, `duration`, `duration_in_months`, `metadata`, `restrictions`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'WELCOME20', 'Welcome Discount 20%', 'percentage', '20.0000', NULL, 'all', NULL, 1000, 145, 1, '2025-11-21 07:55:09', '2026-05-21 07:55:09', 'repeating', 3, '{\"source\": \"email\", \"campaign\": \"welcome\"}', '{\"min_amount\": 10, \"new_customers_only\": true}', NULL, NULL, '2025-11-21 07:55:09', '2026-02-21 07:55:09', NULL),
(2, 'SAVE10', 'Save $10', 'fixed', '10.0000', 'USD', 'plans', '[2, 3]', 500, 78, 1, '2026-01-21 07:55:09', '2026-04-21 07:55:09', 'once', NULL, '{\"campaign\": \"spring_sale\"}', '{\"first_payment_only\": true}', NULL, NULL, '2026-01-21 07:55:09', '2026-02-21 07:55:09', NULL),
(3, 'FREETRIAL', 'Free Trial', 'trial', '14.0000', NULL, 'plans', '[3]', NULL, 230, 1, '2025-08-21 07:55:09', NULL, 'once', NULL, '{\"campaign\": \"trial\"}', '{\"new_customers_only\": true}', NULL, NULL, '2025-08-21 07:55:09', '2026-02-21 07:55:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `features`
--

CREATE TABLE `features` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'limit' COMMENT 'Enum values: limit, boolean, tiered',
  `scope` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'global' COMMENT 'Enum values: global, per_user, per_seat, per_team',
  `is_resettable` tinyint(1) NOT NULL DEFAULT '0',
  `reset_period` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'monthly' COMMENT 'Enum values: monthly, yearly, weekly, never',
  `metadata` json DEFAULT NULL,
  `validations` json DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Available features that can be included in plans';

--
-- Dumping data for table `features`
--

INSERT INTO `features` (`id`, `name`, `code`, `description`, `type`, `scope`, `is_resettable`, `reset_period`, `metadata`, `validations`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'API Requests', 'api_requests', 'Number of API requests per month', 'limit', 'global', 1, 'monthly', '{\"unit\": \"requests\", \"display_order\": 1}', '{\"max\": 10000000, \"min\": 0}', NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(2, 'Storage', 'storage_gb', 'Storage space in gigabytes', 'limit', 'global', 1, 'monthly', '{\"unit\": \"GB\", \"display_order\": 2}', '{\"max\": 10000, \"min\": 0}', NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(3, 'Users', 'users', 'Number of team members', 'limit', 'per_seat', 1, 'monthly', '{\"unit\": \"users\", \"display_order\": 3}', '{\"max\": 1000, \"min\": 1}', NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(4, 'Priority Support', 'priority_support', 'Access to priority customer support', 'boolean', 'global', 0, 'never', '{\"display_order\": 4}', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(5, 'Custom Domain', 'custom_domain', 'Use your own domain name', 'boolean', 'global', 0, 'never', '{\"display_order\": 5}', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(6, 'Webhooks', 'webhooks', 'Number of webhook endpoints', 'limit', 'global', 1, 'never', '{\"unit\": \"endpoints\", \"display_order\": 6}', '{\"max\": 100, \"min\": 0}', NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(7, 'Export', 'export', 'Data export functionality', 'boolean', 'global', 0, 'never', '{\"display_order\": 7}', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(8, 'API Rate Limit', 'api_rate_limit', 'API requests per second', 'limit', 'global', 1, 'monthly', '{\"unit\": \"rps\", \"display_order\": 8}', '{\"max\": 10000, \"min\": 1}', NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `subscription_id` int UNSIGNED DEFAULT NULL,
  `number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `external_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Enum values: subscription, one_time, credit, adjustment',
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Enum values: draft, open, paid, void, uncollectible, refunded',
  `subtotal` decimal(20,8) NOT NULL,
  `tax` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `total` decimal(20,8) NOT NULL,
  `amount_due` decimal(20,8) NOT NULL,
  `amount_paid` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `amount_remaining` decimal(20,8) GENERATED ALWAYS AS ((`total` - `amount_paid`)) STORED,
  `currency` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `issue_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `due_date` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `finalized_at` timestamp NULL DEFAULT NULL,
  `line_items` json DEFAULT NULL,
  `tax_rates` json DEFAULT NULL,
  `discounts` json DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `history` json DEFAULT NULL,
  `pdf_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Billing invoices';

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `user_id`, `subscription_id`, `number`, `external_id`, `type`, `status`, `subtotal`, `tax`, `total`, `amount_due`, `amount_paid`, `currency`, `issue_date`, `due_date`, `paid_at`, `finalized_at`, `line_items`, `tax_rates`, `discounts`, `metadata`, `history`, `pdf_url`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 3, 2, 'INV-20260201-0002', 'in_xYtTKTXATGW7zE', 'subscription', 'paid', '499.95000000', '41.24587500', '541.19587500', '541.19587500', '541.19587500', 'USD', '2026-02-01 07:55:09', '2026-02-06 07:55:09', '2026-02-01 07:55:09', '2026-02-01 07:55:09', '[{\"amount\": \"499.95000000\", \"quantity\": 1, \"description\": \"Subscription - Feb 2026\"}]', '[{\"name\": \"Sales Tax\", \"rate\": 8.25, \"amount\": 41.245875}]', NULL, '{\"source\": \"subscription\"}', '[{\"date\": \"2026-02-01 13:55:09\", \"status\": \"open\"}, {\"date\": \"2026-02-01 13:55:09\", \"status\": \"paid\"}]', 'https://example.com/invoices/PHZobFetHx.pdf', NULL, NULL, '2026-02-01 07:55:09', '2026-02-01 07:55:09', NULL),
(2, 2, 1, 'INV-20260206-0003', 'in_l2vLnAku1Fzcyv', 'subscription', 'paid', '29.99000000', '0.00000000', '29.99000000', '29.99000000', '29.99000000', 'USD', '2026-02-06 07:55:09', '2026-02-11 07:55:09', '2026-02-06 07:55:09', '2026-02-06 07:55:09', '[{\"amount\": \"29.99000000\", \"quantity\": 1, \"description\": \"Subscription - Feb 2026\"}]', NULL, NULL, '{\"source\": \"subscription\"}', '[{\"date\": \"2026-02-06 13:55:09\", \"status\": \"open\"}, {\"date\": \"2026-02-06 13:55:09\", \"status\": \"paid\"}]', 'https://example.com/invoices/LBwmChNafe.pdf', NULL, NULL, '2026-02-06 07:55:09', '2026-02-06 07:55:09', NULL),
(3, 4, 3, 'INV-20250621-0004', 'in_RE4IjmEhmn6Sms', 'subscription', 'paid', '299.99000000', '0.00000000', '299.99000000', '299.99000000', '299.99000000', 'USD', '2025-06-21 07:55:09', '2025-06-26 07:55:09', '2025-06-21 07:55:09', '2025-06-21 07:55:09', '[{\"amount\": \"299.99000000\", \"quantity\": 1, \"description\": \"Subscription - Jun 2025\"}]', NULL, NULL, '{\"source\": \"subscription\"}', '[{\"date\": \"2025-06-21 13:55:09\", \"status\": \"open\"}, {\"date\": \"2025-06-21 13:55:09\", \"status\": \"paid\"}]', 'https://example.com/invoices/WX2jMXPZY2.pdf', NULL, NULL, '2025-06-21 07:55:09', '2025-06-21 07:55:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `metered_usage_aggregates`
--

CREATE TABLE `metered_usage_aggregates` (
  `id` int UNSIGNED NOT NULL,
  `subscription_id` int UNSIGNED NOT NULL,
  `feature_id` int UNSIGNED NOT NULL,
  `aggregate_date` date NOT NULL,
  `aggregate_period` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Enum values: daily, weekly, monthly, yearly',
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
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Pre-aggregated usage data for performance';

--
-- Dumping data for table `metered_usage_aggregates`
--

INSERT INTO `metered_usage_aggregates` (`id`, `subscription_id`, `feature_id`, `aggregate_date`, `aggregate_period`, `total_quantity`, `tier1_quantity`, `tier2_quantity`, `tier3_quantity`, `total_amount`, `record_count`, `last_calculated_at`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 4, 1, '2026-02-21', 'daily', '4236.00000000', '3015.00000000', '1284.00000000', '0.00000000', '0.52000000', 10, '2026-02-21 07:55:09', NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(2, 4, 2, '2026-02-21', 'daily', '4.50000000', '2.20000000', '1.40000000', '0.00000000', '0.30000000', 1, '2026-02-21 07:55:09', NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(3, 4, 1, '2026-02-20', 'daily', '4432.00000000', '2773.00000000', '2846.00000000', '0.00000000', '0.20000000', 38, '2026-02-20 07:55:09', NULL, NULL, '2026-02-20 07:55:09', '2026-02-20 07:55:09', NULL),
(4, 4, 2, '2026-02-20', 'daily', '4.20000000', '2.60000000', '1.60000000', '0.00000000', '0.11000000', 3, '2026-02-20 07:55:09', NULL, NULL, '2026-02-20 07:55:09', '2026-02-20 07:55:09', NULL),
(5, 4, 1, '2026-02-19', 'daily', '5550.00000000', '3532.00000000', '2238.00000000', '0.00000000', '0.39000000', 40, '2026-02-19 07:55:09', NULL, NULL, '2026-02-19 07:55:09', '2026-02-19 07:55:09', NULL),
(6, 4, 2, '2026-02-19', 'daily', '1.80000000', '2.70000000', '1.30000000', '0.00000000', '0.34000000', 2, '2026-02-19 07:55:09', NULL, NULL, '2026-02-19 07:55:09', '2026-02-19 07:55:09', NULL),
(7, 4, 1, '2026-02-18', 'daily', '4703.00000000', '2987.00000000', '1273.00000000', '0.00000000', '0.75000000', 25, '2026-02-18 07:55:09', NULL, NULL, '2026-02-18 07:55:09', '2026-02-18 07:55:09', NULL),
(8, 4, 2, '2026-02-18', 'daily', '3.40000000', '1.40000000', '1.40000000', '0.00000000', '0.35000000', 2, '2026-02-18 07:55:09', NULL, NULL, '2026-02-18 07:55:09', '2026-02-18 07:55:09', NULL),
(9, 4, 1, '2026-02-17', 'daily', '7594.00000000', '4578.00000000', '1722.00000000', '0.00000000', '0.41000000', 26, '2026-02-17 07:55:09', NULL, NULL, '2026-02-17 07:55:09', '2026-02-17 07:55:09', NULL),
(10, 4, 2, '2026-02-17', 'daily', '2.20000000', '0.80000000', '0.50000000', '0.00000000', '0.28000000', 3, '2026-02-17 07:55:09', NULL, NULL, '2026-02-17 07:55:09', '2026-02-17 07:55:09', NULL),
(11, 4, 1, '2026-02-16', 'daily', '5773.00000000', '2361.00000000', '2131.00000000', '0.00000000', '0.62000000', 35, '2026-02-16 07:55:09', NULL, NULL, '2026-02-16 07:55:09', '2026-02-16 07:55:09', NULL),
(12, 4, 2, '2026-02-16', 'daily', '4.10000000', '1.10000000', '1.10000000', '0.00000000', '0.33000000', 1, '2026-02-16 07:55:09', NULL, NULL, '2026-02-16 07:55:09', '2026-02-16 07:55:09', NULL),
(13, 4, 1, '2026-02-15', 'daily', '6996.00000000', '4028.00000000', '1643.00000000', '0.00000000', '0.37000000', 10, '2026-02-15 07:55:09', NULL, NULL, '2026-02-15 07:55:09', '2026-02-15 07:55:09', NULL),
(14, 4, 2, '2026-02-15', 'daily', '3.40000000', '2.70000000', '1.70000000', '0.00000000', '0.17000000', 1, '2026-02-15 07:55:09', NULL, NULL, '2026-02-15 07:55:09', '2026-02-15 07:55:09', NULL),
(15, 4, 1, '2026-02-14', 'daily', '3666.00000000', '3334.00000000', '1973.00000000', '0.00000000', '0.47000000', 24, '2026-02-14 07:55:09', NULL, NULL, '2026-02-14 07:55:09', '2026-02-14 07:55:09', NULL),
(16, 4, 2, '2026-02-14', 'daily', '4.10000000', '2.50000000', '0.50000000', '0.00000000', '0.43000000', 1, '2026-02-14 07:55:09', NULL, NULL, '2026-02-14 07:55:09', '2026-02-14 07:55:09', NULL),
(17, 4, 1, '2026-02-13', 'daily', '6153.00000000', '3536.00000000', '2344.00000000', '0.00000000', '0.35000000', 23, '2026-02-13 07:55:09', NULL, NULL, '2026-02-13 07:55:09', '2026-02-13 07:55:09', NULL),
(18, 4, 2, '2026-02-13', 'daily', '2.90000000', '1.80000000', '1.70000000', '0.00000000', '0.40000000', 5, '2026-02-13 07:55:09', NULL, NULL, '2026-02-13 07:55:09', '2026-02-13 07:55:09', NULL),
(19, 4, 1, '2026-02-12', 'daily', '5718.00000000', '1725.00000000', '1064.00000000', '0.00000000', '0.43000000', 37, '2026-02-12 07:55:09', NULL, NULL, '2026-02-12 07:55:09', '2026-02-12 07:55:09', NULL),
(20, 4, 2, '2026-02-12', 'daily', '4.90000000', '0.50000000', '0.80000000', '0.00000000', '0.10000000', 1, '2026-02-12 07:55:09', NULL, NULL, '2026-02-12 07:55:09', '2026-02-12 07:55:09', NULL),
(21, 4, 1, '2026-02-11', 'daily', '7862.00000000', '3813.00000000', '2621.00000000', '0.00000000', '0.73000000', 45, '2026-02-11 07:55:09', NULL, NULL, '2026-02-11 07:55:09', '2026-02-11 07:55:09', NULL),
(22, 4, 2, '2026-02-11', 'daily', '2.30000000', '1.40000000', '0.50000000', '0.00000000', '0.29000000', 1, '2026-02-11 07:55:09', NULL, NULL, '2026-02-11 07:55:09', '2026-02-11 07:55:09', NULL),
(23, 4, 1, '2026-02-10', 'daily', '2036.00000000', '4734.00000000', '1856.00000000', '0.00000000', '0.77000000', 12, '2026-02-10 07:55:09', NULL, NULL, '2026-02-10 07:55:09', '2026-02-10 07:55:09', NULL),
(24, 4, 2, '2026-02-10', 'daily', '1.70000000', '0.50000000', '1.80000000', '0.00000000', '0.19000000', 4, '2026-02-10 07:55:09', NULL, NULL, '2026-02-10 07:55:09', '2026-02-10 07:55:09', NULL),
(25, 4, 1, '2026-02-09', 'daily', '2353.00000000', '2508.00000000', '2963.00000000', '0.00000000', '0.72000000', 10, '2026-02-09 07:55:09', NULL, NULL, '2026-02-09 07:55:09', '2026-02-09 07:55:09', NULL),
(26, 4, 2, '2026-02-09', 'daily', '3.90000000', '1.60000000', '1.10000000', '0.00000000', '0.12000000', 5, '2026-02-09 07:55:09', NULL, NULL, '2026-02-09 07:55:09', '2026-02-09 07:55:09', NULL),
(27, 4, 1, '2026-02-08', 'daily', '4293.00000000', '1555.00000000', '1957.00000000', '0.00000000', '0.44000000', 48, '2026-02-08 07:55:09', NULL, NULL, '2026-02-08 07:55:09', '2026-02-08 07:55:09', NULL),
(28, 4, 2, '2026-02-08', 'daily', '3.30000000', '3.00000000', '1.60000000', '0.00000000', '0.37000000', 2, '2026-02-08 07:55:09', NULL, NULL, '2026-02-08 07:55:09', '2026-02-08 07:55:09', NULL),
(29, 4, 1, '2026-02-07', 'daily', '2215.00000000', '2036.00000000', '1222.00000000', '0.00000000', '0.72000000', 45, '2026-02-07 07:55:09', NULL, NULL, '2026-02-07 07:55:09', '2026-02-07 07:55:09', NULL),
(30, 4, 2, '2026-02-07', 'daily', '1.50000000', '0.70000000', '0.70000000', '0.00000000', '0.46000000', 5, '2026-02-07 07:55:09', NULL, NULL, '2026-02-07 07:55:09', '2026-02-07 07:55:09', NULL),
(31, 4, 1, '2026-02-06', 'daily', '6799.00000000', '2728.00000000', '1715.00000000', '0.00000000', '0.33000000', 33, '2026-02-06 07:55:09', NULL, NULL, '2026-02-06 07:55:09', '2026-02-06 07:55:09', NULL),
(32, 4, 2, '2026-02-06', 'daily', '1.70000000', '2.20000000', '1.90000000', '0.00000000', '0.45000000', 2, '2026-02-06 07:55:09', NULL, NULL, '2026-02-06 07:55:09', '2026-02-06 07:55:09', NULL),
(33, 4, 1, '2026-02-05', 'daily', '3861.00000000', '4214.00000000', '2228.00000000', '0.00000000', '0.31000000', 14, '2026-02-05 07:55:09', NULL, NULL, '2026-02-05 07:55:09', '2026-02-05 07:55:09', NULL),
(34, 4, 2, '2026-02-05', 'daily', '2.40000000', '1.80000000', '1.20000000', '0.00000000', '0.18000000', 4, '2026-02-05 07:55:09', NULL, NULL, '2026-02-05 07:55:09', '2026-02-05 07:55:09', NULL),
(35, 4, 1, '2026-02-04', 'daily', '7458.00000000', '3359.00000000', '1782.00000000', '0.00000000', '0.30000000', 49, '2026-02-04 07:55:09', NULL, NULL, '2026-02-04 07:55:09', '2026-02-04 07:55:09', NULL),
(36, 4, 2, '2026-02-04', 'daily', '4.70000000', '1.00000000', '0.50000000', '0.00000000', '0.32000000', 3, '2026-02-04 07:55:09', NULL, NULL, '2026-02-04 07:55:09', '2026-02-04 07:55:09', NULL),
(37, 4, 1, '2026-02-03', 'daily', '3602.00000000', '1124.00000000', '2458.00000000', '0.00000000', '0.68000000', 44, '2026-02-03 07:55:09', NULL, NULL, '2026-02-03 07:55:09', '2026-02-03 07:55:09', NULL),
(38, 4, 2, '2026-02-03', 'daily', '3.20000000', '2.30000000', '0.60000000', '0.00000000', '0.43000000', 1, '2026-02-03 07:55:09', NULL, NULL, '2026-02-03 07:55:09', '2026-02-03 07:55:09', NULL),
(39, 4, 1, '2026-02-02', 'daily', '6174.00000000', '2000.00000000', '2590.00000000', '0.00000000', '0.62000000', 16, '2026-02-02 07:55:09', NULL, NULL, '2026-02-02 07:55:09', '2026-02-02 07:55:09', NULL),
(40, 4, 2, '2026-02-02', 'daily', '1.30000000', '1.10000000', '0.90000000', '0.00000000', '0.47000000', 4, '2026-02-02 07:55:09', NULL, NULL, '2026-02-02 07:55:09', '2026-02-02 07:55:09', NULL),
(41, 4, 1, '2026-02-01', 'daily', '6388.00000000', '1919.00000000', '2972.00000000', '0.00000000', '0.73000000', 37, '2026-02-01 07:55:09', NULL, NULL, '2026-02-01 07:55:09', '2026-02-01 07:55:09', NULL),
(42, 4, 2, '2026-02-01', 'daily', '1.20000000', '2.20000000', '1.70000000', '0.00000000', '0.18000000', 3, '2026-02-01 07:55:09', NULL, NULL, '2026-02-01 07:55:09', '2026-02-01 07:55:09', NULL),
(43, 4, 1, '2026-01-31', 'daily', '3206.00000000', '4364.00000000', '2524.00000000', '0.00000000', '0.35000000', 39, '2026-01-31 07:55:09', NULL, NULL, '2026-01-31 07:55:09', '2026-01-31 07:55:09', NULL),
(44, 4, 2, '2026-01-31', 'daily', '4.10000000', '2.00000000', '1.80000000', '0.00000000', '0.15000000', 3, '2026-01-31 07:55:09', NULL, NULL, '2026-01-31 07:55:09', '2026-01-31 07:55:09', NULL),
(45, 4, 1, '2026-01-30', 'daily', '6750.00000000', '4839.00000000', '2998.00000000', '0.00000000', '0.54000000', 36, '2026-01-30 07:55:09', NULL, NULL, '2026-01-30 07:55:09', '2026-01-30 07:55:09', NULL),
(46, 4, 2, '2026-01-30', 'daily', '4.70000000', '2.20000000', '0.80000000', '0.00000000', '0.17000000', 2, '2026-01-30 07:55:09', NULL, NULL, '2026-01-30 07:55:09', '2026-01-30 07:55:09', NULL),
(47, 4, 1, '2026-01-29', 'daily', '2917.00000000', '3540.00000000', '1291.00000000', '0.00000000', '0.36000000', 42, '2026-01-29 07:55:09', NULL, NULL, '2026-01-29 07:55:09', '2026-01-29 07:55:09', NULL),
(48, 4, 2, '2026-01-29', 'daily', '4.00000000', '0.50000000', '1.70000000', '0.00000000', '0.15000000', 2, '2026-01-29 07:55:09', NULL, NULL, '2026-01-29 07:55:09', '2026-01-29 07:55:09', NULL),
(49, 4, 1, '2026-01-28', 'daily', '6611.00000000', '3856.00000000', '2432.00000000', '0.00000000', '0.44000000', 18, '2026-01-28 07:55:09', NULL, NULL, '2026-01-28 07:55:09', '2026-01-28 07:55:09', NULL),
(50, 4, 2, '2026-01-28', 'daily', '1.30000000', '2.70000000', '0.70000000', '0.00000000', '0.13000000', 1, '2026-01-28 07:55:09', NULL, NULL, '2026-01-28 07:55:09', '2026-01-28 07:55:09', NULL),
(51, 4, 1, '2026-01-27', 'daily', '3923.00000000', '1667.00000000', '1841.00000000', '0.00000000', '0.62000000', 46, '2026-01-27 07:55:09', NULL, NULL, '2026-01-27 07:55:09', '2026-01-27 07:55:09', NULL),
(52, 4, 2, '2026-01-27', 'daily', '2.80000000', '3.00000000', '1.50000000', '0.00000000', '0.48000000', 2, '2026-01-27 07:55:09', NULL, NULL, '2026-01-27 07:55:09', '2026-01-27 07:55:09', NULL),
(53, 4, 1, '2026-01-26', 'daily', '7270.00000000', '4911.00000000', '1087.00000000', '0.00000000', '0.34000000', 49, '2026-01-26 07:55:09', NULL, NULL, '2026-01-26 07:55:09', '2026-01-26 07:55:09', NULL),
(54, 4, 2, '2026-01-26', 'daily', '1.10000000', '0.80000000', '1.10000000', '0.00000000', '0.43000000', 3, '2026-01-26 07:55:09', NULL, NULL, '2026-01-26 07:55:09', '2026-01-26 07:55:09', NULL),
(55, 4, 1, '2026-01-25', 'daily', '2772.00000000', '2492.00000000', '2484.00000000', '0.00000000', '0.43000000', 15, '2026-01-25 07:55:09', NULL, NULL, '2026-01-25 07:55:09', '2026-01-25 07:55:09', NULL),
(56, 4, 2, '2026-01-25', 'daily', '2.00000000', '1.00000000', '1.90000000', '0.00000000', '0.27000000', 5, '2026-01-25 07:55:09', NULL, NULL, '2026-01-25 07:55:09', '2026-01-25 07:55:09', NULL),
(57, 4, 1, '2026-01-24', 'daily', '4044.00000000', '2545.00000000', '1594.00000000', '0.00000000', '0.24000000', 46, '2026-01-24 07:55:09', NULL, NULL, '2026-01-24 07:55:09', '2026-01-24 07:55:09', NULL),
(58, 4, 2, '2026-01-24', 'daily', '1.40000000', '2.50000000', '0.50000000', '0.00000000', '0.27000000', 3, '2026-01-24 07:55:09', NULL, NULL, '2026-01-24 07:55:09', '2026-01-24 07:55:09', NULL),
(59, 4, 1, '2026-01-23', 'daily', '3673.00000000', '1197.00000000', '1149.00000000', '0.00000000', '0.69000000', 36, '2026-01-23 07:55:09', NULL, NULL, '2026-01-23 07:55:09', '2026-01-23 07:55:09', NULL),
(60, 4, 2, '2026-01-23', 'daily', '2.10000000', '1.20000000', '1.20000000', '0.00000000', '0.28000000', 5, '2026-01-23 07:55:09', NULL, NULL, '2026-01-23 07:55:09', '2026-01-23 07:55:09', NULL),
(61, 4, 1, '2026-02-01', 'monthly', '125000.00000000', '100000.00000000', '25000.00000000', '0.00000000', '125.00000000', 850, '2026-02-21 07:55:09', NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(62, 4, 2, '2026-02-01', 'monthly', '8.50000000', '8.50000000', '0.00000000', '0.00000000', '0.85000000', 30, '2026-02-21 07:55:09', NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `monthly_recurring_revenue`
--

CREATE TABLE `monthly_recurring_revenue` (
  `month` varchar(7) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `currency` char(3) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `active_subscriptions` bigint DEFAULT NULL,
  `mrr` decimal(42,8) DEFAULT NULL,
  `trial_mrr` decimal(42,8) DEFAULT NULL,
  `churned_mrr` decimal(42,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `otp_verifications`
--

CREATE TABLE `otp_verifications` (
  `id` bigint UNSIGNED NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL,
  `attempts` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `otp_verifications`
--

INSERT INTO `otp_verifications` (`id`, `email`, `otp`, `expires_at`, `attempts`, `created_at`, `updated_at`) VALUES
(3, 'nase@mailinator.com', '$2y$12$ferHEFcxMFAZYuQN59tEd.8tzbqLqrZuIY.nw3UNLEHWh6tu1jBH2', '2026-02-21 07:56:22', 0, '2026-02-21 07:46:22', '2026-02-21 07:46:22'),
(4, 'kaqywa@mailinator.com', '$2y$12$Xn1srOKfMGlgpi6830KgF.hNZf6lzQskLWP5xkZmJo4W28Ztw1Djq', '2026-02-21 08:05:56', 0, '2026-02-21 07:55:56', '2026-02-21 07:55:56'),
(6, 'seruwaquhe@mailinator.com', '$2y$12$SZOtfkUZqF/LnpdISyFm0.XR5RQJQ3oXC0vbfZSsDcnbGjWLpq2s.', '2026-02-21 09:37:22', 0, '2026-02-21 09:27:22', '2026-02-21 09:27:22');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int UNSIGNED NOT NULL,
  `invoice_id` int UNSIGNED DEFAULT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `external_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Enum values: card, bank, wallet, crypto, cash, credit',
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Enum values: pending, processing, completed, failed, refunded, disputed',
  `amount` decimal(20,8) NOT NULL,
  `fee` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `net` decimal(20,8) GENERATED ALWAYS AS ((`amount` - `fee`)) STORED,
  `currency` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `gateway` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Payment records for invoices';

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `invoice_id`, `user_id`, `external_id`, `type`, `status`, `amount`, `fee`, `currency`, `gateway`, `gateway_response`, `payment_method`, `processed_at`, `refunded_at`, `metadata`, `fraud_indicators`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 3, 'pay_ZLAuklvUW9w1KewJm', 'wallet', 'completed', '541.19587500', '15.99468038', 'USD', 'paypal', '{\"id\": \"pay_jutLjFauiA2MZSgKq\"}', '{\"type\": \"paypal_account\", \"email\": \"user@example.com\"}', '2026-02-01 07:55:09', NULL, '{\"invoice_number\": \"INV-20260201-0002\"}', NULL, NULL, NULL, '2026-02-01 07:55:09', '2026-02-01 07:55:09', NULL),
(2, 2, 2, 'ch_DtYB40IGEPxKfh', 'card', 'completed', '29.99000000', '1.16971000', 'USD', 'stripe', '{\"id\": \"ch_pPVlkEoiUMiFb0\"}', '{\"brand\": \"visa\", \"last4\": \"4242\"}', '2026-02-06 07:55:09', NULL, '{\"invoice_number\": \"INV-20260206-0003\"}', NULL, NULL, NULL, '2026-02-06 07:55:09', '2026-02-06 07:55:09', NULL),
(3, 3, 4, 'ch_gT4vJmdByLcuQv', 'card', 'completed', '299.99000000', '8.99971000', 'USD', 'stripe', '{\"id\": \"ch_Bjlbn6aEs27cAU\"}', '{\"brand\": \"visa\", \"last4\": \"4242\"}', '2025-06-21 07:55:09', NULL, '{\"invoice_number\": \"INV-20250621-0004\"}', NULL, NULL, NULL, '2025-06-21 07:55:09', '2025-06-21 07:55:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payment_allocations`
--

CREATE TABLE `payment_allocations` (
  `id` int UNSIGNED NOT NULL,
  `payment_master_id` int UNSIGNED NOT NULL,
  `payment_child_id` int UNSIGNED NOT NULL,
  `payment_transaction_id` int UNSIGNED NOT NULL,
  `allocatable_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `allocatable_id` int UNSIGNED NOT NULL,
  `amount` decimal(20,8) NOT NULL,
  `base_amount` decimal(20,8) GENERATED ALWAYS AS ((`amount` * `exchange_rate`)) STORED,
  `exchange_rate` decimal(12,6) NOT NULL DEFAULT '1.000000',
  `currency` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `allocation_reference` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `allocation_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'payment' COMMENT 'Enum values: payment, refund, credit, adjustment',
  `is_reversed` tinyint(1) NOT NULL DEFAULT '0',
  `reversed_at` timestamp NULL DEFAULT NULL,
  `reversal_id` int UNSIGNED DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Allocation of payments to specific items';

--
-- Dumping data for table `payment_allocations`
--

INSERT INTO `payment_allocations` (`id`, `payment_master_id`, `payment_child_id`, `payment_transaction_id`, `allocatable_type`, `allocatable_id`, `amount`, `exchange_rate`, `currency`, `allocation_reference`, `allocation_type`, `is_reversed`, `reversed_at`, `reversal_id`, `metadata`, `notes`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 1, 'invoice', 1, '541.19587500', '1.000000', 'USD', NULL, 'payment', 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-01 07:55:09', '2026-02-01 07:55:09', NULL),
(2, 2, 2, 2, 'invoice', 2, '29.99000000', '1.000000', 'USD', NULL, 'payment', 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-06 07:55:09', '2026-02-06 07:55:09', NULL),
(3, 3, 3, 3, 'invoice', 3, '299.99000000', '1.000000', 'USD', NULL, 'payment', 0, NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-21 07:55:09', '2025-06-21 07:55:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payment_children`
--

CREATE TABLE `payment_children` (
  `id` int UNSIGNED NOT NULL,
  `payment_master_id` int UNSIGNED NOT NULL,
  `item_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_id` int UNSIGNED NOT NULL,
  `subscription_id` int UNSIGNED DEFAULT NULL,
  `plan_id` int UNSIGNED DEFAULT NULL,
  `invoice_id` int UNSIGNED DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `quantity` int NOT NULL DEFAULT '1',
  `amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `tax_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `discount_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `total_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `period_start` date DEFAULT NULL,
  `period_end` date DEFAULT NULL,
  `billing_cycle` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'Enum values: pending, paid, refunded, cancelled, failed',
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
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Line items within a payment master';

--
-- Dumping data for table `payment_children`
--

INSERT INTO `payment_children` (`id`, `payment_master_id`, `item_type`, `item_id`, `subscription_id`, `plan_id`, `invoice_id`, `description`, `item_code`, `unit_price`, `quantity`, `amount`, `tax_amount`, `discount_amount`, `total_amount`, `period_start`, `period_end`, `billing_cycle`, `status`, `paid_at`, `allocated_amount`, `metadata`, `tax_breakdown`, `discount_breakdown`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'invoice', 1, 2, 3, 1, 'Professional - Monthly subscription', 'PRO', '99.99000000', 5, '499.95000000', '41.24587500', '0.00000000', '541.19587500', '2026-02-01', '2026-03-03', 'monthly', 'paid', '2026-02-01 07:55:09', '541.19587500', NULL, '[{\"name\": \"Sales Tax\", \"rate\": 8.25, \"amount\": \"41.24587500\"}]', NULL, NULL, NULL, '2026-02-01 07:55:09', '2026-02-01 07:55:09', NULL),
(2, 2, 'invoice', 2, 1, 2, 2, 'Starter - Monthly subscription', 'STARTER', '29.99000000', 1, '29.99000000', '0.00000000', '0.00000000', '29.99000000', '2026-02-06', '2026-03-08', 'monthly', 'paid', '2026-02-06 07:55:09', '29.99000000', NULL, NULL, NULL, NULL, NULL, '2026-02-06 07:55:09', '2026-02-06 07:55:09', NULL),
(3, 3, 'invoice', 3, 3, 4, 3, 'Enterprise - Monthly subscription', 'ENTERPRISE', '299.99000000', 1, '299.99000000', '0.00000000', '0.00000000', '299.99000000', '2025-06-21', '2026-06-21', 'monthly', 'paid', '2025-06-21 07:55:09', '299.99000000', NULL, NULL, NULL, NULL, NULL, '2025-06-21 07:55:09', '2025-06-21 07:55:09', NULL);

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
-- Table structure for table `payment_gateways`
--

CREATE TABLE `payment_gateways` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Enum values: card, wallet, bank, crypto, aggregator, cash',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `is_test_mode` tinyint(1) NOT NULL DEFAULT '1',
  `supports_recurring` tinyint(1) NOT NULL DEFAULT '0',
  `supports_refunds` tinyint(1) NOT NULL DEFAULT '0',
  `supports_installments` tinyint(1) NOT NULL DEFAULT '0',
  `api_key` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `api_secret` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `webhook_secret` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `merchant_id` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `merchant_password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_id` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `store_password` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `base_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `callback_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `webhook_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supported_currencies` json DEFAULT NULL,
  `supported_countries` json DEFAULT NULL,
  `excluded_countries` json DEFAULT NULL,
  `percentage_fee` decimal(5,2) NOT NULL DEFAULT '0.00',
  `fixed_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fee_currency` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
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
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configuration for payment gateways';

--
-- Dumping data for table `payment_gateways`
--

INSERT INTO `payment_gateways` (`id`, `name`, `code`, `type`, `is_active`, `is_test_mode`, `supports_recurring`, `supports_refunds`, `supports_installments`, `api_key`, `api_secret`, `webhook_secret`, `merchant_id`, `merchant_password`, `store_id`, `store_password`, `base_url`, `callback_url`, `webhook_url`, `supported_currencies`, `supported_countries`, `excluded_countries`, `percentage_fee`, `fixed_fee`, `fee_currency`, `fee_structure`, `config`, `metadata`, `settlement_days`, `refund_days`, `min_amount`, `max_amount`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Stripe', 'stripe', 'card', 1, 1, 1, 1, 0, 'pk_test_your_key', 'sk_test_your_secret', 'whsec_your_webhook_secret', NULL, NULL, NULL, NULL, 'https://api.stripe.com', NULL, 'http://127.0.0.1:8000/api/v1/webhooks/stripe', '[\"USD\", \"EUR\", \"GBP\", \"CAD\", \"AUD\"]', '[\"US\", \"GB\", \"CA\", \"AU\", \"DE\", \"FR\"]', NULL, '2.90', '0.30', 'USD', '{\"domestic\": {\"fixed\": 0.3, \"percentage\": 2.9}, \"international\": {\"fixed\": 0.3, \"percentage\": 3.9}}', '{\"api_version\": \"2023-10-16\", \"webhook_secret\": \"whsec_kKF3J1lHVaRlxCFotXXymWcJ\"}', NULL, 2, 5, '0.50', '999999.00', NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(2, 'PayPal', 'paypal', 'wallet', 1, 1, 1, 1, 0, 'AeCw1xR5vL5kQ9zX2yP3mN4bV6cX7zL8kQ9wE4rT5yU6iI7oP8aQ9wS0dF1gH2jK3l', 'EL9Q8wE4rT5yU6iI7oP8aQ9wS0dF1gH2jK3lZ4x', NULL, NULL, NULL, NULL, NULL, 'https://api-m.sandbox.paypal.com', NULL, 'http://127.0.0.1:8000/api/v1/webhooks/paypal', '[\"USD\", \"EUR\", \"GBP\", \"AUD\", \"CAD\", \"JPY\"]', '[\"US\", \"GB\", \"CA\", \"AU\", \"DE\", \"FR\", \"JP\"]', NULL, '3.40', '0.30', 'USD', '{\"domestic\": {\"fixed\": 0.49, \"percentage\": 2.99}, \"international\": {\"fixed\": 0.49, \"percentage\": 4.99}}', '{\"client_id\": \"AYjZ_L9ANvcbpIVS5AObRx1RpVYxPHEfFkHKx\", \"webhook_id\": \"WH_A80rVfnvVhbN\"}', NULL, 2, 5, '1.00', '999999.00', NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 15:25:32', NULL),
(3, 'SSLCommerz', 'sslcommerz', 'aggregator', 1, 1, 0, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://127.0.0.1:8000/api/v1/webhooks/sslcommerz', '[\"BDT\", \"USD\"]', '[\"BD\"]', NULL, '2.00', '0.00', 'BDT', NULL, NULL, NULL, 2, 5, '10.00', '999999.00', NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(4, 'bKash', 'bkash', 'wallet', 1, 1, 0, 1, 0, 'your_app_key', 'your_app_secret', NULL, 'your_merchant_id', NULL, NULL, NULL, 'https://tokenized.pay.bka.sh/v1.2.0-beta', 'http://127.0.0.1:8000/payment/bkash/callback', 'http://127.0.0.1:8000/api/v1/webhooks/bkash', '[\"BDT\"]', '[\"BD\"]', NULL, '1.50', '5.00', 'BDT', NULL, NULL, NULL, 1, 3, '10.00', '999999.00', NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(5, 'Nagad', 'nagad', 'wallet', 1, 1, 0, 1, 0, 'your_merchant_id', 'your_merchant_key', NULL, NULL, NULL, NULL, NULL, 'https://sandbox.mynagad.com', 'http://127.0.0.1:8000/payment/nagad/callback', 'http://127.0.0.1:8000/api/v1/webhooks/nagad', '[\"BDT\"]', '[\"BD\"]', NULL, '1.25', '5.00', 'BDT', NULL, NULL, NULL, 1, 3, '10.00', '999999.00', NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(6, 'Rocket', 'rocket', 'wallet', 1, 1, 0, 1, 0, 'your_merchant_id', 'your_merchant_key', NULL, NULL, NULL, NULL, NULL, 'https://api.rocket.com.bd', 'http://127.0.0.1:8000/payment/rocket/callback', 'http://127.0.0.1:8000/api/v1/webhooks/rocket', '[\"BDT\"]', '[\"BD\"]', NULL, '1.25', '5.00', 'BDT', NULL, NULL, NULL, 1, 3, '10.00', '999999.00', NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(7, 'Bank Transfer', 'bank_transfer', 'bank', 1, 0, 0, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[\"USD\", \"EUR\", \"GBP\"]', '[\"US\", \"GB\", \"DE\", \"FR\"]', NULL, '0.00', '0.00', 'USD', NULL, '{\"bank_name\": \"Chase Bank\", \"swift_code\": \"CHASUS33\", \"account_name\": \"Subscription Management Inc\", \"account_number\": \"343434343431234\", \"routing_number\": \"021000021\"}', NULL, 3, 7, '10.00', '999999.00', NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(8, 'Cash', 'cash', 'cash', 1, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[\"USD\", \"EUR\", \"GBP\", \"BDT\"]', '[\"US\", \"GB\", \"BD\"]', NULL, '0.00', '0.00', 'USD', NULL, NULL, NULL, 0, 0, '1.00', '999999.00', NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(9, 'SurjoPay', 'surjopay', 'aggregator', 1, 1, 0, 1, 0, 'your_merchant_key', 'your_api_secret', NULL, 'SP_MERCHANT_987654321', 'your_merchant_password', NULL, NULL, 'https://engine.surjopay.com', 'http://127.0.0.1:8000/payment/surjopay/callback', 'http://127.0.0.1:8000/payment/surjopay/ipn', '[\"BDT\", \"USD\"]', '[\"BD\"]', NULL, '2.00', '0.00', 'BDT', NULL, NULL, NULL, 2, 5, '10.00', '999999.00', NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payment_masters`
--

CREATE TABLE `payment_masters` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `payment_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Enum values: subscription, order, wallet_topup, refund, adjustment, bulk',
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft' COMMENT 'Enum values: draft, pending, processing, partially_paid, paid, failed, refunded, disputed, cancelled, expired',
  `total_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `subtotal` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `tax_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `discount_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `fee_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `net_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `paid_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `due_amount` decimal(20,8) GENERATED ALWAYS AS ((`total_amount` - `paid_amount`)) STORED,
  `currency` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `exchange_rate` decimal(12,6) NOT NULL DEFAULT '1.000000',
  `base_currency` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `base_amount` decimal(20,8) GENERATED ALWAYS AS ((`total_amount` * `exchange_rate`)) STORED,
  `payment_method` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Enum values: cash, bank_transfer, stripe, paypal, sslcommerz, card, bkash, nagad, rocket, google_pay, apple_pay, crypto, wallet, cheque, installment, custom',
  `payment_method_details` json DEFAULT NULL,
  `payment_gateway` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_installment` tinyint(1) NOT NULL DEFAULT '0',
  `installment_count` int DEFAULT NULL,
  `installment_frequency` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_date` timestamp NULL DEFAULT NULL,
  `due_date` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `customer_reference` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_reference` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_reference` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `custom_fields` json DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `failure_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Master payment records (can contain multiple items)';

--
-- Dumping data for table `payment_masters`
--

INSERT INTO `payment_masters` (`id`, `user_id`, `payment_number`, `type`, `status`, `total_amount`, `subtotal`, `tax_amount`, `discount_amount`, `fee_amount`, `net_amount`, `paid_amount`, `currency`, `exchange_rate`, `base_currency`, `payment_method`, `payment_method_details`, `payment_gateway`, `is_installment`, `installment_count`, `installment_frequency`, `payment_date`, `due_date`, `paid_at`, `cancelled_at`, `expires_at`, `customer_reference`, `bank_reference`, `gateway_reference`, `metadata`, `custom_fields`, `notes`, `failure_reason`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 3, 'PMT-20260201-21114', 'subscription', 'paid', '541.19587500', '541.19587500', '0.00000000', '0.00000000', '15.99468038', '525.20119462', '541.19587500', 'USD', '1.000000', 'USD', 'paypal', '{\"type\": \"paypal_account\", \"email\": \"user@example.com\"}', 'paypal', 0, NULL, NULL, '2026-02-01 07:55:09', '2026-02-01 07:55:09', '2026-02-01 07:55:09', NULL, NULL, NULL, NULL, 'pay_ZLAuklvUW9w1KewJm', '{\"invoice_id\": 1}', NULL, NULL, NULL, NULL, NULL, '2026-02-01 07:55:09', '2026-02-01 07:55:09', NULL),
(2, 2, 'PMT-20260206-77599', 'subscription', 'paid', '29.99000000', '29.99000000', '0.00000000', '0.00000000', '1.16971000', '28.82029000', '29.99000000', 'USD', '1.000000', 'USD', 'stripe', '{\"brand\": \"visa\", \"last4\": \"4242\"}', 'stripe', 0, NULL, NULL, '2026-02-06 07:55:09', '2026-02-06 07:55:09', '2026-02-06 07:55:09', NULL, NULL, NULL, NULL, 'ch_DtYB40IGEPxKfh', '{\"invoice_id\": 2}', NULL, NULL, NULL, NULL, NULL, '2026-02-06 07:55:09', '2026-02-06 07:55:09', NULL),
(3, 4, 'PMT-20250621-61054', 'subscription', 'paid', '299.99000000', '299.99000000', '0.00000000', '0.00000000', '8.99971000', '290.99029000', '299.99000000', 'USD', '1.000000', 'USD', 'stripe', '{\"brand\": \"visa\", \"last4\": \"4242\"}', 'stripe', 0, NULL, NULL, '2025-06-21 07:55:09', '2025-06-21 07:55:09', '2025-06-21 07:55:09', NULL, NULL, NULL, NULL, 'ch_gT4vJmdByLcuQv', '{\"invoice_id\": 3}', NULL, NULL, NULL, NULL, NULL, '2025-06-21 07:55:09', '2025-06-21 07:55:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Enum values: card, bank_account, digital_wallet, crypto_wallet, cash, custom',
  `gateway` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gateway_customer_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_payment_method_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nickname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `card_last4` char(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `card_brand` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `card_exp_month` int DEFAULT NULL,
  `card_exp_year` int DEFAULT NULL,
  `card_country` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_last4` char(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_routing_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wallet_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wallet_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `crypto_currency` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `crypto_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `encrypted_data` json DEFAULT NULL,
  `fingerprint` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User''s saved payment methods';

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `user_id`, `type`, `gateway`, `gateway_customer_id`, `gateway_payment_method_id`, `nickname`, `is_default`, `is_verified`, `card_last4`, `card_brand`, `card_exp_month`, `card_exp_year`, `card_country`, `bank_name`, `bank_account_last4`, `bank_account_type`, `bank_routing_number`, `wallet_type`, `wallet_number`, `crypto_currency`, `crypto_address`, `encrypted_data`, `fingerprint`, `is_compromised`, `metadata`, `gateway_metadata`, `verified_at`, `verified_by`, `last_used_at`, `usage_count`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 'card', 'stripe', 'cus_dhIYnPmz8frCf8', 'pm_FxhiiLRieTMxkg', 'Work Visa', 1, 1, '4242', 'visa', 12, 2026, 'US', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'fp_LE8WddhoOhFuLGew', 0, '{\"used_count\": 15}', '{\"cvc_check\": \"pass\"}', '2026-01-07 07:55:09', NULL, '2026-02-06 07:55:09', 15, NULL, NULL, '2026-01-07 07:55:09', '2026-02-21 07:55:09', NULL),
(2, 3, 'digital_wallet', 'paypal', 'paypal_cus_DwH7JSXG8T', NULL, 'PayPal', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'paypal', 'jane@example.com', NULL, NULL, NULL, 'fp_paypal_bPXR2mH3deRTS5iL', 0, '{\"used_count\": 8}', '{\"payer_status\": \"verified\"}', '2025-11-23 07:55:09', NULL, '2026-02-01 07:55:09', 8, NULL, NULL, '2025-11-23 07:55:09', '2026-02-21 07:55:09', NULL),
(3, 4, 'bank_account', 'bank_transfer', NULL, NULL, 'Company Account', 1, 0, NULL, NULL, NULL, NULL, NULL, 'Chase', '1234', 'checking', '021000021', NULL, NULL, NULL, NULL, NULL, 'fp_bank_iVojgTIMQTi6EGgp', 0, '{\"used_count\": 1}', NULL, NULL, NULL, '2025-06-21 07:55:09', 1, NULL, NULL, '2025-06-21 07:55:09', '2026-02-21 07:55:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payment_transactions`
--

CREATE TABLE `payment_transactions` (
  `id` int UNSIGNED NOT NULL,
  `payment_master_id` int UNSIGNED NOT NULL,
  `payment_child_id` int UNSIGNED DEFAULT NULL,
  `transaction_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'payment' COMMENT 'Enum values: payment, refund, chargeback, dispute, adjustment, reversal, settlement',
  `payment_method` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Enum values: cash, bank_transfer, stripe, paypal, sslcommerz, card, bkash, nagad, rocket, google_pay, apple_pay, crypto, wallet, cheque, installment',
  `payment_gateway` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gateway_response` json DEFAULT NULL,
  `payment_method_details` json DEFAULT NULL,
  `amount` decimal(20,8) NOT NULL,
  `fee` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `tax` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `net_amount` decimal(20,8) GENERATED ALWAYS AS ((`amount` - `fee`)) STORED,
  `currency` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `exchange_rate` decimal(12,6) NOT NULL DEFAULT '1.000000',
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'initiated' COMMENT 'Enum values: initiated, authorized, captured, pending, completed, failed, refunded, charged_back, disputed, cancelled, expired',
  `card_last4` char(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `card_brand` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `card_country` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `card_exp_month` int DEFAULT NULL,
  `card_exp_year` int DEFAULT NULL,
  `bank_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_last4` char(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_routing_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wallet_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wallet_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wallet_transaction_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `failure_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `location_data` json DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Individual payment transactions';

--
-- Dumping data for table `payment_transactions`
--

INSERT INTO `payment_transactions` (`id`, `payment_master_id`, `payment_child_id`, `transaction_id`, `reference_id`, `type`, `payment_method`, `payment_gateway`, `gateway_response`, `payment_method_details`, `amount`, `fee`, `tax`, `currency`, `exchange_rate`, `status`, `card_last4`, `card_brand`, `card_country`, `card_exp_month`, `card_exp_year`, `bank_name`, `bank_account_last4`, `bank_routing_number`, `wallet_type`, `wallet_number`, `wallet_transaction_id`, `installment_number`, `total_installments`, `initiated_at`, `authorized_at`, `captured_at`, `completed_at`, `failed_at`, `refunded_at`, `fraud_indicators`, `risk_score`, `requires_review`, `metadata`, `custom_fields`, `notes`, `failure_reason`, `ip_address`, `user_agent`, `location_data`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 'pay_iNlLzEsamzdi82v1Z', 'pay_ZLAuklvUW9w1KewJm', 'payment', 'paypal', 'paypal', '{\"id\": \"pay_ZLAuklvUW9w1KewJm\", \"status\": \"succeeded\"}', '{\"type\": \"paypal_account\", \"email\": \"user@example.com\"}', '541.19587500', '15.99468038', '0.00000000', 'USD', '1.000000', 'completed', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'paypal', 'user@example.com', 'PAY-DOUNHXTZ2P', NULL, NULL, '2026-02-01 07:55:09', '2026-02-01 07:55:09', '2026-02-01 07:55:09', '2026-02-01 07:55:09', NULL, NULL, NULL, '5.00', 0, NULL, NULL, NULL, NULL, '192.168.1.197', 'Mozilla/5.0', '{\"city\": \"New York\", \"country\": \"US\"}', NULL, NULL, '2026-02-01 07:55:09', '2026-02-01 07:55:09', NULL),
(2, 2, 2, 'txn_Z5PbJSBGTifLZV', 'ch_DtYB40IGEPxKfh', 'payment', 'stripe', 'stripe', '{\"id\": \"ch_DtYB40IGEPxKfh\", \"status\": \"succeeded\"}', '{\"brand\": \"visa\", \"last4\": \"4242\"}', '29.99000000', '1.16971000', '0.00000000', 'USD', '1.000000', 'completed', '4242', 'visa', 'US', 12, 2026, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-06 07:55:09', '2026-02-06 07:55:09', '2026-02-06 07:55:09', '2026-02-06 07:55:09', NULL, NULL, NULL, '5.00', 0, NULL, NULL, NULL, NULL, '192.168.1.3', 'Mozilla/5.0', '{\"city\": \"New York\", \"country\": \"US\"}', NULL, NULL, '2026-02-06 07:55:09', '2026-02-06 07:55:09', NULL),
(3, 3, 3, 'txn_2V9TCg6P0yqWDZ', 'ch_gT4vJmdByLcuQv', 'payment', 'stripe', 'stripe', '{\"id\": \"ch_gT4vJmdByLcuQv\", \"status\": \"succeeded\"}', '{\"brand\": \"visa\", \"last4\": \"4242\"}', '299.99000000', '8.99971000', '0.00000000', 'USD', '1.000000', 'completed', '4242', 'visa', 'US', 12, 2026, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-21 07:55:09', '2025-06-21 07:55:09', '2025-06-21 07:55:09', '2025-06-21 07:55:09', NULL, NULL, NULL, '5.00', 0, NULL, NULL, NULL, NULL, '192.168.1.53', 'Mozilla/5.0', '{\"city\": \"New York\", \"country\": \"US\"}', NULL, NULL, '2025-06-21 07:55:09', '2025-06-21 07:55:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payment_webhook_logs`
--

CREATE TABLE `payment_webhook_logs` (
  `id` int UNSIGNED NOT NULL,
  `payment_gateway_id` int UNSIGNED DEFAULT NULL,
  `gateway` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `webhook_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_transaction_id` int UNSIGNED DEFAULT NULL,
  `payload` json DEFAULT NULL,
  `headers` json DEFAULT NULL,
  `response_code` int DEFAULT NULL,
  `response_body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'received' COMMENT 'Enum values: received, processing, processed, failed, ignored',
  `processing_error` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `retry_count` int NOT NULL DEFAULT '0',
  `next_retry_at` timestamp NULL DEFAULT NULL,
  `received_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `processed_at` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `verification_error` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Logs of payment gateway webhooks';

--
-- Dumping data for table `payment_webhook_logs`
--

INSERT INTO `payment_webhook_logs` (`id`, `payment_gateway_id`, `gateway`, `event_type`, `webhook_id`, `reference_id`, `payment_transaction_id`, `payload`, `headers`, `response_code`, `response_body`, `status`, `processing_error`, `retry_count`, `next_retry_at`, `received_at`, `processed_at`, `ip_address`, `is_verified`, `verification_error`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 'paypal', 'PAYMENT.SALE.COMPLETED', 'WH-9cnkijodVmbbyTy3YVES', 'pay_ZLAuklvUW9w1KewJm', NULL, '{\"id\": \"evt_xsFzBjxOiqePpC\", \"type\": \"invoice.payment_succeeded\"}', '{\"paypal-auth-algo\": \"SHA256withRSA\"}', 200, 'Webhook processed successfully', 'processed', NULL, 0, NULL, '2026-02-01 07:55:09', '2026-02-01 07:55:09', '66.211.168.91', 1, NULL, NULL, NULL, '2026-02-01 07:55:09', '2026-02-01 07:55:09', NULL),
(2, 1, 'stripe', 'invoice.payment_succeeded', 'evt_NfLdgomTQMw5ql', 'ch_DtYB40IGEPxKfh', NULL, '{\"id\": \"evt_HVFjGMZa9VSL7X\", \"type\": \"invoice.payment_succeeded\"}', '{\"stripe-signature\": \"t=1771682109,v1=gpX0eamT5sa8lyDl9YxOUmY8P7ymarOAroj91Yk7\"}', 200, 'Webhook processed successfully', 'processed', NULL, 0, NULL, '2026-02-06 07:55:09', '2026-02-06 07:55:09', '54.187.174.169', 1, NULL, NULL, NULL, '2026-02-06 07:55:09', '2026-02-06 07:55:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE `plans` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'recurring' COMMENT 'Enum values: recurring, usage, one_time, hybrid',
  `billing_period` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'monthly' COMMENT 'Enum values: monthly, yearly, quarterly, weekly, daily',
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
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Subscription plans with pricing and features';

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`id`, `name`, `slug`, `code`, `description`, `type`, `billing_period`, `billing_interval`, `is_active`, `is_visible`, `sort_order`, `is_featured`, `metadata`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Free', 'free', 'FREE', 'Basic plan with limited features, perfect for getting started', 'recurring', 'monthly', 1, 1, 1, 1, 0, '{\"popular\": false, \"tagline\": \"Start for free\", \"highlight_color\": \"#gray\"}', NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(2, 'Starter', 'starter', 'STARTER', 'Perfect for small businesses and startups', 'recurring', 'monthly', 1, 1, 1, 2, 1, '{\"popular\": true, \"tagline\": \"Most popular\", \"highlight_color\": \"#blue\"}', NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(3, 'Professional', 'professional', 'PRO', 'For growing businesses with advanced needs', 'recurring', 'monthly', 1, 1, 1, 3, 0, '{\"popular\": false, \"tagline\": \"Advanced features\", \"highlight_color\": \"#purple\"}', NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(4, 'Enterprise', 'enterprise', 'ENTERPRISE', 'Advanced features for large organizations', 'recurring', 'yearly', 1, 1, 1, 4, 0, '{\"popular\": false, \"tagline\": \"For large teams\", \"highlight_color\": \"#gold\"}', NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(5, 'Pay As You Go', 'payg', 'PAYG', 'Usage-based pricing, pay only for what you use', 'usage', 'monthly', 1, 1, 1, 5, 0, '{\"popular\": false, \"tagline\": \"Flexible pricing\", \"highlight_color\": \"#green\"}', NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(6, 'Starter Yearly', 'starter-yearly', 'STARTER_YEARLY', 'Starter plan with yearly billing (save 20%)', 'recurring', 'yearly', 1, 1, 1, 6, 0, '{\"popular\": false, \"tagline\": \"Best value\", \"highlight_color\": \"#blue\", \"discount_percent\": 20}', NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(7, 'Professional Yearly', 'professional-yearly', 'PRO_YEARLY', 'Professional plan with yearly billing (save 20%)', 'recurring', 'yearly', 1, 1, 1, 7, 0, '{\"popular\": false, \"tagline\": \"Best value\", \"highlight_color\": \"#purple\", \"discount_percent\": 20}', NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `plan_discounts`
--

CREATE TABLE `plan_discounts` (
  `id` bigint UNSIGNED NOT NULL,
  `plan_id` int UNSIGNED NOT NULL,
  `discount_id` int UNSIGNED NOT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plan_features`
--

CREATE TABLE `plan_features` (
  `id` int UNSIGNED NOT NULL,
  `plan_id` int UNSIGNED NOT NULL,
  `feature_id` int UNSIGNED NOT NULL,
  `value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Features included in each plan with limits/config';

--
-- Dumping data for table `plan_features`
--

INSERT INTO `plan_features` (`id`, `plan_id`, `feature_id`, `value`, `config`, `sort_order`, `is_inherited`, `parent_feature_id`, `effective_from`, `effective_to`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, '1000', '{\"enabled\": true, \"rollover\": false}', 1, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(2, 1, 2, '1', '{\"enabled\": true, \"rollover\": false}', 2, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(3, 1, 3, '1', '{\"enabled\": true, \"rollover\": false}', 3, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(4, 1, 4, 'false', '{\"enabled\": true, \"rollover\": false}', 4, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(5, 1, 5, 'false', '{\"enabled\": true, \"rollover\": false}', 5, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(6, 1, 6, '0', '{\"enabled\": true, \"rollover\": false}', 6, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(7, 1, 7, 'false', '{\"enabled\": true, \"rollover\": false}', 7, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(8, 1, 8, '10', '{\"enabled\": true, \"rollover\": false}', 8, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(9, 2, 1, '10000', '{\"enabled\": true, \"rollover\": false}', 9, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(10, 2, 2, '10', '{\"enabled\": true, \"rollover\": false}', 10, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(11, 2, 3, '5', '{\"enabled\": true, \"rollover\": false}', 11, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(12, 2, 4, 'false', '{\"enabled\": true, \"rollover\": false}', 12, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(13, 2, 5, 'true', '{\"enabled\": true, \"rollover\": false}', 13, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(14, 2, 6, '3', '{\"enabled\": true, \"rollover\": false}', 14, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(15, 2, 7, 'true', '{\"enabled\": true, \"rollover\": false}', 15, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(16, 2, 8, '50', '{\"enabled\": true, \"rollover\": false}', 16, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(17, 3, 1, '100000', '{\"enabled\": true, \"rollover\": false}', 17, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(18, 3, 2, '100', '{\"enabled\": true, \"rollover\": false}', 18, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(19, 3, 3, '20', '{\"enabled\": true, \"rollover\": false}', 19, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(20, 3, 4, 'true', '{\"enabled\": true, \"rollover\": false}', 20, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(21, 3, 5, 'true', '{\"enabled\": true, \"rollover\": false}', 21, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(22, 3, 6, '10', '{\"enabled\": true, \"rollover\": false}', 22, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(23, 3, 7, 'true', '{\"enabled\": true, \"rollover\": false}', 23, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(24, 3, 8, '200', '{\"enabled\": true, \"rollover\": false}', 24, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(25, 4, 1, 'unlimited', '{\"enabled\": true, \"rollover\": false}', 25, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(26, 4, 2, '1000', '{\"enabled\": true, \"rollover\": false}', 26, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(27, 4, 3, 'unlimited', '{\"enabled\": true, \"rollover\": false}', 27, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(28, 4, 4, 'true', '{\"enabled\": true, \"rollover\": false}', 28, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(29, 4, 5, 'true', '{\"enabled\": true, \"rollover\": false}', 29, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(30, 4, 6, 'unlimited', '{\"enabled\": true, \"rollover\": false}', 30, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(31, 4, 7, 'true', '{\"enabled\": true, \"rollover\": false}', 31, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(32, 4, 8, '1000', '{\"enabled\": true, \"rollover\": false}', 32, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(33, 5, 1, 'unlimited', '{\"enabled\": true, \"rollover\": false}', 33, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(34, 5, 2, 'unlimited', '{\"enabled\": true, \"rollover\": false}', 34, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(35, 5, 3, 'unlimited', '{\"enabled\": true, \"rollover\": false}', 35, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(36, 5, 4, 'false', '{\"enabled\": true, \"rollover\": false}', 36, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(37, 5, 5, 'true', '{\"enabled\": true, \"rollover\": false}', 37, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(38, 5, 6, 'unlimited', '{\"enabled\": true, \"rollover\": false}', 38, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(39, 5, 7, 'true', '{\"enabled\": true, \"rollover\": false}', 39, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(40, 5, 8, '100', '{\"enabled\": true, \"rollover\": false}', 40, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(41, 6, 1, '10000', '{\"enabled\": true, \"rollover\": false}', 41, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(42, 6, 2, '10', '{\"enabled\": true, \"rollover\": false}', 42, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(43, 6, 3, '5', '{\"enabled\": true, \"rollover\": false}', 43, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(44, 6, 4, 'false', '{\"enabled\": true, \"rollover\": false}', 44, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(45, 6, 5, 'true', '{\"enabled\": true, \"rollover\": false}', 45, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(46, 6, 6, '3', '{\"enabled\": true, \"rollover\": false}', 46, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(47, 6, 7, 'true', '{\"enabled\": true, \"rollover\": false}', 47, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(48, 6, 8, '50', '{\"enabled\": true, \"rollover\": false}', 48, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(49, 7, 1, '100000', '{\"enabled\": true, \"rollover\": false}', 49, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(50, 7, 2, '100', '{\"enabled\": true, \"rollover\": false}', 50, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(51, 7, 3, '20', '{\"enabled\": true, \"rollover\": false}', 51, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(52, 7, 4, 'true', '{\"enabled\": true, \"rollover\": false}', 52, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(53, 7, 5, 'true', '{\"enabled\": true, \"rollover\": false}', 53, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(54, 7, 6, '10', '{\"enabled\": true, \"rollover\": false}', 54, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(55, 7, 7, 'true', '{\"enabled\": true, \"rollover\": false}', 55, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(56, 7, 8, '200', '{\"enabled\": true, \"rollover\": false}', 56, 0, NULL, '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(57, 2, 4, 'true', '{\"enabled\": true}', 99, 0, NULL, '2025-08-21 07:55:09', '2025-11-21 07:55:09', NULL, NULL, '2025-08-21 07:55:09', '2025-11-21 07:55:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `plan_prices`
--

CREATE TABLE `plan_prices` (
  `id` int UNSIGNED NOT NULL,
  `plan_id` int UNSIGNED NOT NULL,
  `currency` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `amount` decimal(20,8) NOT NULL,
  `interval` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Enum values: month, year, quarter, week, day',
  `interval_count` int NOT NULL DEFAULT '1',
  `usage_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'licensed' COMMENT 'Enum values: licensed, metered, tiered',
  `tiers` json DEFAULT NULL,
  `transformations` json DEFAULT NULL,
  `stripe_price_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_from` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active_to` timestamp NULL DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Pricing details for plans in different currencies';

--
-- Dumping data for table `plan_prices`
--

INSERT INTO `plan_prices` (`id`, `plan_id`, `currency`, `amount`, `interval`, `interval_count`, `usage_type`, `tiers`, `transformations`, `stripe_price_id`, `active_from`, `active_to`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'USD', '0.00000000', 'month', 1, 'licensed', NULL, NULL, 'price_free_month', '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(2, 2, 'USD', '29.99000000', 'month', 1, 'licensed', NULL, NULL, 'price_starter_month', '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(3, 3, 'USD', '99.99000000', 'month', 1, 'licensed', NULL, NULL, 'price_pro_month', '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(4, 4, 'USD', '299.99000000', 'year', 1, 'licensed', NULL, NULL, 'price_enterprise_year', '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(5, 5, 'USD', '0.00000000', 'month', 1, 'metered', '[{\"last\": 1000000, \"unit\": \"api_request\", \"first\": 0, \"price\": 0.0001}, {\"last\": null, \"unit\": \"api_request\", \"first\": 1000001, \"price\": 0.00005}, {\"last\": null, \"unit\": \"storage_gb\", \"first\": 0, \"price\": 0.1}]', '{\"round\": \"up\", \"multiply\": 1}', 'price_payg_month', '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(6, 6, 'USD', '287.90000000', 'year', 1, 'licensed', NULL, NULL, 'price_starter_yearly_year', '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(7, 7, 'USD', '959.90000000', 'year', 1, 'licensed', NULL, NULL, 'price_pro_yearly_year', '2026-02-21 07:55:09', NULL, NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rate_limits`
--

CREATE TABLE `rate_limits` (
  `id` int UNSIGNED NOT NULL,
  `subscription_id` int UNSIGNED NOT NULL,
  `feature_id` int UNSIGNED NOT NULL,
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `max_attempts` int NOT NULL,
  `decay_seconds` int NOT NULL,
  `remaining` int NOT NULL,
  `resets_at` timestamp NOT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Rate limiting for subscription features';

--
-- Dumping data for table `rate_limits`
--

INSERT INTO `rate_limits` (`id`, `subscription_id`, `feature_id`, `key`, `max_attempts`, `decay_seconds`, `remaining`, `resets_at`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 4, 8, 'api_requests:4', 100, 60, 60, '2026-02-21 08:25:09', NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(2, 2, 8, 'api_requests:2', 100, 60, 96, '2026-02-21 08:25:09', NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(3, 1, 8, 'api_requests:1', 100, 60, 47, '2026-02-21 08:44:09', NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(4, 3, 8, 'api_requests:3', 100, 60, 34, '2026-02-21 08:06:09', NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 07:55:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `refunds`
--

CREATE TABLE `refunds` (
  `id` int UNSIGNED NOT NULL,
  `payment_master_id` int UNSIGNED NOT NULL,
  `payment_transaction_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `refund_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Enum values: full, partial, chargeback, dispute',
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'requested' COMMENT 'Enum values: requested, approved, processing, completed, failed, rejected',
  `initiated_by` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer' COMMENT 'Enum values: customer, merchant, gateway, system',
  `amount` decimal(20,8) NOT NULL,
  `fee` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `net_amount` decimal(20,8) GENERATED ALWAYS AS ((`amount` - `fee`)) STORED,
  `currency` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `exchange_rate` decimal(12,6) NOT NULL DEFAULT '1.000000',
  `reason` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other' COMMENT 'Enum values: duplicate, fraudulent, requested_by_customer, credit_not_processed, goods_not_received, goods_defective, subscription_cancelled, other',
  `reason_details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `customer_comments` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `requested_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` int UNSIGNED DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `failed_at` timestamp NULL DEFAULT NULL,
  `gateway_refund_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_response` json DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `documents` json DEFAULT NULL,
  `processed_by` int UNSIGNED DEFAULT NULL,
  `rejection_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Refund records';

--
-- Dumping data for table `refunds`
--

INSERT INTO `refunds` (`id`, `payment_master_id`, `payment_transaction_id`, `user_id`, `refund_number`, `type`, `status`, `initiated_by`, `amount`, `fee`, `currency`, `exchange_rate`, `reason`, `reason_details`, `customer_comments`, `requested_at`, `approved_at`, `approved_by`, `processed_at`, `completed_at`, `failed_at`, `gateway_refund_id`, `gateway_response`, `metadata`, `documents`, `processed_by`, `rejection_reason`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 3, 'REF-20260221-40809', 'partial', 'completed', 'customer', '10.00000000', '0.00000000', 'USD', '1.000000', 'requested_by_customer', 'Customer requested partial refund due to service issue', 'Had some downtime last week', '2026-02-11 07:55:09', '2026-02-12 07:55:09', NULL, '2026-02-13 07:55:09', '2026-02-13 07:55:09', NULL, 're_OIgn85AMo42C1R', '{\"id\": \"re_zDYRWf6U6lPpgn\", \"status\": \"succeeded\"}', '{\"reason_code\": \"service_issue\"}', NULL, NULL, NULL, NULL, NULL, '2026-02-11 07:55:09', '2026-02-13 07:55:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('GeN9Ete42pc5AL2rcWvqqyUxb6CHdAhxK4pP0jqe', 1, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoic2N1UDZZNGRqYm4zd1N0TlZQMzJHSzdOY2tySUlaaU5iQWJjNUdMcCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQvdXNhZ2UiO3M6NToicm91dGUiO3M6MjM6IndlYnNpdGUuZGFzaGJvYXJkLnVzYWdlIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjIxOiJwYXNzd29yZF9oYXNoX3NhbmN0dW0iO3M6NjQ6ImI1NTRlYzQ3OTc2MGI4NGMwNzY1NmU2YjZhNTRiMzEwOTVkMDNiN2Y1ZjM0M2I2YTYwOWNiNzBjZTYwNmY2NDQiO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiJiNTU0ZWM0Nzk3NjBiODRjMDc2NTZlNmI2YTU0YjMxMDk1ZDAzYjdmNWYzNDNiNmE2MDljYjcwY2U2MDZmNjQ0Ijt9', 1771689724),
('Gq3ntzoACzs8NHHwpyQF3w6wFc1GQtzG2HLv2NZ7', 2, '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiQVVaOWozVGZTZWZlbW5XZEswMlZwamlFNHpNdFJ0OEhQUG9TeHRuciI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jaGVja291dC8yP3ByaWNlX2lkPTIiO3M6NToicm91dGUiO3M6MjI6IndlYnNpdGUuY2hlY2tvdXQuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO3M6MjE6InBhc3N3b3JkX2hhc2hfc2FuY3R1bSI7czo2NDoiOTcwNmU0MWQ0ZGJjMDU0MmFjZWM4OTVkOTRiNzJhNTc2MmZjN2FmODFjNjE0MGIyYWQ2NGQzZDMxNjVlODA1MSI7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjQ6Ijk3MDZlNDFkNGRiYzA1NDJhY2VjODk1ZDk0YjcyYTU3NjJmYzdhZjgxYzYxNDBiMmFkNjRkM2QzMTY1ZTgwNTEiO30=', 1771689909);

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `plan_id` int UNSIGNED NOT NULL,
  `plan_price_id` int UNSIGNED NOT NULL,
  `parent_subscription_id` int UNSIGNED DEFAULT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'trialing' COMMENT 'Enum values: active, trialing, past_due, canceled, unpaid, incomplete, incomplete_expired, paused, suspended',
  `billing_cycle_anchor` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'creation' COMMENT 'Enum values: creation, billing_cycle',
  `quantity` int NOT NULL DEFAULT '1',
  `unit_price` decimal(20,8) NOT NULL,
  `amount` decimal(20,8) NOT NULL,
  `currency` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `trial_starts_at` timestamp NULL DEFAULT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `trial_converted` tinyint(1) NOT NULL DEFAULT '0',
  `current_period_starts_at` timestamp NULL DEFAULT NULL,
  `current_period_ends_at` timestamp NULL DEFAULT NULL,
  `billing_cycle_anchor_date` timestamp NULL DEFAULT NULL,
  `canceled_at` timestamp NULL DEFAULT NULL,
  `cancellation_reason` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Enum values: customer, payment_failed, fraud, business, upgrade, downgrade, other',
  `prorate` tinyint(1) NOT NULL DEFAULT '1',
  `proration_amount` decimal(20,8) DEFAULT NULL,
  `proration_date` timestamp NULL DEFAULT NULL,
  `gateway` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'stripe',
  `gateway_subscription_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_customer_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_metadata` json DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `history` json DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User subscriptions to plans';

--
-- Dumping data for table `subscriptions`
--

INSERT INTO `subscriptions` (`id`, `user_id`, `plan_id`, `plan_price_id`, `parent_subscription_id`, `status`, `billing_cycle_anchor`, `quantity`, `unit_price`, `amount`, `currency`, `trial_starts_at`, `trial_ends_at`, `trial_converted`, `current_period_starts_at`, `current_period_ends_at`, `billing_cycle_anchor_date`, `canceled_at`, `cancellation_reason`, `prorate`, `proration_amount`, `proration_date`, `gateway`, `gateway_subscription_id`, `gateway_customer_id`, `gateway_metadata`, `metadata`, `history`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `is_active`) VALUES
(1, 2, 2, 2, NULL, 'active', 'creation', 1, '29.99000000', '29.99000000', 'USD', NULL, NULL, 0, '2026-02-06 07:55:09', '2026-03-08 07:55:09', '2026-02-06 07:55:09', NULL, NULL, 1, NULL, NULL, 'stripe', 'sub_MQHFT32QlS2iSD', 'cus_OcoobiE3EaI0aY', '{\"payment_method\": \"pm_card_visa\"}', '{\"source\": \"web\", \"auto_renew\": true}', '[{\"date\": \"2026-01-07T13:55:09+00:00\", \"event\": \"created\"}, {\"date\": \"2026-02-06T13:55:09+00:00\", \"event\": \"payment_succeeded\"}]', NULL, NULL, '2026-01-07 07:55:09', '2026-02-21 07:55:09', NULL, 1),
(2, 3, 3, 3, NULL, 'active', 'creation', 5, '99.99000000', '499.95000000', 'USD', '2025-11-23 07:55:09', '2025-11-30 07:55:09', 1, '2026-02-01 07:55:09', '2026-03-03 07:55:09', '2025-11-30 07:55:09', NULL, NULL, 1, NULL, NULL, 'paypal', 'I-OTDKAVGCUY', 'paypal_cus_HwQPqBsIIi', '{\"payer_id\": \"PAYER_cZ0s4QQ0tb\"}', '{\"source\": \"referral\", \"referral_code\": \"FRIEND10\"}', '[{\"date\": \"2025-11-23T13:55:09+00:00\", \"event\": \"created\"}, {\"date\": \"2025-11-23T13:55:09+00:00\", \"event\": \"trial_started\"}, {\"date\": \"2025-11-30T13:55:09+00:00\", \"event\": \"converted\"}, {\"date\": \"2026-02-01T13:55:09+00:00\", \"event\": \"payment_succeeded\"}]', NULL, NULL, '2025-11-23 07:55:09', '2026-02-21 07:55:09', NULL, 1),
(3, 4, 4, 4, NULL, 'active', 'creation', 1, '299.99000000', '299.99000000', 'USD', NULL, NULL, 0, '2025-06-21 07:55:09', '2026-06-21 07:55:09', '2025-06-21 07:55:09', NULL, NULL, 1, NULL, NULL, 'bank_transfer', NULL, NULL, NULL, '{\"source\": \"sales\", \"account_manager\": \"sarah@company.com\"}', '[{\"date\": \"2025-06-21T13:55:09+00:00\", \"event\": \"created\"}, {\"date\": \"2025-06-21T13:55:09+00:00\", \"event\": \"payment_succeeded\"}]', NULL, NULL, '2025-06-21 07:55:09', '2026-02-21 07:55:09', NULL, 1),
(4, 5, 5, 5, NULL, 'active', 'creation', 1, '0.00000000', '0.00000000', 'USD', '2026-01-22 07:55:09', '2026-01-29 07:55:09', 1, '2026-01-29 07:55:09', '2026-02-28 07:55:09', '2026-01-29 07:55:09', NULL, NULL, 1, NULL, NULL, 'stripe', 'sub_JcJViUMxT5qEOI', 'cus_yrFn7kwHG1QZOO', '{\"payment_method\": \"pm_card_visa\"}', '{\"source\": \"web\"}', '[{\"date\": \"2026-01-22T13:55:09+00:00\", \"event\": \"created\"}, {\"date\": \"2026-01-22T13:55:09+00:00\", \"event\": \"trial_started\"}, {\"date\": \"2026-01-29T13:55:09+00:00\", \"event\": \"converted\"}]', NULL, NULL, '2026-01-22 07:55:09', '2026-02-21 07:55:09', NULL, 1),
(5, 6, 1, 1, NULL, 'canceled', 'creation', 1, '0.00000000', '0.00000000', 'USD', NULL, NULL, 0, '2025-08-21 07:55:09', '2025-09-21 07:55:09', '2025-08-21 07:55:09', '2025-09-21 07:55:09', 'customer', 1, NULL, NULL, 'stripe', 'sub_iSuVpF12XMbDiI', 'cus_UHEhkTpcuxJSHY', '{\"payment_method\": \"pm_card_visa\"}', '{\"source\": \"web\", \"cancellation_reason\": \"too_expensive\"}', '[{\"date\": \"2025-08-21T13:55:09+00:00\", \"event\": \"created\"}, {\"date\": \"2025-09-21T13:55:09+00:00\", \"event\": \"canceled\"}]', NULL, NULL, '2025-08-21 07:55:09', '2025-09-21 07:55:09', NULL, 0),
(6, 1, 1, 1, NULL, 'trialing', 'creation', 1, '0.00000000', '0.00000000', 'USD', '2026-02-16 07:55:09', '2026-03-02 07:55:09', 0, '2026-02-16 07:55:09', '2026-03-02 07:55:09', '2026-02-16 07:55:09', NULL, NULL, 1, NULL, NULL, 'stripe', 'sub_qTUoAUs72tu4fM', 'cus_y3Azt7R6SFZBLo', '{\"payment_method\": \"pm_card_visa\"}', '{\"source\": \"web\"}', '[{\"date\": \"2026-02-16T13:55:09+00:00\", \"event\": \"created\"}, {\"date\": \"2026-02-16T13:55:09+00:00\", \"event\": \"trial_started\"}]', NULL, NULL, '2026-02-16 07:55:09', '2026-02-21 07:55:09', NULL, 1);

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
-- Table structure for table `subscription_events`
--

CREATE TABLE `subscription_events` (
  `id` int UNSIGNED NOT NULL,
  `subscription_id` int UNSIGNED NOT NULL,
  `type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Enum values: created, updated, canceled, reactivated, plan_changed, quantity_changed, trial_started, trial_ended, invoice_created, payment_succeeded, payment_failed, usage_recorded, downgrade_scheduled',
  `data` json DEFAULT NULL,
  `changes` json DEFAULT NULL,
  `causer_id` int UNSIGNED DEFAULT NULL,
  `causer_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL,
  `occurred_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Audit log for subscription changes';

--
-- Dumping data for table `subscription_events`
--

INSERT INTO `subscription_events` (`id`, `subscription_id`, `type`, `data`, `changes`, `causer_id`, `causer_type`, `ip_address`, `user_agent`, `metadata`, `occurred_at`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'created', '{\"plan\": 2, \"amount\": \"29.99000000\"}', '{\"status\": [\"null\", \"active\"]}', 2, 'user', '192.168.1.59', 'Mozilla/5.0', '{\"source\": \"web\"}', '2026-01-07 07:55:09', NULL, NULL, '2026-01-07 07:55:09', '2026-01-07 07:55:09', NULL),
(2, 2, 'created', '{\"plan\": 3, \"amount\": \"499.95000000\"}', '{\"status\": [\"null\", \"active\"]}', 3, 'user', '192.168.1.225', 'Mozilla/5.0', '{\"source\": \"web\"}', '2025-11-23 07:55:09', NULL, NULL, '2025-11-23 07:55:09', '2025-11-23 07:55:09', NULL),
(3, 2, 'trial_started', '{\"trial_end\": \"2025-11-30 13:55:09\"}', NULL, 3, 'user', '192.168.1.146', 'Mozilla/5.0', NULL, '2025-11-23 07:55:09', NULL, NULL, '2025-11-23 07:55:09', '2025-11-23 07:55:09', NULL),
(4, 2, 'trial_ended', '{\"converted\": true}', '{\"status\": [\"trialing\", \"active\"]}', NULL, 'system', NULL, NULL, NULL, '2025-11-30 07:55:09', NULL, NULL, '2025-11-30 07:55:09', '2025-11-30 07:55:09', NULL),
(5, 3, 'created', '{\"plan\": 4, \"amount\": \"299.99000000\"}', '{\"status\": [\"null\", \"active\"]}', 4, 'user', '192.168.1.43', 'Mozilla/5.0', '{\"source\": \"web\"}', '2025-06-21 07:55:09', NULL, NULL, '2025-06-21 07:55:09', '2025-06-21 07:55:09', NULL),
(6, 4, 'created', '{\"plan\": 5, \"amount\": \"0.00000000\"}', '{\"status\": [\"null\", \"active\"]}', 5, 'user', '192.168.1.214', 'Mozilla/5.0', '{\"source\": \"web\"}', '2026-01-22 07:55:09', NULL, NULL, '2026-01-22 07:55:09', '2026-01-22 07:55:09', NULL),
(7, 4, 'trial_started', '{\"trial_end\": \"2026-01-29 13:55:09\"}', NULL, 5, 'user', '192.168.1.245', 'Mozilla/5.0', NULL, '2026-01-22 07:55:09', NULL, NULL, '2026-01-22 07:55:09', '2026-01-22 07:55:09', NULL),
(8, 4, 'trial_ended', '{\"converted\": true}', '{\"status\": [\"trialing\", \"active\"]}', NULL, 'system', NULL, NULL, NULL, '2026-01-29 07:55:09', NULL, NULL, '2026-01-29 07:55:09', '2026-01-29 07:55:09', NULL),
(9, 5, 'created', '{\"plan\": 1, \"amount\": \"0.00000000\"}', '{\"status\": [\"null\", \"canceled\"]}', 6, 'user', '192.168.1.201', 'Mozilla/5.0', '{\"source\": \"web\"}', '2025-08-21 07:55:09', NULL, NULL, '2025-08-21 07:55:09', '2025-08-21 07:55:09', NULL),
(10, 5, 'canceled', '{\"reason\": \"customer\"}', '{\"status\": [\"active\", \"canceled\"]}', 6, 'user', '192.168.1.178', 'Mozilla/5.0', NULL, '2025-09-21 07:55:09', NULL, NULL, '2025-09-21 07:55:09', '2025-09-21 07:55:09', NULL),
(11, 6, 'created', '{\"plan\": 1, \"amount\": \"0.00000000\"}', '{\"status\": [\"null\", \"trialing\"]}', 1, 'user', '192.168.1.80', 'Mozilla/5.0', '{\"source\": \"web\"}', '2026-02-16 07:55:09', NULL, NULL, '2026-02-16 07:55:09', '2026-02-16 07:55:09', NULL),
(12, 6, 'trial_started', '{\"trial_end\": \"2026-03-02 13:55:09\"}', NULL, 1, 'user', '192.168.1.237', 'Mozilla/5.0', NULL, '2026-02-16 07:55:09', NULL, NULL, '2026-02-16 07:55:09', '2026-02-16 07:55:09', NULL),
(13, 2, 'invoice_created', '{\"amount\": \"541.19587500\", \"invoice_id\": 1}', NULL, NULL, 'system', NULL, NULL, NULL, '2026-02-01 07:55:09', NULL, NULL, '2026-02-01 07:55:09', '2026-02-01 07:55:09', NULL),
(14, 2, 'payment_succeeded', '{\"amount\": \"541.19587500\", \"invoice_id\": 1}', NULL, NULL, 'system', NULL, NULL, NULL, '2026-02-01 07:55:09', NULL, NULL, '2026-02-01 07:55:09', '2026-02-01 07:55:09', NULL),
(15, 1, 'invoice_created', '{\"amount\": \"29.99000000\", \"invoice_id\": 2}', NULL, NULL, 'system', NULL, NULL, NULL, '2026-02-06 07:55:09', NULL, NULL, '2026-02-06 07:55:09', '2026-02-06 07:55:09', NULL),
(16, 1, 'payment_succeeded', '{\"amount\": \"29.99000000\", \"invoice_id\": 2}', NULL, NULL, 'system', NULL, NULL, NULL, '2026-02-06 07:55:09', NULL, NULL, '2026-02-06 07:55:09', '2026-02-06 07:55:09', NULL),
(17, 3, 'invoice_created', '{\"amount\": \"299.99000000\", \"invoice_id\": 3}', NULL, NULL, 'system', NULL, NULL, NULL, '2025-06-21 07:55:09', NULL, NULL, '2025-06-21 07:55:09', '2025-06-21 07:55:09', NULL),
(18, 3, 'payment_succeeded', '{\"amount\": \"299.99000000\", \"invoice_id\": 3}', NULL, NULL, 'system', NULL, NULL, NULL, '2025-06-21 07:55:09', NULL, NULL, '2025-06-21 07:55:09', '2025-06-21 07:55:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subscription_items`
--

CREATE TABLE `subscription_items` (
  `id` int UNSIGNED NOT NULL,
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
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Individual items within a subscription';

--
-- Dumping data for table `subscription_items`
--

INSERT INTO `subscription_items` (`id`, `subscription_id`, `plan_price_id`, `feature_id`, `quantity`, `unit_price`, `amount`, `metadata`, `tiers`, `effective_from`, `effective_to`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 2, 1, 1, '3.74875000', '3.74875000', '{\"included\": true}', NULL, '2026-02-06 07:55:09', NULL, NULL, NULL, '2026-01-07 07:55:09', '2026-02-21 07:55:09', NULL),
(2, 1, 2, 2, 1, '3.74875000', '3.74875000', '{\"included\": true}', NULL, '2026-02-06 07:55:09', NULL, NULL, NULL, '2026-01-07 07:55:09', '2026-02-21 07:55:09', NULL),
(3, 1, 2, 3, 1, '3.74875000', '3.74875000', '{\"included\": true}', NULL, '2026-02-06 07:55:09', NULL, NULL, NULL, '2026-01-07 07:55:09', '2026-02-21 07:55:09', NULL),
(4, 1, 2, 4, 1, '3.74875000', '3.74875000', '{\"included\": true}', NULL, '2026-02-06 07:55:09', NULL, NULL, NULL, '2026-01-07 07:55:09', '2026-02-21 07:55:09', NULL),
(5, 1, 2, 5, 1, '3.74875000', '3.74875000', '{\"included\": true}', NULL, '2026-02-06 07:55:09', NULL, NULL, NULL, '2026-01-07 07:55:09', '2026-02-21 07:55:09', NULL),
(6, 1, 2, 6, 1, '3.74875000', '3.74875000', '{\"included\": true}', NULL, '2026-02-06 07:55:09', NULL, NULL, NULL, '2026-01-07 07:55:09', '2026-02-21 07:55:09', NULL),
(7, 1, 2, 7, 1, '3.74875000', '3.74875000', '{\"included\": true}', NULL, '2026-02-06 07:55:09', NULL, NULL, NULL, '2026-01-07 07:55:09', '2026-02-21 07:55:09', NULL),
(8, 1, 2, 8, 1, '3.74875000', '3.74875000', '{\"included\": true}', NULL, '2026-02-06 07:55:09', NULL, NULL, NULL, '2026-01-07 07:55:09', '2026-02-21 07:55:09', NULL),
(9, 2, 3, 1, 5, '12.49875000', '62.49375000', '{\"included\": true}', NULL, '2026-02-01 07:55:09', NULL, NULL, NULL, '2025-11-23 07:55:09', '2026-02-21 07:55:09', NULL),
(10, 2, 3, 2, 5, '12.49875000', '62.49375000', '{\"included\": true}', NULL, '2026-02-01 07:55:09', NULL, NULL, NULL, '2025-11-23 07:55:09', '2026-02-21 07:55:09', NULL),
(11, 2, 3, 3, 5, '12.49875000', '62.49375000', '{\"included\": true}', NULL, '2026-02-01 07:55:09', NULL, NULL, NULL, '2025-11-23 07:55:09', '2026-02-21 07:55:09', NULL),
(12, 2, 3, 4, 5, '12.49875000', '62.49375000', '{\"included\": true}', NULL, '2026-02-01 07:55:09', NULL, NULL, NULL, '2025-11-23 07:55:09', '2026-02-21 07:55:09', NULL),
(13, 2, 3, 5, 5, '12.49875000', '62.49375000', '{\"included\": true}', NULL, '2026-02-01 07:55:09', NULL, NULL, NULL, '2025-11-23 07:55:09', '2026-02-21 07:55:09', NULL),
(14, 2, 3, 6, 5, '12.49875000', '62.49375000', '{\"included\": true}', NULL, '2026-02-01 07:55:09', NULL, NULL, NULL, '2025-11-23 07:55:09', '2026-02-21 07:55:09', NULL),
(15, 2, 3, 7, 5, '12.49875000', '62.49375000', '{\"included\": true}', NULL, '2026-02-01 07:55:09', NULL, NULL, NULL, '2025-11-23 07:55:09', '2026-02-21 07:55:09', NULL),
(16, 2, 3, 8, 5, '12.49875000', '62.49375000', '{\"included\": true}', NULL, '2026-02-01 07:55:09', NULL, NULL, NULL, '2025-11-23 07:55:09', '2026-02-21 07:55:09', NULL),
(17, 3, 4, 1, 1, '37.49875000', '37.49875000', '{\"included\": true}', NULL, '2025-06-21 07:55:09', NULL, NULL, NULL, '2025-06-21 07:55:09', '2026-02-21 07:55:09', NULL),
(18, 3, 4, 2, 1, '37.49875000', '37.49875000', '{\"included\": true}', NULL, '2025-06-21 07:55:09', NULL, NULL, NULL, '2025-06-21 07:55:09', '2026-02-21 07:55:09', NULL),
(19, 3, 4, 3, 1, '37.49875000', '37.49875000', '{\"included\": true}', NULL, '2025-06-21 07:55:09', NULL, NULL, NULL, '2025-06-21 07:55:09', '2026-02-21 07:55:09', NULL),
(20, 3, 4, 4, 1, '37.49875000', '37.49875000', '{\"included\": true}', NULL, '2025-06-21 07:55:09', NULL, NULL, NULL, '2025-06-21 07:55:09', '2026-02-21 07:55:09', NULL),
(21, 3, 4, 5, 1, '37.49875000', '37.49875000', '{\"included\": true}', NULL, '2025-06-21 07:55:09', NULL, NULL, NULL, '2025-06-21 07:55:09', '2026-02-21 07:55:09', NULL),
(22, 3, 4, 6, 1, '37.49875000', '37.49875000', '{\"included\": true}', NULL, '2025-06-21 07:55:09', NULL, NULL, NULL, '2025-06-21 07:55:09', '2026-02-21 07:55:09', NULL),
(23, 3, 4, 7, 1, '37.49875000', '37.49875000', '{\"included\": true}', NULL, '2025-06-21 07:55:09', NULL, NULL, NULL, '2025-06-21 07:55:09', '2026-02-21 07:55:09', NULL),
(24, 3, 4, 8, 1, '37.49875000', '37.49875000', '{\"included\": true}', NULL, '2025-06-21 07:55:09', NULL, NULL, NULL, '2025-06-21 07:55:09', '2026-02-21 07:55:09', NULL),
(25, 4, 5, 1, 1, '0.00000000', '0.00000000', '{\"included\": true}', NULL, '2026-01-29 07:55:09', NULL, NULL, NULL, '2026-01-22 07:55:09', '2026-02-21 07:55:09', NULL),
(26, 4, 5, 2, 1, '0.00000000', '0.00000000', '{\"included\": true}', NULL, '2026-01-29 07:55:09', NULL, NULL, NULL, '2026-01-22 07:55:09', '2026-02-21 07:55:09', NULL),
(27, 4, 5, 3, 1, '0.00000000', '0.00000000', '{\"included\": true}', NULL, '2026-01-29 07:55:09', NULL, NULL, NULL, '2026-01-22 07:55:09', '2026-02-21 07:55:09', NULL),
(28, 4, 5, 4, 1, '0.00000000', '0.00000000', '{\"included\": true}', NULL, '2026-01-29 07:55:09', NULL, NULL, NULL, '2026-01-22 07:55:09', '2026-02-21 07:55:09', NULL),
(29, 4, 5, 5, 1, '0.00000000', '0.00000000', '{\"included\": true}', NULL, '2026-01-29 07:55:09', NULL, NULL, NULL, '2026-01-22 07:55:09', '2026-02-21 07:55:09', NULL),
(30, 4, 5, 6, 1, '0.00000000', '0.00000000', '{\"included\": true}', NULL, '2026-01-29 07:55:09', NULL, NULL, NULL, '2026-01-22 07:55:09', '2026-02-21 07:55:09', NULL),
(31, 4, 5, 7, 1, '0.00000000', '0.00000000', '{\"included\": true}', NULL, '2026-01-29 07:55:09', NULL, NULL, NULL, '2026-01-22 07:55:09', '2026-02-21 07:55:09', NULL),
(32, 4, 5, 8, 1, '0.00000000', '0.00000000', '{\"included\": true}', NULL, '2026-01-29 07:55:09', NULL, NULL, NULL, '2026-01-22 07:55:09', '2026-02-21 07:55:09', NULL),
(33, 5, 1, 1, 1, '0.00000000', '0.00000000', '{\"included\": true}', NULL, '2025-08-21 07:55:09', '2025-09-21 07:55:09', NULL, NULL, '2025-08-21 07:55:09', '2025-09-21 07:55:09', NULL),
(34, 5, 1, 2, 1, '0.00000000', '0.00000000', '{\"included\": true}', NULL, '2025-08-21 07:55:09', '2025-09-21 07:55:09', NULL, NULL, '2025-08-21 07:55:09', '2025-09-21 07:55:09', NULL),
(35, 5, 1, 3, 1, '0.00000000', '0.00000000', '{\"included\": true}', NULL, '2025-08-21 07:55:09', '2025-09-21 07:55:09', NULL, NULL, '2025-08-21 07:55:09', '2025-09-21 07:55:09', NULL),
(36, 5, 1, 4, 1, '0.00000000', '0.00000000', '{\"included\": true}', NULL, '2025-08-21 07:55:09', '2025-09-21 07:55:09', NULL, NULL, '2025-08-21 07:55:09', '2025-09-21 07:55:09', NULL),
(37, 5, 1, 5, 1, '0.00000000', '0.00000000', '{\"included\": true}', NULL, '2025-08-21 07:55:09', '2025-09-21 07:55:09', NULL, NULL, '2025-08-21 07:55:09', '2025-09-21 07:55:09', NULL),
(38, 5, 1, 6, 1, '0.00000000', '0.00000000', '{\"included\": true}', NULL, '2025-08-21 07:55:09', '2025-09-21 07:55:09', NULL, NULL, '2025-08-21 07:55:09', '2025-09-21 07:55:09', NULL),
(39, 5, 1, 7, 1, '0.00000000', '0.00000000', '{\"included\": true}', NULL, '2025-08-21 07:55:09', '2025-09-21 07:55:09', NULL, NULL, '2025-08-21 07:55:09', '2025-09-21 07:55:09', NULL),
(40, 5, 1, 8, 1, '0.00000000', '0.00000000', '{\"included\": true}', NULL, '2025-08-21 07:55:09', '2025-09-21 07:55:09', NULL, NULL, '2025-08-21 07:55:09', '2025-09-21 07:55:09', NULL),
(41, 6, 1, 1, 1, '0.00000000', '0.00000000', '{\"included\": true}', NULL, '2026-02-16 07:55:09', NULL, NULL, NULL, '2026-02-16 07:55:09', '2026-02-21 07:55:09', NULL),
(42, 6, 1, 2, 1, '0.00000000', '0.00000000', '{\"included\": true}', NULL, '2026-02-16 07:55:09', NULL, NULL, NULL, '2026-02-16 07:55:09', '2026-02-21 07:55:09', NULL),
(43, 6, 1, 3, 1, '0.00000000', '0.00000000', '{\"included\": true}', NULL, '2026-02-16 07:55:09', NULL, NULL, NULL, '2026-02-16 07:55:09', '2026-02-21 07:55:09', NULL),
(44, 6, 1, 4, 1, '0.00000000', '0.00000000', '{\"included\": true}', NULL, '2026-02-16 07:55:09', NULL, NULL, NULL, '2026-02-16 07:55:09', '2026-02-21 07:55:09', NULL),
(45, 6, 1, 5, 1, '0.00000000', '0.00000000', '{\"included\": true}', NULL, '2026-02-16 07:55:09', NULL, NULL, NULL, '2026-02-16 07:55:09', '2026-02-21 07:55:09', NULL),
(46, 6, 1, 6, 1, '0.00000000', '0.00000000', '{\"included\": true}', NULL, '2026-02-16 07:55:09', NULL, NULL, NULL, '2026-02-16 07:55:09', '2026-02-21 07:55:09', NULL),
(47, 6, 1, 7, 1, '0.00000000', '0.00000000', '{\"included\": true}', NULL, '2026-02-16 07:55:09', NULL, NULL, NULL, '2026-02-16 07:55:09', '2026-02-21 07:55:09', NULL),
(48, 6, 1, 8, 1, '0.00000000', '0.00000000', '{\"included\": true}', NULL, '2026-02-16 07:55:09', NULL, NULL, NULL, '2026-02-16 07:55:09', '2026-02-21 07:55:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subscription_orders`
--

CREATE TABLE `subscription_orders` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `payment_master_id` int UNSIGNED DEFAULT NULL,
  `order_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft' COMMENT 'Enum values: draft, pending, processing, completed, cancelled, failed',
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new' COMMENT 'Enum values: new, renewal, upgrade, downgrade, bulk',
  `subtotal` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `tax_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `discount_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `total_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `currency` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `customer_info` json DEFAULT NULL,
  `billing_address` json DEFAULT NULL,
  `ordered_at` timestamp NULL DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `coupon_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `applied_discounts` json DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `failure_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Orders for multiple subscriptions';

--
-- Dumping data for table `subscription_orders`
--

INSERT INTO `subscription_orders` (`id`, `user_id`, `payment_master_id`, `order_number`, `status`, `type`, `subtotal`, `tax_amount`, `discount_amount`, `total_amount`, `currency`, `customer_info`, `billing_address`, `ordered_at`, `processed_at`, `cancelled_at`, `coupon_code`, `applied_discounts`, `metadata`, `notes`, `failure_reason`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, NULL, 'ORD-20260107-00001', 'completed', 'new', '29.99000000', '0.00000000', '0.00000000', '29.99000000', 'USD', '{\"name\": \"John Doe\", \"email\": \"john.doe@example.com\", \"phone\": \"+1234567891\"}', '{\"city\": \"San Francisco\", \"line1\": \"456 Business Ave\", \"state\": \"CA\", \"country\": \"US\", \"postal_code\": \"94105\"}', '2026-01-07 07:55:09', '2026-01-07 07:55:09', NULL, NULL, NULL, '{\"source\": \"web\"}', NULL, NULL, NULL, NULL, '2026-01-07 07:55:09', '2026-01-07 07:55:09', NULL),
(2, 3, NULL, 'ORD-20251123-00002', 'completed', 'new', '499.95000000', '41.24587500', '99.99000000', '441.20587500', 'USD', '{\"name\": \"Jane Smith\", \"email\": \"jane.smith@example.com\", \"phone\": \"+1234567892\"}', '{\"city\": \"Austin\", \"line1\": \"789 Oak St\", \"state\": \"TX\", \"country\": \"US\", \"postal_code\": \"78701\"}', '2025-11-23 07:55:09', '2025-11-23 07:55:09', NULL, 'WELCOME20', '[{\"code\": \"WELCOME20\", \"type\": \"percentage\", \"amount\": 20, \"discount\": 99.99}]', '{\"source\": \"web\"}', 'Customer used referral link', NULL, NULL, NULL, '2025-11-23 07:55:09', '2025-11-23 07:55:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subscription_order_items`
--

CREATE TABLE `subscription_order_items` (
  `id` int UNSIGNED NOT NULL,
  `subscription_order_id` int UNSIGNED NOT NULL,
  `plan_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `plan_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `billing_cycle` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `subscription_status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'Enum values: pending, created, failed',
  `processing_error` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Items within subscription orders';

--
-- Dumping data for table `subscription_order_items`
--

INSERT INTO `subscription_order_items` (`id`, `subscription_order_id`, `plan_id`, `user_id`, `plan_name`, `billing_cycle`, `quantity`, `recipient_user_id`, `recipient_info`, `unit_price`, `amount`, `tax_amount`, `discount_amount`, `total_amount`, `start_date`, `end_date`, `subscription_id`, `subscription_status`, `processing_error`, `processed_at`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 2, 2, 'Starter', 'monthly', 1, NULL, NULL, '29.99000000', '29.99000000', '0.00000000', '0.00000000', '29.99000000', '2026-02-06', '2026-03-08', 1, 'created', NULL, '2026-01-07 07:55:09', NULL, NULL, '2026-01-07 07:55:09', '2026-01-07 07:55:09', NULL),
(2, 2, 3, 3, 'Professional', 'monthly', 5, NULL, NULL, '99.99000000', '499.95000000', '41.24587500', '99.99000000', '441.20587500', '2026-02-01', '2026-03-03', 2, 'created', NULL, '2025-11-23 07:55:09', NULL, NULL, '2025-11-23 07:55:09', '2025-11-23 07:55:09', NULL);

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
-- Table structure for table `subscription_usage_summary`
--

CREATE TABLE `subscription_usage_summary` (
  `subscription_id` int UNSIGNED DEFAULT NULL,
  `user_email` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `plan_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `feature_code` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `feature_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `total_usage` decimal(42,8) DEFAULT NULL,
  `limit_value` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `usage_percentage` decimal(57,12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usage_records`
--

CREATE TABLE `usage_records` (
  `id` int UNSIGNED NOT NULL,
  `subscription_id` int UNSIGNED NOT NULL,
  `subscription_item_id` int UNSIGNED NOT NULL,
  `feature_id` int UNSIGNED NOT NULL,
  `quantity` decimal(20,8) NOT NULL,
  `tier_quantity` decimal(20,8) DEFAULT NULL,
  `amount` decimal(20,8) DEFAULT NULL,
  `unit` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'Enum values: pending, billed, void, disputed',
  `recorded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `billing_date` date NOT NULL,
  `metadata` json DEFAULT NULL,
  `dimensions` json DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Records of feature usage for metered billing';

--
-- Dumping data for table `usage_records`
--

INSERT INTO `usage_records` (`id`, `subscription_id`, `subscription_item_id`, `feature_id`, `quantity`, `tier_quantity`, `amount`, `unit`, `status`, `recorded_at`, `billing_date`, `metadata`, `dimensions`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 4, 25, 1, '2484.00000000', NULL, '0.24840000', 'request', 'billed', '2026-02-22 07:55:09', '2026-01-29', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(2, 4, 26, 2, '2.80000000', NULL, '0.28000000', 'gb', 'billed', '2026-02-22 07:55:09', '2026-01-29', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(3, 4, 25, 1, '4036.00000000', NULL, '0.40360000', 'request', 'billed', '2026-02-22 07:55:09', '2026-01-30', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(4, 4, 26, 2, '5.00000000', NULL, '0.50000000', 'gb', 'billed', '2026-02-22 07:55:09', '2026-01-30', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(5, 4, 25, 1, '519.00000000', NULL, '0.05190000', 'request', 'billed', '2026-02-22 07:55:09', '2026-01-31', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(6, 4, 26, 2, '2.30000000', NULL, '0.23000000', 'gb', 'billed', '2026-02-22 07:55:09', '2026-01-31', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(7, 4, 25, 1, '4375.00000000', NULL, '0.43750000', 'request', 'billed', '2026-02-22 07:55:09', '2026-02-01', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(8, 4, 26, 2, '2.40000000', NULL, '0.24000000', 'gb', 'billed', '2026-02-22 07:55:09', '2026-02-01', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(9, 4, 25, 1, '4251.00000000', NULL, '0.42510000', 'request', 'billed', '2026-02-22 07:55:09', '2026-02-02', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(10, 4, 26, 2, '2.10000000', NULL, '0.21000000', 'gb', 'billed', '2026-02-22 07:55:09', '2026-02-02', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(11, 4, 25, 1, '1662.00000000', NULL, '0.16620000', 'request', 'billed', '2026-02-22 07:55:09', '2026-02-03', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(12, 4, 26, 2, '1.40000000', NULL, '0.14000000', 'gb', 'billed', '2026-02-22 07:55:09', '2026-02-03', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(13, 4, 25, 1, '1017.00000000', NULL, '0.10170000', 'request', 'billed', '2026-02-22 07:55:09', '2026-02-04', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(14, 4, 26, 2, '0.30000000', NULL, '0.03000000', 'gb', 'billed', '2026-02-22 07:55:09', '2026-02-04', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(15, 4, 25, 1, '4363.00000000', NULL, '0.43630000', 'request', 'billed', '2026-02-22 07:55:09', '2026-02-05', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(16, 4, 26, 2, '4.60000000', NULL, '0.46000000', 'gb', 'billed', '2026-02-22 07:55:09', '2026-02-05', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(17, 4, 25, 1, '3472.00000000', NULL, '0.34720000', 'request', 'billed', '2026-02-22 07:55:09', '2026-02-06', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(18, 4, 26, 2, '2.40000000', NULL, '0.24000000', 'gb', 'billed', '2026-02-22 07:55:09', '2026-02-06', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(19, 4, 25, 1, '3441.00000000', NULL, '0.34410000', 'request', 'billed', '2026-02-22 07:55:09', '2026-02-07', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(20, 4, 26, 2, '4.60000000', NULL, '0.46000000', 'gb', 'billed', '2026-02-22 07:55:09', '2026-02-07', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(21, 4, 25, 1, '2105.00000000', NULL, '0.21050000', 'request', 'billed', '2026-02-22 07:55:09', '2026-02-08', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(22, 4, 26, 2, '2.70000000', NULL, '0.27000000', 'gb', 'billed', '2026-02-22 07:55:09', '2026-02-08', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(23, 4, 25, 1, '3891.00000000', NULL, '0.38910000', 'request', 'billed', '2026-02-22 07:55:09', '2026-02-09', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(24, 4, 26, 2, '4.40000000', NULL, '0.44000000', 'gb', 'billed', '2026-02-22 07:55:09', '2026-02-09', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(25, 4, 25, 1, '1192.00000000', NULL, '0.11920000', 'request', 'billed', '2026-02-22 07:55:09', '2026-02-10', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(26, 4, 26, 2, '2.90000000', NULL, '0.29000000', 'gb', 'billed', '2026-02-22 07:55:09', '2026-02-10', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(27, 4, 25, 1, '1075.00000000', NULL, '0.10750000', 'request', 'billed', '2026-02-22 07:55:09', '2026-02-11', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(28, 4, 26, 2, '1.40000000', NULL, '0.14000000', 'gb', 'billed', '2026-02-22 07:55:09', '2026-02-11', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(29, 4, 25, 1, '550.00000000', NULL, '0.05500000', 'request', 'billed', '2026-02-22 07:55:09', '2026-02-12', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(30, 4, 26, 2, '0.10000000', NULL, '0.01000000', 'gb', 'billed', '2026-02-22 07:55:09', '2026-02-12', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(31, 4, 25, 1, '840.00000000', NULL, '0.08400000', 'request', 'billed', '2026-02-22 07:55:09', '2026-02-13', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(32, 4, 26, 2, '3.60000000', NULL, '0.36000000', 'gb', 'billed', '2026-02-22 07:55:09', '2026-02-13', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(33, 4, 25, 1, '3807.00000000', NULL, '0.38070000', 'request', 'billed', '2026-02-22 07:55:09', '2026-02-14', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(34, 4, 26, 2, '0.80000000', NULL, '0.08000000', 'gb', 'billed', '2026-02-22 07:55:09', '2026-02-14', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(35, 4, 25, 1, '942.00000000', NULL, '0.09420000', 'request', 'billed', '2026-02-22 07:55:09', '2026-02-15', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(36, 4, 26, 2, '1.70000000', NULL, '0.17000000', 'gb', 'billed', '2026-02-22 07:55:09', '2026-02-15', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(37, 4, 25, 1, '3729.00000000', NULL, '0.37290000', 'request', 'billed', '2026-02-22 07:55:09', '2026-02-16', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(38, 4, 26, 2, '5.00000000', NULL, '0.50000000', 'gb', 'billed', '2026-02-22 07:55:09', '2026-02-16', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(39, 4, 25, 1, '2559.00000000', NULL, '0.25590000', 'request', 'billed', '2026-02-22 07:55:09', '2026-02-17', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(40, 4, 26, 2, '1.60000000', NULL, '0.16000000', 'gb', 'billed', '2026-02-22 07:55:09', '2026-02-17', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(41, 4, 25, 1, '2040.00000000', NULL, '0.20400000', 'request', 'billed', '2026-02-22 07:55:09', '2026-02-18', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(42, 4, 26, 2, '2.80000000', NULL, '0.28000000', 'gb', 'billed', '2026-02-22 07:55:09', '2026-02-18', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(43, 4, 25, 1, '4898.00000000', NULL, '0.48980000', 'request', 'billed', '2026-02-22 07:55:09', '2026-02-19', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(44, 4, 26, 2, '1.00000000', NULL, '0.10000000', 'gb', 'billed', '2026-02-22 07:55:09', '2026-02-19', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(45, 4, 25, 1, '2862.00000000', NULL, '0.28620000', 'request', 'billed', '2026-02-22 07:55:09', '2026-02-20', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(46, 4, 26, 2, '1.10000000', NULL, '0.11000000', 'gb', 'billed', '2026-02-22 07:55:09', '2026-02-20', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(47, 4, 25, 1, '2013.00000000', NULL, '0.20130000', 'request', 'billed', '2026-02-22 07:55:09', '2026-02-21', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(48, 4, 26, 2, '1.40000000', NULL, '0.14000000', 'gb', 'billed', '2026-02-22 07:55:09', '2026-02-21', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-22 07:55:09', '2026-02-22 07:55:09', NULL),
(49, 2, 9, 1, '394.00000000', NULL, NULL, 'request', 'billed', '2026-02-11 07:55:09', '2026-02-11', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-11 07:55:09', '2026-02-11 07:55:09', NULL),
(50, 2, 9, 1, '270.00000000', NULL, NULL, 'request', 'billed', '2026-02-16 07:55:09', '2026-02-16', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-16 07:55:09', '2026-02-16 07:55:09', NULL),
(51, 2, 9, 1, '376.00000000', NULL, NULL, 'request', 'billed', '2026-02-09 07:55:09', '2026-02-09', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-09 07:55:09', '2026-02-09 07:55:09', NULL),
(52, 2, 9, 1, '200.00000000', NULL, NULL, 'request', 'billed', '2026-01-23 07:55:09', '2026-01-23', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-01-23 07:55:09', '2026-01-23 07:55:09', NULL),
(53, 2, 9, 1, '578.00000000', NULL, NULL, 'request', 'billed', '2026-01-24 07:55:09', '2026-01-24', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-01-24 07:55:09', '2026-01-24 07:55:09', NULL),
(54, 1, 1, 1, '885.00000000', NULL, NULL, 'request', 'billed', '2026-02-06 07:55:09', '2026-02-06', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-06 07:55:09', '2026-02-06 07:55:09', NULL),
(55, 1, 1, 1, '270.00000000', NULL, NULL, 'request', 'billed', '2026-01-23 07:55:09', '2026-01-23', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-01-23 07:55:09', '2026-01-23 07:55:09', NULL),
(56, 1, 1, 1, '603.00000000', NULL, NULL, 'request', 'billed', '2026-02-07 07:55:09', '2026-02-07', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-07 07:55:09', '2026-02-07 07:55:09', NULL),
(57, 1, 1, 1, '645.00000000', NULL, NULL, 'request', 'billed', '2026-02-18 07:55:09', '2026-02-18', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-18 07:55:09', '2026-02-18 07:55:09', NULL),
(58, 1, 1, 1, '865.00000000', NULL, NULL, 'request', 'billed', '2026-01-27 07:55:09', '2026-01-27', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-01-27 07:55:09', '2026-01-27 07:55:09', NULL),
(59, 3, 17, 1, '103.00000000', NULL, NULL, 'request', 'billed', '2026-01-29 07:55:09', '2026-01-29', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-01-29 07:55:09', '2026-01-29 07:55:09', NULL),
(60, 3, 17, 1, '675.00000000', NULL, NULL, 'request', 'billed', '2026-02-09 07:55:09', '2026-02-09', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-09 07:55:09', '2026-02-09 07:55:09', NULL),
(61, 3, 17, 1, '488.00000000', NULL, NULL, 'request', 'billed', '2026-01-24 07:55:09', '2026-01-24', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-01-24 07:55:09', '2026-01-24 07:55:09', NULL),
(62, 3, 17, 1, '456.00000000', NULL, NULL, 'request', 'billed', '2026-02-08 07:55:09', '2026-02-08', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-08 07:55:09', '2026-02-08 07:55:09', NULL),
(63, 3, 17, 1, '185.00000000', NULL, NULL, 'request', 'billed', '2026-02-08 07:55:09', '2026-02-08', '{\"source\": \"api\"}', NULL, NULL, NULL, '2026-02-08 07:55:09', '2026-02-08 07:55:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'personal' COMMENT 'Enum values: personal, business, enterprise',
  `tax_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_tax_exempt` tinyint(1) NOT NULL DEFAULT '0',
  `tax_certificate` json DEFAULT NULL,
  `billing_address` json DEFAULT NULL,
  `shipping_address` json DEFAULT NULL,
  `preferred_currency` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `preferred_payment_method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auto_renew` tinyint(1) NOT NULL DEFAULT '1',
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `has_used_trial` tinyint(1) NOT NULL DEFAULT '0',
  `account_status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active' COMMENT 'Enum values: active, suspended, closed, fraudulent',
  `account_status_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL,
  `preferences` json DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores user information with subscription preferences';

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `email_verified_at`, `password`, `remember_token`, `billing_type`, `tax_id`, `is_tax_exempt`, `tax_certificate`, `billing_address`, `shipping_address`, `preferred_currency`, `preferred_payment_method`, `auto_renew`, `trial_ends_at`, `has_used_trial`, `account_status`, `account_status_reason`, `metadata`, `preferences`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Admin User', 'admin@gmail.com', '+1234567890', '2026-02-21 07:55:09', '$2y$12$FXuZhLztbNjNvQ67OAa/9OTNsaN1WAXZjsRER5KLuuSQPV50egtse', 'cLYCcZ71qL', 'personal', NULL, 0, NULL, '{\"city\": \"New York\", \"line1\": \"123 Main St\", \"state\": \"NY\", \"country\": \"US\", \"postal_code\": \"10001\"}', '{\"city\": \"New York\", \"line1\": \"123 Main St\", \"state\": \"NY\", \"country\": \"US\", \"postal_code\": \"10001\"}', 'USD', 'card', 1, NULL, 0, 'active', NULL, '{\"source\": \"website\"}', '{\"newsletter\": true}', NULL, NULL, '2026-02-21 07:55:09', '2026-02-21 09:46:38', NULL),
(2, 'John Doe', 'john.doe@example.com', '+1234567891', '2026-02-21 07:55:09', '$2y$12$YOx0.7ztnHu2pG0WKhqoL.IsxEpMqbAhrnFmWgzwHTMUMJi7Ibkq2', 'qcFAVbmDhT', 'business', '123456789', 0, '{\"number\": \"CERT123\", \"expires\": \"2026-12-31\"}', '{\"city\": \"San Francisco\", \"line1\": \"456 Business Ave\", \"state\": \"CA\", \"country\": \"US\", \"postal_code\": \"94105\"}', '{\"city\": \"San Francisco\", \"line1\": \"456 Business Ave\", \"state\": \"CA\", \"country\": \"US\", \"postal_code\": \"94105\"}', 'USD', 'card', 1, NULL, 1, 'active', NULL, '{\"source\": \"referral\", \"company\": \"Acme Inc\"}', '{\"dark_mode\": false, \"newsletter\": true}', NULL, NULL, '2025-08-21 07:55:09', '2026-02-21 09:54:26', NULL),
(3, 'Jane Smith', 'jane.smith@example.com', '+1234567892', '2026-02-21 07:55:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mAUuW7Wprm', 'personal', NULL, 1, '{\"number\": \"TAXEXEMPT123\"}', '{\"city\": \"Austin\", \"line1\": \"789 Oak St\", \"state\": \"TX\", \"country\": \"US\", \"postal_code\": \"78701\"}', '{\"city\": \"Austin\", \"line1\": \"789 Oak St\", \"state\": \"TX\", \"country\": \"US\", \"postal_code\": \"78701\"}', 'USD', 'paypal', 1, NULL, 1, 'active', NULL, '{\"source\": \"google_ads\"}', '{\"dark_mode\": true, \"newsletter\": false}', NULL, NULL, '2025-11-21 07:55:09', '2026-02-21 07:55:09', NULL),
(4, 'Bob Johnson', 'bob.johnson@example.com', '+1234567893', '2026-02-21 07:55:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'b3ReoMUJfo', 'enterprise', '987654321', 0, NULL, '{\"city\": \"Chicago\", \"line1\": \"321 Corporate Blvd\", \"state\": \"IL\", \"country\": \"US\", \"postal_code\": \"60601\"}', '{\"city\": \"Chicago\", \"line1\": \"321 Corporate Blvd\", \"state\": \"IL\", \"country\": \"US\", \"postal_code\": \"60601\"}', 'USD', 'bank_transfer', 1, NULL, 0, 'active', NULL, '{\"source\": \"sales_team\", \"company\": \"Johnson Corp\", \"employees\": 500}', '{\"dark_mode\": true, \"api_access\": true, \"newsletter\": true}', NULL, NULL, '2025-02-21 07:55:09', '2026-02-21 07:55:09', NULL),
(5, 'Sarah Williams', 'sarah.williams@example.com', '+1234567894', '2026-02-21 07:55:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ifguSbGXjw', 'business', '456789123', 0, NULL, '{\"city\": \"Seattle\", \"line1\": \"555 Tech Park\", \"state\": \"WA\", \"country\": \"US\", \"postal_code\": \"98101\"}', '{\"city\": \"Seattle\", \"line1\": \"555 Tech Park\", \"state\": \"WA\", \"country\": \"US\", \"postal_code\": \"98101\"}', 'USD', 'card', 1, NULL, 1, 'active', NULL, '{\"source\": \"twitter\", \"company\": \"TechStart\"}', '{\"dark_mode\": false, \"newsletter\": true, \"beta_features\": true}', NULL, NULL, '2025-12-21 07:55:09', '2026-02-21 07:55:09', NULL),
(6, 'Mike Brown', 'mike.brown@example.com', '+1234567895', '2026-02-21 07:55:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1Bk2XQmdv3', 'personal', NULL, 0, NULL, '{\"city\": \"Miami\", \"line1\": \"777 Beach Ave\", \"state\": \"FL\", \"country\": \"US\", \"postal_code\": \"33101\"}', '{\"city\": \"Miami\", \"line1\": \"777 Beach Ave\", \"state\": \"FL\", \"country\": \"US\", \"postal_code\": \"33101\"}', 'USD', 'card', 0, NULL, 1, 'suspended', 'payment_failed_multiple_times', '{\"source\": \"facebook\"}', '{\"dark_mode\": false, \"newsletter\": false}', NULL, NULL, '2025-06-21 07:55:09', '2026-02-21 07:55:09', NULL);

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
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code_UNIQUE` (`code`),
  ADD KEY `active_discounts_idx` (`code`,`is_active`,`expires_at`),
  ADD KEY `discount_finder_idx` (`type`,`applies_to`,`starts_at`);

--
-- Indexes for table `features`
--
ALTER TABLE `features`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code_UNIQUE` (`code`),
  ADD KEY `feature_code_type_idx` (`code`,`type`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `number_UNIQUE` (`number`),
  ADD UNIQUE KEY `external_id_UNIQUE` (`external_id`),
  ADD KEY `user_invoice_status_idx` (`user_id`,`status`,`due_date`),
  ADD KEY `subscription_invoices_idx` (`subscription_id`,`issue_date`),
  ADD KEY `external_invoice_idx` (`external_id`),
  ADD KEY `invoice_analytics_idx` (`type`,`status`,`issue_date`),
  ADD KEY `idx_invoices_user_due` (`user_id`,`due_date`,`status`);

--
-- Indexes for table `metered_usage_aggregates`
--
ALTER TABLE `metered_usage_aggregates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usage_aggregate_unique` (`subscription_id`,`feature_id`,`aggregate_date`,`aggregate_period`),
  ADD KEY `rollup_usage_idx` (`subscription_id`,`aggregate_date`,`feature_id`),
  ADD KEY `global_usage_trends_idx` (`aggregate_date`,`feature_id`,`total_quantity`),
  ADD KEY `fk_metered_usage_aggregates_features` (`feature_id`);

--
-- Indexes for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `otp_verifications_email_index` (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `external_id_UNIQUE` (`external_id`),
  ADD KEY `user_payments_idx` (`user_id`,`status`,`processed_at`),
  ADD KEY `gateway_payments_idx` (`gateway`,`external_id`),
  ADD KEY `payment_analytics_idx` (`type`,`status`,`processed_at`),
  ADD KEY `fk_payments_invoices` (`invoice_id`);

--
-- Indexes for table `payment_allocations`
--
ALTER TABLE `payment_allocations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_allocation` (`payment_transaction_id`,`allocatable_type`,`allocatable_id`),
  ADD KEY `master_allocation_idx` (`payment_master_id`,`allocatable_type`,`allocatable_id`),
  ADD KEY `item_allocations_idx` (`allocatable_type`,`allocatable_id`,`is_reversed`),
  ADD KEY `fk_payment_allocations_payment_children` (`payment_child_id`);

--
-- Indexes for table `payment_children`
--
ALTER TABLE `payment_children`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_item_lookup_idx` (`payment_master_id`,`item_type`,`item_id`),
  ADD KEY `subscription_payments_idx` (`subscription_id`,`status`),
  ADD KEY `invoice_payments_idx` (`invoice_id`,`status`),
  ADD KEY `item_payment_status_idx` (`item_type`,`item_id`,`status`),
  ADD KEY `fk_payment_children_plans` (`plan_id`);

--
-- Indexes for table `payment_gateways`
--
ALTER TABLE `payment_gateways`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code_UNIQUE` (`code`),
  ADD KEY `active_gateways_idx` (`code`,`is_active`,`is_test_mode`),
  ADD KEY `gateway_type_idx` (`type`,`is_active`);

--
-- Indexes for table `payment_masters`
--
ALTER TABLE `payment_masters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_number_UNIQUE` (`payment_number`),
  ADD KEY `user_payment_status_idx` (`user_id`,`status`,`payment_date`),
  ADD KEY `payment_lookup_idx` (`payment_number`,`type`),
  ADD KEY `payment_method_analytics_idx` (`payment_method`,`status`,`payment_date`),
  ADD KEY `pending_payments_idx` (`due_date`,`status`),
  ADD KEY `idx_payment_masters_user_date` (`user_id`,`payment_date`,`status`);
ALTER TABLE `payment_masters` ADD FULLTEXT KEY `payment_search_ft_idx` (`payment_number`,`customer_reference`,`notes`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_payment_method` (`user_id`,`fingerprint`),
  ADD KEY `user_payment_methods_idx` (`user_id`,`type`,`is_default`),
  ADD KEY `gateway_method_lookup_idx` (`gateway`,`gateway_payment_method_id`);

--
-- Indexes for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_id_UNIQUE` (`transaction_id`),
  ADD KEY `master_transaction_status_idx` (`payment_master_id`,`status`),
  ADD KEY `transaction_lookup_idx` (`transaction_id`,`reference_id`),
  ADD KEY `payment_method_stats_idx` (`payment_method`,`status`,`completed_at`),
  ADD KEY `gateway_stats_idx` (`payment_gateway`,`status`,`completed_at`),
  ADD KEY `card_analytics_idx` (`card_brand`,`status`),
  ADD KEY `revenue_analytics_idx` (`completed_at`,`amount`),
  ADD KEY `fk_payment_transactions_payment_children` (`payment_child_id`);

--
-- Indexes for table `payment_webhook_logs`
--
ALTER TABLE `payment_webhook_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `webhook_events_idx` (`gateway`,`event_type`,`received_at`),
  ADD KEY `webhook_reference_idx` (`reference_id`,`gateway`),
  ADD KEY `pending_webhooks_idx` (`status`,`next_retry_at`),
  ADD KEY `fk_payment_webhook_logs_payment_gateways` (`payment_gateway_id`),
  ADD KEY `fk_payment_webhook_logs_payment_transactions` (`payment_transaction_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug_UNIQUE` (`slug`),
  ADD UNIQUE KEY `code_UNIQUE` (`code`),
  ADD KEY `plan_type_active_idx` (`type`,`is_active`,`is_visible`),
  ADD KEY `plan_display_idx` (`sort_order`,`is_featured`);

--
-- Indexes for table `plan_discounts`
--
ALTER TABLE `plan_discounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_plan_discount` (`plan_id`,`discount_id`),
  ADD KEY `fk_plan_discounts_discount` (`discount_id`);

--
-- Indexes for table `plan_features`
--
ALTER TABLE `plan_features`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feature_value_idx` (`feature_id`,`value`),
  ADD KEY `plan_feature_unique` (`plan_id`,`feature_id`) USING BTREE COMMENT 'WHERE effective_to IS NULL';

--
-- Indexes for table `plan_prices`
--
ALTER TABLE `plan_prices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `plan_price_unique` (`plan_id`,`currency`,`interval`,`interval_count`),
  ADD UNIQUE KEY `stripe_price_id_UNIQUE` (`stripe_price_id`),
  ADD KEY `active_price_idx` (`plan_id`,`currency`,`interval`) COMMENT 'WHERE active_to IS NULL';

--
-- Indexes for table `rate_limits`
--
ALTER TABLE `rate_limits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rate_limit_unique` (`subscription_id`,`feature_id`,`key`),
  ADD KEY `expired_rate_limits_idx` (`resets_at`),
  ADD KEY `fk_rate_limits_features` (`feature_id`);

--
-- Indexes for table `refunds`
--
ALTER TABLE `refunds`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `refund_number_UNIQUE` (`refund_number`),
  ADD KEY `user_refunds_idx` (`user_id`,`status`,`requested_at`),
  ADD KEY `payment_refunds_idx` (`payment_master_id`,`status`),
  ADD KEY `fk_refunds_payment_transactions` (`payment_transaction_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_active_subscription_idx` (`user_id`,`status`,`current_period_ends_at`),
  ADD KEY `expiring_subscriptions_idx` (`status`,`current_period_ends_at`),
  ADD KEY `gateway_subscription_lookup` (`gateway`,`gateway_subscription_id`),
  ADD KEY `child_subscriptions_idx` (`parent_subscription_id`,`status`),
  ADD KEY `fk_subscriptions_plans` (`plan_id`),
  ADD KEY `fk_subscriptions_plan_prices` (`plan_price_id`),
  ADD KEY `idx_subscriptions_user_status` (`user_id`,`status`,`current_period_ends_at`);

--
-- Indexes for table `subscription_events`
--
ALTER TABLE `subscription_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subscription_timeline_idx` (`subscription_id`,`occurred_at`),
  ADD KEY `event_analytics_idx` (`type`,`occurred_at`),
  ADD KEY `causer_events_idx` (`causer_id`,`causer_type`,`occurred_at`),
  ADD KEY `subscription_state_changes_idx` (`subscription_id`,`type`,`occurred_at`);

--
-- Indexes for table `subscription_items`
--
ALTER TABLE `subscription_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subscription_item_unique` (`subscription_id`,`plan_price_id`,`feature_id`) COMMENT 'WHERE effective_to IS NULL',
  ADD KEY `subscription_pricing_idx` (`subscription_id`,`amount`),
  ADD KEY `fk_subscription_items_plan_prices` (`plan_price_id`),
  ADD KEY `fk_subscription_items_features` (`feature_id`);

--
-- Indexes for table `subscription_orders`
--
ALTER TABLE `subscription_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number_UNIQUE` (`order_number`),
  ADD KEY `user_subscription_orders_idx` (`user_id`,`status`,`ordered_at`),
  ADD KEY `order_lookup_idx` (`order_number`,`type`),
  ADD KEY `order_processing_idx` (`status`,`ordered_at`),
  ADD KEY `fk_subscription_orders_payment_masters` (`payment_master_id`);

--
-- Indexes for table `subscription_order_items`
--
ALTER TABLE `subscription_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_idx` (`subscription_order_id`,`plan_id`),
  ADD KEY `user_order_subscriptions_idx` (`user_id`,`subscription_id`),
  ADD KEY `gifted_subscriptions_idx` (`recipient_user_id`,`subscription_status`),
  ADD KEY `fk_subscription_order_items_plans` (`plan_id`),
  ADD KEY `fk_subscription_order_items_subscriptions` (`subscription_id`),
  ADD KEY `idx_subscription_order_items_status` (`subscription_status`,`processed_at`);

--
-- Indexes for table `usage_records`
--
ALTER TABLE `usage_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subscription_usage_idx` (`subscription_id`,`billing_date`,`feature_id`),
  ADD KEY `billing_aggregation_idx` (`billing_date`,`feature_id`,`status`),
  ADD KEY `record_timestamp_idx` (`recorded_at`),
  ADD KEY `fk_usage_records_subscription_items` (`subscription_item_id`),
  ADD KEY `fk_usage_records_features` (`feature_id`),
  ADD KEY `idx_usage_records_subscription_feature` (`subscription_id`,`feature_id`,`billing_date`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`),
  ADD KEY `user_email_status_idx` (`email`,`account_status`),
  ADD KEY `user_trial_status_idx` (`trial_ends_at`,`account_status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `discounts`
--
ALTER TABLE `discounts`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `features`
--
ALTER TABLE `features`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `metered_usage_aggregates`
--
ALTER TABLE `metered_usage_aggregates`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payment_allocations`
--
ALTER TABLE `payment_allocations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payment_children`
--
ALTER TABLE `payment_children`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payment_gateways`
--
ALTER TABLE `payment_gateways`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payment_masters`
--
ALTER TABLE `payment_masters`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payment_webhook_logs`
--
ALTER TABLE `payment_webhook_logs`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `plan_discounts`
--
ALTER TABLE `plan_discounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plan_features`
--
ALTER TABLE `plan_features`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `plan_prices`
--
ALTER TABLE `plan_prices`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `rate_limits`
--
ALTER TABLE `rate_limits`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `refunds`
--
ALTER TABLE `refunds`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `subscription_events`
--
ALTER TABLE `subscription_events`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `subscription_items`
--
ALTER TABLE `subscription_items`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `subscription_orders`
--
ALTER TABLE `subscription_orders`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `subscription_order_items`
--
ALTER TABLE `subscription_order_items`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `usage_records`
--
ALTER TABLE `usage_records`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `fk_invoices_subscriptions` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_invoices_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `metered_usage_aggregates`
--
ALTER TABLE `metered_usage_aggregates`
  ADD CONSTRAINT `fk_metered_usage_aggregates_features` FOREIGN KEY (`feature_id`) REFERENCES `features` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_metered_usage_aggregates_subscriptions` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_invoices` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_payments_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment_allocations`
--
ALTER TABLE `payment_allocations`
  ADD CONSTRAINT `fk_payment_allocations_payment_children` FOREIGN KEY (`payment_child_id`) REFERENCES `payment_children` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_payment_allocations_payment_masters` FOREIGN KEY (`payment_master_id`) REFERENCES `payment_masters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_payment_allocations_payment_transactions` FOREIGN KEY (`payment_transaction_id`) REFERENCES `payment_transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment_children`
--
ALTER TABLE `payment_children`
  ADD CONSTRAINT `fk_payment_children_invoices` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_payment_children_payment_masters` FOREIGN KEY (`payment_master_id`) REFERENCES `payment_masters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_payment_children_plans` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_payment_children_subscriptions` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment_masters`
--
ALTER TABLE `payment_masters`
  ADD CONSTRAINT `fk_payment_masters_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD CONSTRAINT `fk_payment_methods_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD CONSTRAINT `fk_payment_transactions_payment_children` FOREIGN KEY (`payment_child_id`) REFERENCES `payment_children` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_payment_transactions_payment_masters` FOREIGN KEY (`payment_master_id`) REFERENCES `payment_masters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment_webhook_logs`
--
ALTER TABLE `payment_webhook_logs`
  ADD CONSTRAINT `fk_payment_webhook_logs_payment_gateways` FOREIGN KEY (`payment_gateway_id`) REFERENCES `payment_gateways` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_payment_webhook_logs_payment_transactions` FOREIGN KEY (`payment_transaction_id`) REFERENCES `payment_transactions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `plan_discounts`
--
ALTER TABLE `plan_discounts`
  ADD CONSTRAINT `fk_plan_discounts_discount` FOREIGN KEY (`discount_id`) REFERENCES `discounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_plan_discounts_plan` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `plan_features`
--
ALTER TABLE `plan_features`
  ADD CONSTRAINT `fk_plan_features_features` FOREIGN KEY (`feature_id`) REFERENCES `features` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_plan_features_plans` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `plan_prices`
--
ALTER TABLE `plan_prices`
  ADD CONSTRAINT `fk_plan_prices_plans` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rate_limits`
--
ALTER TABLE `rate_limits`
  ADD CONSTRAINT `fk_rate_limits_features` FOREIGN KEY (`feature_id`) REFERENCES `features` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rate_limits_subscriptions` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `refunds`
--
ALTER TABLE `refunds`
  ADD CONSTRAINT `fk_refunds_payment_masters` FOREIGN KEY (`payment_master_id`) REFERENCES `payment_masters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_refunds_payment_transactions` FOREIGN KEY (`payment_transaction_id`) REFERENCES `payment_transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_refunds_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `fk_subscriptions_plan_prices` FOREIGN KEY (`plan_price_id`) REFERENCES `plan_prices` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_subscriptions_plans` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_subscriptions_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `subscription_events`
--
ALTER TABLE `subscription_events`
  ADD CONSTRAINT `fk_subscription_events_subscriptions` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `subscription_items`
--
ALTER TABLE `subscription_items`
  ADD CONSTRAINT `fk_subscription_items_features` FOREIGN KEY (`feature_id`) REFERENCES `features` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_subscription_items_plan_prices` FOREIGN KEY (`plan_price_id`) REFERENCES `plan_prices` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_subscription_items_subscriptions` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `subscription_orders`
--
ALTER TABLE `subscription_orders`
  ADD CONSTRAINT `fk_subscription_orders_payment_masters` FOREIGN KEY (`payment_master_id`) REFERENCES `payment_masters` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_subscription_orders_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `subscription_order_items`
--
ALTER TABLE `subscription_order_items`
  ADD CONSTRAINT `fk_subscription_order_items_plans` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_subscription_order_items_recipient_users` FOREIGN KEY (`recipient_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_subscription_order_items_subscription_orders` FOREIGN KEY (`subscription_order_id`) REFERENCES `subscription_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_subscription_order_items_subscriptions` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_subscription_order_items_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `usage_records`
--
ALTER TABLE `usage_records`
  ADD CONSTRAINT `fk_usage_records_features` FOREIGN KEY (`feature_id`) REFERENCES `features` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_usage_records_subscription_items` FOREIGN KEY (`subscription_item_id`) REFERENCES `subscription_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_usage_records_subscriptions` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
