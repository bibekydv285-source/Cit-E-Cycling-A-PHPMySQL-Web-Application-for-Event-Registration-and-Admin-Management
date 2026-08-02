-- ============================================================
-- Migration: Add club_id to interest table
-- Run this once in phpMyAdmin or your MySQL client
-- ============================================================

ALTER TABLE `interest`
  ADD COLUMN `club_id` int(11) DEFAULT NULL
    COMMENT 'NULL = no club preference',
  ADD CONSTRAINT `fk_interest_club`
    FOREIGN KEY (`club_id`) REFERENCES `club`(`id`)
    ON DELETE SET NULL ON UPDATE CASCADE;
