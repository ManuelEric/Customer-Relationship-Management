CREATE TABLE `contribution_calculation_tmp` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `divisi` varchar(255) NOT NULL,
  `contribution_in_percent` double NOT NULL,
  `contribution_to_target` int(11) DEFAULT null,
  `initial_consult_target` int(11) DEFAULT null COMMENT '# of IC (1,5x target)',
  `hot_leads_target` int(11) DEFAULT null COMMENT '# of hot leads (2x IC)',
  `leads_needed` int(11) DEFAULT null
);

CREATE TABLE `target_tracking` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `divisi` varchar(255) NOT NULL,
  `target_lead` int(11) NOT NULL,
  `achieved_lead` int(11) NOT NULL,
  `target_hotleads` int(11) NOT NULL,
  `achieved_hotleads` int(11) NOT NULL,
  `target_initconsult` int(11) NOT NULL,
  `achieved_initconsult` int(11) NOT NULL,
  `contribution_target` int(11) NOT NULL,
  `contribution_achieved` int(11) NOT NULL,
  `revenue_achieved` int(11) NOT NULL,
  `revenue_target` int(11) NOT NULL,
  `month_year` date NOT NULL,
  `added` int(11) NOT NULL COMMENT 'the number of deviation from month before',
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: incomplete, 1: complete',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_acad_tutor_dtl` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `clientprog_id` bigint(20) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `link` text NOT NULL COMMENT 'online meeting room',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_agenda_speaker` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `event_id` char(11) DEFAULT null,
  `sch_prog_id` bigint(20) DEFAULT null,
  `partner_prog_id` bigint(20) DEFAULT null,
  `eduf_id` bigint(20) DEFAULT null,
  `sch_pic_id` bigint(20) DEFAULT null,
  `univ_pic_id` bigint(20) DEFAULT null,
  `partner_pic_id` bigint(20) DEFAULT null,
  `empl_id` varchar(36) DEFAULT null COMMENT 'ALL-In PIC',
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `priority` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `speaker_type` ENUM ('school', 'university', 'partner', 'internal') NOT NULL,
  `notes` text DEFAULT null,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_asset` (
  `asset_id` char(7) PRIMARY KEY NOT NULL,
  `asset_name` varchar(255) DEFAULT null,
  `asset_merktype` varchar(255) DEFAULT null,
  `asset_dateachieved` date DEFAULT null,
  `asset_amount` int(11) DEFAULT null,
  `asset_running_stock` int(11) NOT NULL DEFAULT 0,
  `asset_unit` varchar(50) DEFAULT null,
  `asset_condition` varchar(255) DEFAULT null,
  `asset_notes` varchar(255) DEFAULT null,
  `asset_status` varchar(255) DEFAULT null,
  `created_at` timestamp NOT NULL DEFAULT (current_timestamp()),
  `updated_at` timestamp NOT NULL DEFAULT (current_timestamp())
);

CREATE TABLE `tbl_asset_returned` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `asset_used_id` bigint(20) NOT NULL,
  `returned_date` date NOT NULL,
  `amount_returned` int(11) NOT NULL DEFAULT 1,
  `condition` ENUM ('Good', 'Not Good') NOT NULL DEFAULT 'Good',
  `notes` text DEFAULT null,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_asset_used` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `asset_id` char(7) NOT NULL,
  `user_id` varchar(36) NOT NULL,
  `used_date` date NOT NULL,
  `amount_used` int(11) NOT NULL DEFAULT 1,
  `condition` ENUM ('Good', 'Not Good') NOT NULL DEFAULT 'Good',
  `notes` text DEFAULT null,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_axis` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `top` double(8,2) NOT NULL,
  `left` double(8,2) NOT NULL,
  `scaleX` double(8,2) NOT NULL,
  `scaleY` double(8,2) NOT NULL,
  `angle` double(8,2) NOT NULL,
  `flipX` tinyint(4) NOT NULL COMMENT '0: False, 1: True',
  `flipY` tinyint(4) NOT NULL COMMENT '0: False, 1: True',
  `type` ENUM ('invoice', 'receipt') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_bundling` (
  `uuid` char(36) PRIMARY KEY NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_bundling_dtl` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `bundling_id` char(36) NOT NULL,
  `clientprog_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_client` (
  `id` varchar(36) PRIMARY KEY NOT NULL DEFAULT (uuid()),
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) DEFAULT null,
  `mail` varchar(255) DEFAULT null,
  `phone` varchar(22) DEFAULT null,
  `phone_desc` varchar(50) DEFAULT null,
  `dob` date DEFAULT null,
  `insta` varchar(255) DEFAULT null,
  `state` varchar(255) DEFAULT null,
  `city` varchar(255) DEFAULT null,
  `postal_code` int(11) DEFAULT null,
  `address` varchar(255) DEFAULT null,
  `sch_id` char(8) DEFAULT null,
  `st_grade` int(11) DEFAULT null,
  `lead_id` varchar(5) DEFAULT null,
  `eduf_id` bigint(20) DEFAULT null,
  `event_id` char(11) DEFAULT null,
  `st_levelinterest` varchar(255) DEFAULT null,
  `graduation_year` char(4) DEFAULT null,
  `gap_year` char(4) DEFAULT null,
  `st_abryear` char(4) DEFAULT null,
  `st_statusact` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'status aktif client',
  `st_note` text DEFAULT null,
  `st_statuscli` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: prospective, 1: potential, 2: current, 3: completed',
  `st_password` text DEFAULT '$2y$10$SWFdY4TqrTDzPlRqcG7F6.FpdeeMNLGllgHaaD8nIRDthqBQFTI1i',
  `is_funding` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: False, 1: True',
  `register_as` ENUM ('student', 'parent', 'teacher/counsellor') DEFAULT 'student',
  `preferred_program` varchar(255) DEFAULT null,
  `scholarship` ENUM ('Y', 'N') NOT NULL DEFAULT 'N' COMMENT 'Scholarship Eligibility',
  `is_verified` ENUM ('Y', 'N') NOT NULL DEFAULT 'N',
  `referral_code` varchar(255) DEFAULT null COMMENT 'Referral code is a unique code from client data',
  `category` ENUM ('new-lead', 'potential', 'mentee', 'non-mentee', 'alumni-mentee', 'alumni-non-mentee') DEFAULT null,
  `took_ia` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_client_abrcountry` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `client_id` varchar(36) NOT NULL,
  `tag_id` bigint(20) DEFAULT null,
  `country_name` varchar(255) DEFAULT null COMMENT 'used when countries that shown in client are all country',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_client_acceptance` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `client_id` varchar(36) NOT NULL,
  `univ_id` char(8) NOT NULL,
  `major_id` bigint(20) NOT NULL,
  `status` ENUM ('waitlisted', 'accepted', 'denied', 'chosen') NOT NULL DEFAULT 'waitlisted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_client_additional_info` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `client_id` varchar(36) NOT NULL,
  `category` ENUM ('mail', 'phone') NOT NULL,
  `value` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT null,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_client_event` (
  `clientevent_id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `ticket_id` varchar(10) DEFAULT null COMMENT 'can be used as identifier',
  `client_id` varchar(36) NOT NULL,
  `child_id` varchar(36) DEFAULT null COMMENT 'is used when client_id is a parent / they registered as a parent',
  `parent_id` varchar(36) DEFAULT null COMMENT 'is used when client_id is a student / they registered as a student',
  `event_id` char(11) DEFAULT null,
  `lead_id` varchar(5) NOT NULL,
  `eduf_id` bigint(20) DEFAULT null,
  `partner_id` char(9) DEFAULT null,
  `registration_type` ENUM ('OTS', 'PR') NOT NULL DEFAULT 'PR' COMMENT 'PR : Pra Registration, OTS : On The Spot',
  `number_of_attend` int(11) NOT NULL DEFAULT 1 COMMENT 'How many people are joined the event',
  `notes` text DEFAULT null,
  `referral_code` varchar(255) DEFAULT null COMMENT 'Referral code is a unique code from client data',
  `status` int(11) NOT NULL,
  `joined_date` date DEFAULT null,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_client_event_log_mail` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `clientevent_id` bigint(20) DEFAULT null,
  `child_id` varchar(36) DEFAULT null,
  `notes` text DEFAULT null,
  `client_id` varchar(36) DEFAULT null,
  `event_id` char(11) DEFAULT null,
  `sent_status` tinyint(1) NOT NULL DEFAULT 0,
  `category` ENUM ('thanks-mail', 'qrcode-mail', 'qrcode-mail-referral', 'reminder-registration', 'reminder-referral', 'reminder-attend', 'invitation-mail', 'thanks-mail-after', 'feedback-mail', 'invitation-info', 'reminder-mail', 'registration-event-mail', 'verification-registration-event-mail') DEFAULT null,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_client_lead_tracking` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `group_id` varchar(10) NOT NULL,
  `client_id` varchar(36) NOT NULL,
  `initialprogram_id` bigint(20) NOT NULL,
  `type` ENUM ('Program', 'Lead') NOT NULL,
  `total_result` double NOT NULL,
  `potential_point` double NOT NULL DEFAULT 0 COMMENT 'this point is for digital team tracker',
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `reason_id` bigint(20) DEFAULT null,
  `reason_notes` text DEFAULT null,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_client_mentor` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `clientprog_id` bigint(20) NOT NULL,
  `user_id` varchar(36) NOT NULL,
  `timesheet_link` text DEFAULT null,
  `type` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1: Supervising Mentor, 2: Profile Building & Exploration Mentor, 3: Aplication Strategy Mentor, 4: Writing Mentor, 5: Tutor',
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_client_prog` (
  `clientprog_id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `client_id` varchar(36) NOT NULL,
  `prog_id` char(11) NOT NULL,
  `lead_id` varchar(5) NOT NULL,
  `eduf_lead_id` bigint(20) DEFAULT null,
  `partner_id` char(9) DEFAULT null,
  `clientevent_id` bigint(20) DEFAULT null,
  `first_discuss_date` date DEFAULT null,
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '0: pending, 1: success, 2: failed, 3: refund',
  `initconsult_date` date DEFAULT null,
  `assessmentsent_date` date DEFAULT null,
  `negotiation_date` date DEFAULT null,
  `reason_id` bigint(20) DEFAULT null,
  `reason_notes` text DEFAULT null,
  `test_date` date DEFAULT null,
  `first_class` date DEFAULT null,
  `last_class` date DEFAULT null,
  `diag_score` int(11) NOT NULL DEFAULT 0,
  `test_score` int(11) NOT NULL DEFAULT 0,
  `price_from_tutor` bigint(20) NOT NULL DEFAULT 0,
  `our_price_tutor` bigint(20) NOT NULL DEFAULT 0,
  `total_price_tutor` bigint(20) NOT NULL DEFAULT 0,
  `duration_notes` text DEFAULT null,
  `total_uni` int(11) NOT NULL DEFAULT 0,
  `total_foreign_currency` bigint(20) NOT NULL DEFAULT 0,
  `foreign_currency_exchange` int(11) NOT NULL DEFAULT 0,
  `foreign_currency` varchar(20) DEFAULT null,
  `total_idr` bigint(20) NOT NULL DEFAULT 0,
  `installment_notes` text DEFAULT null,
  `prog_running_status` int(11) NOT NULL DEFAULT 0 COMMENT '0: not yet, 1: ongoing, 2: done',
  `prog_start_date` date DEFAULT null,
  `prog_end_date` date DEFAULT null,
  `empl_id` varchar(36) DEFAULT null,
  `success_date` date DEFAULT null,
  `failed_date` date DEFAULT null,
  `refund_date` date DEFAULT null,
  `refund_notes` text DEFAULT null,
  `timesheet_link` text DEFAULT null,
  `trial_date` date DEFAULT null,
  `session_tutor` int(11) DEFAULT null COMMENT 'for academic tutor only',
  `registration_type` ENUM ('FE', 'I') DEFAULT null COMMENT 'FE: Form Embed, I: Import',
  `referral_code` varchar(255) DEFAULT null COMMENT 'Referral code is a unique code from client data',
  `agreement` text DEFAULT null COMMENT 'file path',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_client_prog_log_mail` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `clientprog_id` bigint(20) NOT NULL,
  `sent_status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_client_relation` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `parent_id` varchar(36) NOT NULL,
  `child_id` varchar(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_client_roles` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `client_id` varchar(36) NOT NULL,
  `role_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_corp` (
  `corp_id` char(9) PRIMARY KEY NOT NULL,
  `corp_name` varchar(255) DEFAULT null,
  `corp_industry` varchar(255) DEFAULT null,
  `corp_mail` varchar(255) DEFAULT null,
  `corp_phone` varchar(255) DEFAULT null,
  `corp_insta` varchar(255) DEFAULT null,
  `corp_site` varchar(255) DEFAULT null,
  `corp_region` varchar(255) DEFAULT null,
  `corp_address` text DEFAULT null,
  `corp_note` text DEFAULT null,
  `corp_password` varchar(255) DEFAULT null,
  `country_type` ENUM ('Indonesia', 'Overseas') NOT NULL,
  `type` ENUM ('Corporate', 'Individual Professional', 'Tutoring Center', 'Course Center', 'Agent', 'Community', 'NGO') NOT NULL,
  `partnership_type` ENUM ('Market Sharing', 'Program Collaborator', 'Internship', 'External Mentor') DEFAULT null,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_corp_partner_event` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `corp_id` char(9) NOT NULL,
  `event_id` char(8) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_corp_pic` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `corp_id` char(9) NOT NULL,
  `pic_name` varchar(255) NOT NULL,
  `pic_mail` varchar(255) DEFAULT null,
  `pic_linkedin` varchar(255) DEFAULT null,
  `pic_phone` varchar(255) DEFAULT null,
  `is_pic` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_country_categorization_lead` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `value` varchar(255) NOT NULL,
  `description` text DEFAULT null,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_curriculum` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_department` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `dept_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_dreams_major` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `client_id` varchar(36) NOT NULL,
  `major_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_dreams_uni` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `univ_id` char(8) NOT NULL,
  `client_id` varchar(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_eduf_lead` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `sch_id` char(8) DEFAULT null,
  `corp_id` char(9) DEFAULT null,
  `title` varchar(255) DEFAULT null,
  `location` text NOT NULL,
  `intr_pic` varchar(36) NOT NULL,
  `ext_pic_name` varchar(255) DEFAULT null,
  `ext_pic_mail` varchar(255) DEFAULT null,
  `ext_pic_phone` varchar(255) DEFAULT null,
  `first_discussion_date` date DEFAULT null,
  `last_discussion_date` date DEFAULT null,
  `event_start` date DEFAULT null,
  `event_end` date DEFAULT null,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT null,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_eduf_review` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `eduf_id` bigint(20) NOT NULL,
  `reviewer_name` varchar(36) NOT NULL,
  `score` varchar(50) NOT NULL,
  `review` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_events` (
  `event_id` char(11) PRIMARY KEY NOT NULL,
  `event_title` varchar(255) NOT NULL,
  `event_description` text DEFAULT null,
  `event_location` varchar(255) DEFAULT null,
  `type` ENUM ('offline', 'online', 'hybrid') DEFAULT null,
  `event_startdate` datetime DEFAULT null,
  `event_enddate` datetime DEFAULT null,
  `event_target` int(11) DEFAULT null,
  `event_banner` text DEFAULT null,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `category` char(11) DEFAULT null,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_event_pic` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `event_id` char(8) NOT NULL,
  `empl_id` varchar(36) NOT NULL COMMENT 'internal pic',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_event_speaker` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `event_id` char(11) NOT NULL,
  `sch_pic_id` bigint(20) DEFAULT null,
  `univ_pic_id` bigint(20) DEFAULT null,
  `corp_pic_id` bigint(20) DEFAULT null,
  `start_time` datetime DEFAULT null,
  `end_time` datetime DEFAULT null,
  `priority` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_followup` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `clientprog_id` bigint(20) NOT NULL,
  `followup_date` date NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: Not yet, 1: Done',
  `notes` text DEFAULT null,
  `reminder` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_followup_client` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `user_id` varchar(36) NOT NULL,
  `client_id` varchar(36) NOT NULL,
  `followup_date` timestamp NOT NULL DEFAULT (current_timestamp()),
  `notes` text DEFAULT null,
  `minutes_of_meeting` text DEFAULT null,
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '0: Not yet, 1: Done, 2: Pause, 3: Negotiation',
  `reminder_is_sent` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_grade_categorization_lead` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `value` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_initial_program_lead` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_initial_prog_sub_lead` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `initialprogram_id` bigint(20) NOT NULL,
  `subprogram_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_interest_prog` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `client_id` varchar(36) NOT NULL,
  `prog_id` char(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_inv` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `inv_id` char(50) NOT NULL,
  `clientprog_id` bigint(20) DEFAULT null,
  `bundling_id` char(36) DEFAULT null,
  `ref_id` bigint(20) DEFAULT null,
  `inv_category` varchar(50) DEFAULT null,
  `inv_price` bigint(20) DEFAULT null,
  `inv_earlybird` bigint(20) NOT NULL DEFAULT 0,
  `inv_discount` bigint(20) NOT NULL DEFAULT 0,
  `inv_totalprice` bigint(20) NOT NULL DEFAULT 0,
  `inv_words` text DEFAULT null,
  `inv_price_idr` bigint(20) DEFAULT null,
  `inv_earlybird_idr` bigint(20) DEFAULT null,
  `inv_discount_idr` bigint(20) DEFAULT null,
  `inv_totalprice_idr` bigint(20) NOT NULL,
  `inv_words_idr` text DEFAULT null,
  `session` int(11) NOT NULL DEFAULT 0,
  `duration` int(11) NOT NULL DEFAULT 0,
  `inv_paymentmethod` ENUM ('Full Payment', 'Installment') NOT NULL,
  `invoice_date` date DEFAULT null,
  `inv_duedate` date DEFAULT null,
  `inv_notes` text DEFAULT null,
  `inv_tnc` text DEFAULT null,
  `inv_status` int(11) NOT NULL DEFAULT 1 COMMENT '1: success, 2: refund',
  `curs_rate` int(11) NOT NULL DEFAULT 0,
  `currency` ENUM ('gbp', 'usd', 'sgd', 'idr', 'aud', 'myr', 'vnd', 'jpy', 'cny', 'thb') DEFAULT null,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `send_to_client` ENUM ('sent', 'not sent') NOT NULL DEFAULT 'not sent',
  `reminded` int(11) NOT NULL COMMENT 'jumlah reminder terkirim'
);

CREATE TABLE `tbl_invb2b` (
  `invb2b_num` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `invb2b_id` char(50) NOT NULL,
  `schprog_id` bigint(20) DEFAULT null,
  `partnerprog_id` bigint(20) DEFAULT null,
  `ref_id` bigint(20) DEFAULT null,
  `invb2b_price` int(11) DEFAULT null,
  `invb2b_priceidr` int(11) DEFAULT null,
  `invb2b_participants` int(11) DEFAULT null,
  `invb2b_disc` int(11) DEFAULT null,
  `invb2b_discidr` int(11) DEFAULT null,
  `invb2b_totprice` int(11) DEFAULT null,
  `invb2b_totpriceidr` int(11) DEFAULT null,
  `invb2b_words` text DEFAULT null,
  `invb2b_wordsidr` text DEFAULT null,
  `invb2b_date` date NOT NULL,
  `invb2b_duedate` date DEFAULT null,
  `invb2b_pm` varchar(255) NOT NULL,
  `invb2b_notes` text DEFAULT null,
  `invb2b_tnc` text DEFAULT null,
  `invb2b_status` int(11) NOT NULL DEFAULT 1 COMMENT '1: Success, 2: Refund',
  `curs_rate` bigint(20) DEFAULT null,
  `currency` ENUM ('gbp', 'usd', 'sgd', 'idr', 'aud', 'myr', 'vnd', 'jpy', 'cny', 'thb') DEFAULT null,
  `is_full_amount` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `reminded` int(11) NOT NULL COMMENT 'jumlah reminder terkirim'
);

CREATE TABLE `tbl_invdtl` (
  `invdtl_id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `invb2b_id` char(50) DEFAULT null,
  `inv_id` char(50) DEFAULT null,
  `invdtl_installment` varchar(255) DEFAULT null,
  `invdtl_duedate` date DEFAULT null,
  `invdtl_percentage` double(8,2) DEFAULT null,
  `invdtl_amount` int(11) DEFAULT null,
  `invdtl_amountidr` int(11) DEFAULT null,
  `invdtl_status` tinyint(4) NOT NULL DEFAULT 0,
  `invdtl_cursrate` bigint(20) DEFAULT null,
  `invdtl_currency` ENUM ('gbp', 'usd', 'sgd', 'idr', 'aud', 'myr', 'vnd', 'jpy', 'cny', 'thb') DEFAULT null,
  `reminded` int(11) NOT NULL DEFAULT 0 COMMENT 'has been reminded = 1 else 0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_inv_attachment` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `inv_id` char(50) DEFAULT null,
  `invb2b_id` char(50) DEFAULT null,
  `currency` ENUM ('idr', 'other') NOT NULL,
  `sign_status` ENUM ('not yet', 'signed') NOT NULL DEFAULT 'not yet',
  `recipient` varchar(255) DEFAULT null,
  `approve_date` datetime DEFAULT null,
  `send_to_client` ENUM ('not sent', 'sent') NOT NULL DEFAULT 'not sent',
  `attachment` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_lead` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `lead_id` varchar(5) NOT NULL,
  `main_lead` varchar(255) NOT NULL,
  `sub_lead` varchar(255) DEFAULT null,
  `score` bigint(20) NOT NULL DEFAULT 0,
  `department_id` bigint(20) DEFAULT null,
  `color_code` varchar(10) DEFAULT null,
  `note` text DEFAULT null,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_lead_bucket_params` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `bucket_id` char(8) NOT NULL,
  `initialprogram_id` bigint(20) NOT NULL,
  `param_id` bigint(20) NOT NULL,
  `weight_existing_non_mentee` int(11) DEFAULT null,
  `weight_existing_mentee` int(11) DEFAULT null,
  `weight_new` int(11) DEFAULT null,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_mail_log` (
  `identifier` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL COMMENT 'ex: invoice / receipt',
  `target` varchar(255) NOT NULL COMMENT 'ex: partner / client / etc',
  `description` text DEFAULT null,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_main_menus` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `mainmenu_name` varchar(255) NOT NULL,
  `order_no` int(11) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_main_prog` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `prog_name` varchar(255) NOT NULL,
  `prog_status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_major` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_major_categorization_lead` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `value` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_mentor_ic` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `clientprog_id` bigint(20) NOT NULL,
  `user_id` varchar(36) NOT NULL,
  `note` text DEFAULT null,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_menus` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `mainmenu_id` bigint(20) NOT NULL,
  `submenu_name` varchar(255) NOT NULL,
  `submenu_link` text NOT NULL,
  `order_no` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_menusdtl` (
  `menusdtl_id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `menu_id` bigint(20) NOT NULL,
  `department_id` bigint(20) NOT NULL,
  `copy` tinyint(1) NOT NULL DEFAULT 0,
  `export` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_menus_user` (
  `menusdtl_id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `menu_id` bigint(20) NOT NULL,
  `user_id` varchar(36) NOT NULL,
  `copy` tinyint(1) NOT NULL DEFAULT 0,
  `export` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_param_lead` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_partner_agreement` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `corp_id` char(9) NOT NULL,
  `agreement_name` varchar(255) NOT NULL,
  `agreement_type` tinyint(4) NOT NULL COMMENT '0: Referral Mutual Agreement, 1: Partnership Agreement, 2: Speaker Agreement, 3: University Agent',
  `attachment` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `corp_pic` bigint(20) NOT NULL,
  `empl_id` varchar(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_partner_prog` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `corp_id` char(9) NOT NULL,
  `prog_id` char(11) NOT NULL,
  `type` tinyint(4) DEFAULT null,
  `first_discuss` date DEFAULT null,
  `notes` text DEFAULT null,
  `refund_notes` text DEFAULT null,
  `refund_date` date DEFAULT null,
  `status` int(11) NOT NULL COMMENT '0: Pending, 1: Success, 2: Rejected 3: Refund 4: Accepted 5: Cancel',
  `participants` bigint(20) DEFAULT 0,
  `start_date` date DEFAULT null,
  `end_date` date DEFAULT null,
  `denied_date` date DEFAULT null,
  `success_date` date DEFAULT null,
  `cancel_date` date DEFAULT null,
  `accepted_date` date DEFAULT null,
  `pending_date` date DEFAULT null,
  `total_fee` double DEFAULT null,
  `is_corporate_scheme` tinyint(4) NOT NULL,
  `reason_id` bigint(20) DEFAULT null,
  `reason_notes` text DEFAULT null,
  `empl_id` varchar(36) DEFAULT null COMMENT 'ALL-In PIC',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_partner_prog_attachment` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `partner_prog_id` bigint(20) NOT NULL,
  `corprog_file` text NOT NULL,
  `corprog_attach` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_partner_prog_partner` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `partnerprog_id` bigint(20) NOT NULL,
  `corp_id` char(9) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_partner_prog_sch` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `partnerprog_id` bigint(20) NOT NULL,
  `sch_id` char(8) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_partner_prog_univ` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `partnerprog_id` bigint(20) NOT NULL,
  `univ_id` char(8) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_pic_client` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `client_id` varchar(36) NOT NULL,
  `user_id` varchar(36) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0: non active, 1: active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_position` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `position_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_priority_lead` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `weight` double(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_prog` (
  `prog_id` char(11) PRIMARY KEY NOT NULL,
  `main_prog_id` bigint(20) DEFAULT null,
  `sub_prog_id` bigint(20) DEFAULT null,
  `prog_program` varchar(255) DEFAULT null,
  `prog_type` varchar(255) DEFAULT null,
  `prog_mentor` varchar(50) NOT NULL,
  `prog_payment` varchar(25) NOT NULL,
  `prog_scope` ENUM ('public', 'mentee', 'school', 'partner') DEFAULT null,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT (current_timestamp()),
  `updated_at` timestamp NOT NULL DEFAULT (current_timestamp())
);

CREATE TABLE `tbl_program_buckets_params` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `bucket_id` char(8) NOT NULL,
  `initialprogram_id` bigint(20) NOT NULL,
  `param_id` bigint(20) NOT NULL,
  `weight_existing_non_mentee` int(11) DEFAULT null,
  `weight_existing_mentee` int(11) DEFAULT null,
  `weight_new` int(11) DEFAULT null,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_program_lead_library` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `programbucket_id` char(8) DEFAULT null,
  `leadbucket_id` char(8) DEFAULT null,
  `value_category` int(11) NOT NULL,
  `new` tinyint(1) NOT NULL,
  `existing_mentee` tinyint(1) NOT NULL,
  `existing_non_mentee` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_purchase_dtl` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `purchase_id` char(255) NOT NULL,
  `item` varchar(255) NOT NULL,
  `amount` int(11) NOT NULL,
  `price_per_unit` int(11) NOT NULL,
  `notes` varchar(255) DEFAULT null,
  `total` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_purchase_request` (
  `purchase_id` char(8) PRIMARY KEY NOT NULL,
  `requested_by` varchar(36) NOT NULL,
  `purchase_department` bigint(20) NOT NULL,
  `purchase_statusrequest` varchar(255) NOT NULL,
  `purchase_requestdate` date NOT NULL,
  `purchase_notes` text DEFAULT null,
  `purchase_attachment` text DEFAULT null,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_reason` (
  `reason_id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `reason_name` text NOT NULL,
  `type` ENUM ('Program', 'Hot Lead') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_receipt` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `receipt_id` char(50) NOT NULL,
  `receipt_cat` ENUM ('student', 'school', 'partner', 'referral') DEFAULT null,
  `inv_id` char(50) DEFAULT null,
  `invdtl_id` bigint(20) DEFAULT null,
  `invb2b_id` char(50) DEFAULT null,
  `receipt_method` varchar(255) DEFAULT null,
  `receipt_cheque` varchar(50) DEFAULT null,
  `receipt_amount` int(11) DEFAULT null,
  `receipt_words` text DEFAULT null,
  `receipt_amount_idr` int(11) DEFAULT null,
  `receipt_words_idr` text DEFAULT null,
  `receipt_notes` text DEFAULT null,
  `receipt_date` date DEFAULT null,
  `pph23` int(11) DEFAULT null,
  `receipt_status` int(11) NOT NULL DEFAULT 1 COMMENT '1: success, 2: refund',
  `download_other` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: Not Yet, 1: Downloaded',
  `download_idr` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: Not Yet, 1: Downloaded',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_receipt_attachment` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `receipt_id` char(50) NOT NULL,
  `currency` ENUM ('idr', 'other') NOT NULL,
  `sign_status` ENUM ('not yet', 'signed') NOT NULL DEFAULT 'not yet',
  `recipient` varchar(255) DEFAULT null,
  `approve_date` datetime DEFAULT null,
  `send_to_client` ENUM ('not sent', 'sent') NOT NULL DEFAULT 'not sent',
  `attachment` text DEFAULT null,
  `request_status` ENUM ('not yet', 'requested') NOT NULL DEFAULT 'not yet',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_referral` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `partner_id` char(9) NOT NULL,
  `prog_id` char(11) DEFAULT null,
  `empl_id` varchar(36) NOT NULL COMMENT 'Internal PIC',
  `referral_type` ENUM ('In', 'Out') NOT NULL,
  `additional_prog_name` varchar(255) DEFAULT null,
  `number_of_student` bigint(20) NOT NULL DEFAULT 0,
  `currency` char(3) NOT NULL,
  `curs_rate` int(11) DEFAULT null,
  `revenue` bigint(20) NOT NULL DEFAULT 0,
  `revenue_other` int(11) DEFAULT null,
  `ref_date` date NOT NULL,
  `notes` text DEFAULT null,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_refund` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `invb2b_id` char(50) DEFAULT null,
  `inv_id` char(50) DEFAULT null,
  `total_payment` int(11) NOT NULL,
  `total_paid` int(11) NOT NULL,
  `refund_amount` double NOT NULL,
  `percentage_refund` double NOT NULL,
  `tax_amount` double NOT NULL,
  `tax_percentage` double NOT NULL,
  `total_refunded` double NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_reminder` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `foreign_identifier` bigint(20) NOT NULL,
  `content` varchar(255) NOT NULL,
  `sent_status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_roles` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `role_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_sales_target` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `main_prog_id` bigint(20) DEFAULT null,
  `prog_id` char(11) DEFAULT null,
  `month_year` date NOT NULL,
  `total_participant` int(11) NOT NULL,
  `total_target` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_sch` (
  `sch_id` char(8) PRIMARY KEY NOT NULL,
  `uuid` varchar(39) NOT NULL,
  `sch_name` varchar(255) DEFAULT null,
  `sch_type` varchar(255) DEFAULT null,
  `sch_mail` varchar(255) DEFAULT null,
  `sch_phone` varchar(255) DEFAULT null,
  `sch_insta` varchar(255) DEFAULT null,
  `sch_city` varchar(255) DEFAULT null,
  `sch_location` varchar(255) DEFAULT null,
  `sch_score` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `is_verified` ENUM ('Y', 'N') NOT NULL DEFAULT 'N',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_schdetail` (
  `schdetail_id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `sch_id` char(8) NOT NULL,
  `schdetail_fullname` varchar(50) DEFAULT null,
  `schdetail_email` varchar(50) DEFAULT null,
  `schdetail_grade` varchar(50) DEFAULT null,
  `schdetail_position` varchar(50) DEFAULT null,
  `schdetail_phone` varchar(25) DEFAULT null,
  `is_pic` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_school_categorization_lead` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `value` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_sch_aliases` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `sch_id` char(8) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_sch_curriculum` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `sch_id` char(8) NOT NULL,
  `curriculum_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_sch_event` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `sch_id` char(8) NOT NULL,
  `event_id` char(8) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_sch_prog` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `sch_id` char(8) NOT NULL,
  `prog_id` char(11) NOT NULL,
  `first_discuss` date DEFAULT null,
  `status` int(11) NOT NULL COMMENT '0: Pending, 1: Success, 2: Rejected 3: Refund 4: Accepted 5: Cancel',
  `notes` text DEFAULT null,
  `notes_detail` text DEFAULT null,
  `refund_notes` text DEFAULT null,
  `refund_date` date DEFAULT null,
  `running_status` ENUM ('Not yet', 'On going', 'Done') DEFAULT null,
  `total_hours` int(11) DEFAULT null,
  `total_fee` double DEFAULT null,
  `participants` int(11) DEFAULT null,
  `place` varchar(255) DEFAULT null,
  `end_program_date` date DEFAULT null,
  `start_program_date` date DEFAULT null,
  `success_date` date DEFAULT null,
  `cancel_date` date DEFAULT null,
  `accepted_date` date DEFAULT null,
  `pending_date` date DEFAULT null,
  `reason_id` bigint(20) DEFAULT null,
  `reason_notes` text DEFAULT null,
  `denied_date` date DEFAULT null,
  `empl_id` varchar(36) DEFAULT null COMMENT 'ALL-In PIC',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_sch_prog_attach` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `schprog_id` bigint(20) NOT NULL,
  `schprog_file` text NOT NULL,
  `schprog_attach` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_sch_prog_partner` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `schprog_id` bigint(20) NOT NULL,
  `corp_id` char(9) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_sch_prog_school` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `schprog_id` bigint(20) NOT NULL,
  `sch_id` char(8) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_sch_prog_univ` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `schprog_id` bigint(20) NOT NULL,
  `univ_id` char(8) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_sch_visit` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `sch_id` char(8) NOT NULL,
  `internal_pic` varchar(36) NOT NULL,
  `school_pic` bigint(20) NOT NULL,
  `visit_date` date NOT NULL,
  `notes` text DEFAULT null,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` ENUM ('waiting', 'visited') NOT NULL DEFAULT 'waiting'
);

CREATE TABLE `tbl_scoring_param` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `category` varchar(255) NOT NULL,
  `max_score` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_seasonal_lead` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `prog_id` char(11) NOT NULL,
  `start` date DEFAULT null,
  `end` date DEFAULT null,
  `sales_date` date DEFAULT null COMMENT 'The date when sales department can start selling',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_speaker` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `sp_name` varchar(255) NOT NULL,
  `sp_title` varchar(255) DEFAULT null,
  `sp_institution` varchar(255) DEFAULT null,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_status_categorization_lead` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `value` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_subjects` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_sub_prog` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `main_prog_id` bigint(20) NOT NULL,
  `sub_prog_name` varchar(255) NOT NULL,
  `sub_prog_status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_tag` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `score` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_univ` (
  `univ_id` char(8) PRIMARY KEY NOT NULL,
  `univ_name` varchar(255) DEFAULT null,
  `univ_address` text DEFAULT null,
  `univ_country` bigint(20) DEFAULT null,
  `univ_email` varchar(255) DEFAULT null,
  `univ_phone` varchar(255) DEFAULT null,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_univ_event` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `univ_id` char(8) NOT NULL,
  `event_id` char(8) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_univ_pic` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `univ_id` char(8) NOT NULL,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT null,
  `email` varchar(255) DEFAULT null,
  `is_pic` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_user_educations` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `user_id` varchar(36) NOT NULL,
  `univ_id` char(8) NOT NULL,
  `major_id` bigint(20) NOT NULL,
  `degree` varchar(255) DEFAULT null,
  `graduation_date` date DEFAULT null,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_user_roles` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `user_id` varchar(36) NOT NULL,
  `role_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_user_subjects` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `user_role_id` bigint(20) NOT NULL,
  `subject_id` bigint(20) NOT NULL,
  `year` year(4) DEFAULT null,
  `agreement` text DEFAULT null,
  `head` int(11) DEFAULT null,
  `additional_fee` bigint(20) DEFAULT null,
  `grade` varchar(255) DEFAULT null,
  `fee_individual` bigint(20) DEFAULT null,
  `fee_group` bigint(20) DEFAULT null,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_user_type` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `type_name` varchar(30) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_user_type_detail` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `user_type_id` bigint(20) NOT NULL,
  `user_id` varchar(36) NOT NULL,
  `department_id` bigint(20) DEFAULT null,
  `start_date` date DEFAULT null,
  `end_date` date DEFAULT null,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deactivated_at` datetime DEFAULT null
);

CREATE TABLE `tbl_vendor` (
  `vendor_id` char(7) PRIMARY KEY NOT NULL,
  `vendor_name` varchar(255) DEFAULT null,
  `vendor_address` varchar(255) DEFAULT null,
  `vendor_phone` varchar(255) DEFAULT null,
  `vendor_type` varchar(255) DEFAULT null,
  `vendor_material` varchar(255) DEFAULT null,
  `vendor_size` varchar(255) DEFAULT null,
  `vendor_unitprice` int(11) NOT NULL DEFAULT 0,
  `vendor_processingtime` varchar(50) DEFAULT null,
  `vendor_notes` text DEFAULT null,
  `created_at` timestamp NOT NULL DEFAULT (current_timestamp()),
  `updated_at` timestamp NOT NULL DEFAULT (current_timestamp())
);

CREATE TABLE `tbl_vendor_type` (
  `id` bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `tbl_volunt` (
  `volunt_id` char(8) PRIMARY KEY NOT NULL,
  `volunt_firstname` varchar(255) DEFAULT null,
  `volunt_lastname` varchar(255) DEFAULT null,
  `volunt_address` varchar(255) DEFAULT null,
  `volunt_mail` varchar(255) DEFAULT null,
  `volunt_phone` varchar(255) DEFAULT null,
  `volunt_idcard` varchar(255) DEFAULT null,
  `volunt_npwp` varchar(255) DEFAULT null,
  `empl_insurance` varchar(255) DEFAULT null,
  `health_insurance` varchar(255) DEFAULT null,
  `volunt_npwp_number` bigint(20) DEFAULT null,
  `volunt_nik` bigint(20) NOT NULL,
  `volunt_bank_accnumber` bigint(20) NOT NULL,
  `volunt_bank_accname` varchar(255) NOT NULL,
  `volunt_cv` varchar(255) NOT NULL,
  `volunt_status` int(2) DEFAULT 1,
  `volunt_lasteditdate` datetime DEFAULT null,
  `position_id` bigint(20) DEFAULT null,
  `major_id` bigint(20) DEFAULT null,
  `univ_id` char(8) DEFAULT null,
  `created_at` timestamp NOT NULL DEFAULT (current_timestamp()),
  `updated_at` timestamp NOT NULL DEFAULT (current_timestamp())
);

CREATE TABLE `users` (
  `number` bigint(20) NOT NULL,
  `id` varchar(36) PRIMARY KEY NOT NULL,
  `nip` varchar(30) DEFAULT null,
  `extended_id` varchar(15) DEFAULT null,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) DEFAULT null,
  `address` varchar(255) DEFAULT null,
  `email` varchar(255) DEFAULT null,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `phone` varchar(250) DEFAULT null,
  `emergency_contact` varchar(25) DEFAULT null,
  `datebirth` date DEFAULT null,
  `position_id` bigint(20) DEFAULT null,
  `password` text DEFAULT null,
  `hiredate` date DEFAULT null,
  `nik` bigint(20) DEFAULT null,
  `idcard` text DEFAULT null,
  `cv` text DEFAULT null,
  `bankname` varchar(50) DEFAULT null,
  `bankacc` varchar(25) DEFAULT null,
  `npwp` varchar(30) DEFAULT null,
  `tax` text DEFAULT null,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `health_insurance` varchar(255) DEFAULT null,
  `empl_insurance` varchar(255) DEFAULT null,
  `export` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT null,
  `remember_token` varchar(100) DEFAULT null,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
);

CREATE INDEX `tbl_acad_tutor_dtl_clientprog_id_foreign` ON `tbl_acad_tutor_dtl` (`clientprog_id`);

CREATE INDEX `tbl_agenda_speaker_event_id_foreign` ON `tbl_agenda_speaker` (`event_id`);

CREATE INDEX `tbl_agenda_speaker_sch_prog_id_foreign` ON `tbl_agenda_speaker` (`sch_prog_id`);

CREATE INDEX `tbl_agenda_speaker_partner_prog_id_foreign` ON `tbl_agenda_speaker` (`partner_prog_id`);

CREATE INDEX `tbl_agenda_speaker_sch_pic_id_foreign` ON `tbl_agenda_speaker` (`sch_pic_id`);

CREATE INDEX `tbl_agenda_speaker_univ_pic_id_foreign` ON `tbl_agenda_speaker` (`univ_pic_id`);

CREATE INDEX `tbl_agenda_speaker_partner_pic_id_foreign` ON `tbl_agenda_speaker` (`partner_pic_id`);

CREATE INDEX `tbl_agenda_speaker_empl_id_foreign` ON `tbl_agenda_speaker` (`empl_id`);

CREATE INDEX `tbl_agenda_speaker_eduf_id_foreign` ON `tbl_agenda_speaker` (`eduf_id`);

CREATE INDEX `tbl_asset_returned_asset_used_id_foreign` ON `tbl_asset_returned` (`asset_used_id`);

CREATE INDEX `tbl_asset_used_asset_id_foreign` ON `tbl_asset_used` (`asset_id`);

CREATE INDEX `tbl_asset_used_user_id_foreign` ON `tbl_asset_used` (`user_id`);

CREATE INDEX `tbl_bundling_dtl_bundling_id_foreign` ON `tbl_bundling_dtl` (`bundling_id`);

CREATE INDEX `tbl_bundling_dtl_clientprog_id_foreign` ON `tbl_bundling_dtl` (`clientprog_id`);

CREATE INDEX `tbl_client_sch_id_foreign` ON `tbl_client` (`sch_id`);

CREATE INDEX `tbl_client_eduf_id_foreign` ON `tbl_client` (`eduf_id`);

CREATE INDEX `tbl_client_event_id_foreign` ON `tbl_client` (`event_id`);

CREATE INDEX `tbl_client_lead_id_foreign` ON `tbl_client` (`lead_id`);

CREATE INDEX `tbl_client_abrcountry_client_id_foreign` ON `tbl_client_abrcountry` (`client_id`);

CREATE INDEX `tbl_client_abrcountry_tag_id_foreign` ON `tbl_client_abrcountry` (`tag_id`);

CREATE INDEX `tbl_client_acceptance_client_id_foreign` ON `tbl_client_acceptance` (`client_id`);

CREATE INDEX `tbl_client_acceptance_univ_id_foreign` ON `tbl_client_acceptance` (`univ_id`);

CREATE INDEX `tbl_client_acceptance_major_id_foreign` ON `tbl_client_acceptance` (`major_id`);

CREATE INDEX `tbl_client_additional_info_client_id_foreign` ON `tbl_client_additional_info` (`client_id`);

CREATE INDEX `tbl_client_event_event_id_foreign` ON `tbl_client_event` (`event_id`);

CREATE INDEX `tbl_client_event_lead_id_foreign` ON `tbl_client_event` (`lead_id`);

CREATE INDEX `tbl_client_event_eduf_id_foreign` ON `tbl_client_event` (`eduf_id`);

CREATE INDEX `tbl_client_event_client_id_foreign` ON `tbl_client_event` (`client_id`);

CREATE INDEX `tbl_client_event_partner_id_foreign` ON `tbl_client_event` (`partner_id`);

CREATE INDEX `tbl_client_event_child_id_foreign` ON `tbl_client_event` (`child_id`);

CREATE INDEX `tbl_client_event_parent_id_foreign` ON `tbl_client_event` (`parent_id`);

CREATE INDEX `tbl_client_event_log_mail_clientevent_id_foreign` ON `tbl_client_event_log_mail` (`clientevent_id`);

CREATE INDEX `tbl_client_event_log_mail_client_id_foreign` ON `tbl_client_event_log_mail` (`client_id`);

CREATE INDEX `tbl_client_event_log_mail_event_id_foreign` ON `tbl_client_event_log_mail` (`event_id`);

CREATE INDEX `tbl_client_event_log_mail_child_id_foreign` ON `tbl_client_event_log_mail` (`child_id`);

CREATE INDEX `tbl_client_lead_tracking_client_id_foreign` ON `tbl_client_lead_tracking` (`client_id`);

CREATE INDEX `tbl_client_lead_tracking_initialprogram_id_foreign` ON `tbl_client_lead_tracking` (`initialprogram_id`);

CREATE INDEX `tbl_client_lead_tracking_reason_id_foreign` ON `tbl_client_lead_tracking` (`reason_id`);

CREATE INDEX `tbl_client_mentor_user_id_foreign` ON `tbl_client_mentor` (`user_id`);

CREATE INDEX `tbl_client_mentor_clientprog_id_foreign` ON `tbl_client_mentor` (`clientprog_id`);

CREATE INDEX `tbl_client_prog_lead_id_foreign` ON `tbl_client_prog` (`lead_id`);

CREATE INDEX `tbl_client_prog_eduf_lead_id_foreign` ON `tbl_client_prog` (`eduf_lead_id`);

CREATE INDEX `tbl_client_prog_clientevent_id_foreign` ON `tbl_client_prog` (`clientevent_id`);

CREATE INDEX `tbl_client_prog_reason_id_foreign` ON `tbl_client_prog` (`reason_id`);

CREATE INDEX `tbl_client_prog_empl_id_foreign` ON `tbl_client_prog` (`empl_id`);

CREATE INDEX `tbl_client_prog_client_id_foreign` ON `tbl_client_prog` (`client_id`);

CREATE INDEX `tbl_client_prog_partner_id_foreign` ON `tbl_client_prog` (`partner_id`);

CREATE INDEX `tbl_client_prog_prog_id_foreign` ON `tbl_client_prog` (`prog_id`);

CREATE INDEX `tbl_client_prog_log_mail_clientprog_id_foreign` ON `tbl_client_prog_log_mail` (`clientprog_id`);

CREATE INDEX `tbl_client_relation_parent_id_foreign` ON `tbl_client_relation` (`parent_id`);

CREATE INDEX `tbl_client_relation_child_id_foreign` ON `tbl_client_relation` (`child_id`);

CREATE INDEX `tbl_client_roles_client_id_foreign` ON `tbl_client_roles` (`client_id`);

CREATE INDEX `tbl_client_roles_role_id_foreign` ON `tbl_client_roles` (`role_id`);

CREATE INDEX `tbl_corp_partner_event_corp_id_foreign` ON `tbl_corp_partner_event` (`corp_id`);

CREATE INDEX `tbl_corp_partner_event_event_id_foreign` ON `tbl_corp_partner_event` (`event_id`);

CREATE INDEX `tbl_corp_pic_corp_id_foreign` ON `tbl_corp_pic` (`corp_id`);

CREATE INDEX `tbl_dreams_major_client_id_foreign` ON `tbl_dreams_major` (`client_id`);

CREATE INDEX `tbl_dreams_major_major_id_foreign` ON `tbl_dreams_major` (`major_id`);

CREATE INDEX `tbl_dreams_uni_univ_id_foreign` ON `tbl_dreams_uni` (`univ_id`);

CREATE INDEX `tbl_dreams_uni_client_id_foreign` ON `tbl_dreams_uni` (`client_id`);

CREATE INDEX `tbl_eduf_lead_sch_id_foreign` ON `tbl_eduf_lead` (`sch_id`);

CREATE INDEX `tbl_eduf_lead_intr_pic_foreign` ON `tbl_eduf_lead` (`intr_pic`);

CREATE INDEX `tbl_eduf_lead_corp_id_foreign` ON `tbl_eduf_lead` (`corp_id`);

CREATE INDEX `tbl_eduf_review_eduf_id_foreign` ON `tbl_eduf_review` (`eduf_id`);

CREATE INDEX `tbl_eduf_review_reviewer_name_foreign` ON `tbl_eduf_review` (`reviewer_name`);

CREATE INDEX `tbl_events_category_foreign` ON `tbl_events` (`category`);

CREATE INDEX `tbl_event_pic_event_id_foreign` ON `tbl_event_pic` (`event_id`);

CREATE INDEX `tbl_event_pic_empl_id_foreign` ON `tbl_event_pic` (`empl_id`);

CREATE INDEX `tbl_event_speaker_event_id_foreign` ON `tbl_event_speaker` (`event_id`);

CREATE INDEX `tbl_event_speaker_sch_pic_id_foreign` ON `tbl_event_speaker` (`sch_pic_id`);

CREATE INDEX `tbl_event_speaker_univ_pic_id_foreign` ON `tbl_event_speaker` (`univ_pic_id`);

CREATE INDEX `tbl_event_speaker_corp_pic_id_foreign` ON `tbl_event_speaker` (`corp_pic_id`);

CREATE INDEX `tbl_followup_clientprog_id_foreign` ON `tbl_followup` (`clientprog_id`);

CREATE INDEX `tbl_followup_client_user_id_foreign` ON `tbl_followup_client` (`user_id`);

CREATE INDEX `tbl_followup_client_client_id_foreign` ON `tbl_followup_client` (`client_id`);

CREATE INDEX `tbl_initial_prog_sub_lead_initialprogram_id_foreign` ON `tbl_initial_prog_sub_lead` (`initialprogram_id`);

CREATE INDEX `tbl_initial_prog_sub_lead_subprogram_id_foreign` ON `tbl_initial_prog_sub_lead` (`subprogram_id`);

CREATE INDEX `tbl_interest_prog_client_id_foreign` ON `tbl_interest_prog` (`client_id`);

CREATE INDEX `tbl_interest_prog_prog_id_foreign` ON `tbl_interest_prog` (`prog_id`);

CREATE UNIQUE INDEX `tbl_inv_inv_id_unique` ON `tbl_inv` (`inv_id`);

CREATE INDEX `tbl_inv_clientprog_id_foreign` ON `tbl_inv` (`clientprog_id`);

CREATE INDEX `tbl_inv_ref_id_foreign` ON `tbl_inv` (`ref_id`);

CREATE INDEX `tbl_inv_bundling_id_foreign` ON `tbl_inv` (`bundling_id`);

CREATE UNIQUE INDEX `tbl_invb2b_invb2b_id_unique` ON `tbl_invb2b` (`invb2b_id`);

CREATE INDEX `tbl_invb2b_schprog_id_foreign` ON `tbl_invb2b` (`schprog_id`);

CREATE INDEX `tbl_invb2b_partnerprog_id_foreign` ON `tbl_invb2b` (`partnerprog_id`);

CREATE INDEX `tbl_invb2b_ref_id_foreign` ON `tbl_invb2b` (`ref_id`);

CREATE INDEX `tbl_invdtl_invb2b_id_foreign` ON `tbl_invdtl` (`invb2b_id`);

CREATE INDEX `tbl_invdtl_inv_id_foreign` ON `tbl_invdtl` (`inv_id`);

CREATE INDEX `tbl_inv_attachment_inv_id_foreign` ON `tbl_inv_attachment` (`inv_id`);

CREATE INDEX `tbl_inv_attachment_invb2b_id_foreign` ON `tbl_inv_attachment` (`invb2b_id`);

CREATE UNIQUE INDEX `extended_id` ON `tbl_lead` (`lead_id`);

CREATE INDEX `tbl_lead_department_id_foreign` ON `tbl_lead` (`department_id`);

CREATE UNIQUE INDEX `tbl_lead_bucket_params_bucket_id_unique` ON `tbl_lead_bucket_params` (`bucket_id`);

CREATE INDEX `tbl_lead_bucket_params_initialprogram_id_foreign` ON `tbl_lead_bucket_params` (`initialprogram_id`);

CREATE INDEX `tbl_lead_bucket_params_param_id_foreign` ON `tbl_lead_bucket_params` (`param_id`);

CREATE INDEX `tbl_mentor_ic_clientprog_id_foreign` ON `tbl_mentor_ic` (`clientprog_id`);

CREATE INDEX `tbl_mentor_ic_user_id_foreign` ON `tbl_mentor_ic` (`user_id`);

CREATE INDEX `tbl_menus_mainmenu_id_foreign` ON `tbl_menus` (`mainmenu_id`);

CREATE INDEX `tbl_menusdtl_department_id_foreign` ON `tbl_menusdtl` (`department_id`);

CREATE INDEX `tbl_menusdtl_menu_id_foreign` ON `tbl_menusdtl` (`menu_id`);

CREATE INDEX `tbl_menus_user_menu_id_foreign` ON `tbl_menus_user` (`menu_id`);

CREATE INDEX `tbl_menus_user_user_id_foreign` ON `tbl_menus_user` (`user_id`);

CREATE INDEX `tbl_partner_agreement_corp_id_foreign` ON `tbl_partner_agreement` (`corp_id`);

CREATE INDEX `tbl_partner_agreement_corp_pic_foreign` ON `tbl_partner_agreement` (`corp_pic`);

CREATE INDEX `tbl_partner_agreement_empl_id_foreign` ON `tbl_partner_agreement` (`empl_id`);

CREATE INDEX `tbl_partner_prog_corp_id_foreign` ON `tbl_partner_prog` (`corp_id`);

CREATE INDEX `tbl_partner_prog_prog_id_foreign` ON `tbl_partner_prog` (`prog_id`);

CREATE INDEX `tbl_partner_prog_empl_id_foreign` ON `tbl_partner_prog` (`empl_id`);

CREATE INDEX `tbl_partner_prog_reason_id_foreign` ON `tbl_partner_prog` (`reason_id`);

CREATE INDEX `tbl_partner_prog_attachment_partner_prog_id_foreign` ON `tbl_partner_prog_attachment` (`partner_prog_id`);

CREATE INDEX `tbl_partner_prog_partner_partnerprog_id_foreign` ON `tbl_partner_prog_partner` (`partnerprog_id`);

CREATE INDEX `tbl_partner_prog_partner_corp_id_foreign` ON `tbl_partner_prog_partner` (`corp_id`);

CREATE INDEX `tbl_partner_prog_sch_partnerprog_id_foreign` ON `tbl_partner_prog_sch` (`partnerprog_id`);

CREATE INDEX `tbl_partner_prog_sch_sch_id_foreign` ON `tbl_partner_prog_sch` (`sch_id`);

CREATE INDEX `tbl_partner_prog_univ_partnerprog_id_foreign` ON `tbl_partner_prog_univ` (`partnerprog_id`);

CREATE INDEX `tbl_partner_prog_univ_univ_id_foreign` ON `tbl_partner_prog_univ` (`univ_id`);

CREATE INDEX `tbl_pic_client_client_id_foreign` ON `tbl_pic_client` (`client_id`);

CREATE INDEX `tbl_pic_client_user_id_foreign` ON `tbl_pic_client` (`user_id`);

CREATE INDEX `tbl_prog_sub_prog_id_foreign` ON `tbl_prog` (`sub_prog_id`);

CREATE INDEX `tbl_prog_main_prog_id_foreign` ON `tbl_prog` (`main_prog_id`);

CREATE UNIQUE INDEX `tbl_program_buckets_params_bucket_id_unique` ON `tbl_program_buckets_params` (`bucket_id`);

CREATE INDEX `tbl_program_buckets_params_initialprogram_id_foreign` ON `tbl_program_buckets_params` (`initialprogram_id`);

CREATE INDEX `tbl_program_buckets_params_param_id_foreign` ON `tbl_program_buckets_params` (`param_id`);

CREATE INDEX `tbl_program_lead_library_leadbucket_id_foreign` ON `tbl_program_lead_library` (`leadbucket_id`);

CREATE INDEX `tbl_program_lead_library_programbucket_id_foreign` ON `tbl_program_lead_library` (`programbucket_id`);

CREATE INDEX `tbl_purchase_dtl_purchase_id_foreign` ON `tbl_purchase_dtl` (`purchase_id`);

CREATE INDEX `tbl_purchase_request_purchase_department_foreign` ON `tbl_purchase_request` (`purchase_department`);

CREATE INDEX `tbl_purchase_request_requested_by_foreign` ON `tbl_purchase_request` (`requested_by`);

CREATE UNIQUE INDEX `tbl_receipt_receipt_id_unique` ON `tbl_receipt` (`receipt_id`);

CREATE INDEX `tbl_receipt_inv_id_foreign` ON `tbl_receipt` (`inv_id`);

CREATE INDEX `tbl_receipt_invdtl_id_foreign` ON `tbl_receipt` (`invdtl_id`);

CREATE INDEX `tbl_receipt_invb2b_id_foreign` ON `tbl_receipt` (`invb2b_id`);

CREATE INDEX `tbl_receipt_attachment_receipt_id_foreign` ON `tbl_receipt_attachment` (`receipt_id`);

CREATE INDEX `tbl_referral_partner_id_foreign` ON `tbl_referral` (`partner_id`);

CREATE INDEX `tbl_referral_prog_id_foreign` ON `tbl_referral` (`prog_id`);

CREATE INDEX `tbl_referral_empl_id_foreign` ON `tbl_referral` (`empl_id`);

CREATE INDEX `tbl_refund_invb2b_id_foreign` ON `tbl_refund` (`invb2b_id`);

CREATE INDEX `tbl_refund_inv_id_foreign` ON `tbl_refund` (`inv_id`);

CREATE INDEX `tbl_sales_target_prog_id_foreign` ON `tbl_sales_target` (`prog_id`);

CREATE INDEX `tbl_sales_target_main_prog_id_foreign` ON `tbl_sales_target` (`main_prog_id`);

CREATE UNIQUE INDEX `tbl_sch_uuid_unique` ON `tbl_sch` (`uuid`);

CREATE INDEX `sch_id` ON `tbl_schdetail` (`sch_id`);

CREATE INDEX `tbl_sch_aliases_sch_id_foreign` ON `tbl_sch_aliases` (`sch_id`);

CREATE INDEX `tbl_sch_curriculum_sch_id_foreign` ON `tbl_sch_curriculum` (`sch_id`);

CREATE INDEX `tbl_sch_curriculum_curriculum_id_foreign` ON `tbl_sch_curriculum` (`curriculum_id`);

CREATE INDEX `tbl_sch_event_sch_id_foreign` ON `tbl_sch_event` (`sch_id`);

CREATE INDEX `tbl_sch_event_event_id_foreign` ON `tbl_sch_event` (`event_id`);

CREATE INDEX `tbl_sch_prog_sch_id_foreign` ON `tbl_sch_prog` (`sch_id`);

CREATE INDEX `tbl_sch_prog_prog_id_foreign` ON `tbl_sch_prog` (`prog_id`);

CREATE INDEX `tbl_sch_prog_empl_id_foreign` ON `tbl_sch_prog` (`empl_id`);

CREATE INDEX `tbl_sch_prog_reason_id_foreign` ON `tbl_sch_prog` (`reason_id`);

CREATE INDEX `tbl_sch_prog_attach_schprog_id_foreign` ON `tbl_sch_prog_attach` (`schprog_id`);

CREATE INDEX `tbl_sch_prog_partner_schprog_id_foreign` ON `tbl_sch_prog_partner` (`schprog_id`);

CREATE INDEX `tbl_sch_prog_partner_corp_id_foreign` ON `tbl_sch_prog_partner` (`corp_id`);

CREATE INDEX `tbl_sch_prog_school_schprog_id_foreign` ON `tbl_sch_prog_school` (`schprog_id`);

CREATE INDEX `tbl_sch_prog_school_sch_id_foreign` ON `tbl_sch_prog_school` (`sch_id`);

CREATE INDEX `tbl_sch_prog_univ_schprog_id_foreign` ON `tbl_sch_prog_univ` (`schprog_id`);

CREATE INDEX `tbl_sch_prog_univ_univ_id_foreign` ON `tbl_sch_prog_univ` (`univ_id`);

CREATE INDEX `tbl_sch_visit_sch_id_foreign` ON `tbl_sch_visit` (`sch_id`);

CREATE INDEX `tbl_sch_visit_internal_pic_foreign` ON `tbl_sch_visit` (`internal_pic`);

CREATE INDEX `tbl_sch_visit_school_pic_foreign` ON `tbl_sch_visit` (`school_pic`);

CREATE INDEX `tbl_seasonal_lead_prog_id_foreign` ON `tbl_seasonal_lead` (`prog_id`);

CREATE INDEX `tbl_sub_prog_main_prog_id_foreign` ON `tbl_sub_prog` (`main_prog_id`);

CREATE INDEX `tbl_univ_tag_foreign` ON `tbl_univ` (`univ_country`);

CREATE INDEX `tbl_univ_event_event_id_foreign` ON `tbl_univ_event` (`event_id`);

CREATE INDEX `tbl_univ_event_univ_id_foreign` ON `tbl_univ_event` (`univ_id`);

CREATE INDEX `tbl_univ_pic_univ_id_foreign` ON `tbl_univ_pic` (`univ_id`);

CREATE INDEX `tbl_user_educations_user_id_foreign` ON `tbl_user_educations` (`user_id`);

CREATE INDEX `tbl_user_educations_univ_id_foreign` ON `tbl_user_educations` (`univ_id`);

CREATE INDEX `tbl_user_educations_major_id_foreign` ON `tbl_user_educations` (`major_id`);

CREATE INDEX `tbl_user_roles_user_id_foreign` ON `tbl_user_roles` (`user_id`);

CREATE INDEX `tbl_user_roles_role_id_foreign` ON `tbl_user_roles` (`role_id`);

CREATE INDEX `tbl_user_subjects_user_role_id_foreign` ON `tbl_user_subjects` (`user_role_id`);

CREATE INDEX `tbl_user_subjects_subject_id_foreign` ON `tbl_user_subjects` (`subject_id`);

CREATE INDEX `tbl_user_type_detail_user_type_id_foreign` ON `tbl_user_type_detail` (`user_type_id`);

CREATE INDEX `tbl_user_type_detail_user_id_foreign` ON `tbl_user_type_detail` (`user_id`);

CREATE INDEX `tbl_user_type_detail_department_id_foreign` ON `tbl_user_type_detail` (`department_id`);

CREATE INDEX `tbl_volunt_univ_id_foreign` ON `tbl_volunt` (`univ_id`);

CREATE INDEX `tbl_volunt_major_id_foreign` ON `tbl_volunt` (`major_id`);

CREATE INDEX `tbl_volunt_position_id_foreign` ON `tbl_volunt` (`position_id`);

CREATE UNIQUE INDEX `users_email_unique` ON `users` (`email`);

CREATE UNIQUE INDEX `extended_id` ON `users` (`extended_id`);

CREATE UNIQUE INDEX `nip` ON `users` (`nip`);

CREATE INDEX `users_position_id_foreign` ON `users` (`position_id`);

ALTER TABLE `tbl_acad_tutor_dtl` ADD CONSTRAINT `tbl_acad_tutor_dtl_clientprog_id_foreign` FOREIGN KEY (`clientprog_id`) REFERENCES `tbl_client_prog` (`clientprog_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_agenda_speaker` ADD CONSTRAINT `tbl_agenda_speaker_eduf_id_foreign` FOREIGN KEY (`eduf_id`) REFERENCES `tbl_eduf_lead` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_agenda_speaker` ADD CONSTRAINT `tbl_agenda_speaker_empl_id_foreign` FOREIGN KEY (`empl_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_agenda_speaker` ADD CONSTRAINT `tbl_agenda_speaker_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `tbl_events` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_agenda_speaker` ADD CONSTRAINT `tbl_agenda_speaker_partner_pic_id_foreign` FOREIGN KEY (`partner_pic_id`) REFERENCES `tbl_corp_pic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_agenda_speaker` ADD CONSTRAINT `tbl_agenda_speaker_partner_prog_id_foreign` FOREIGN KEY (`partner_prog_id`) REFERENCES `tbl_partner_prog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_agenda_speaker` ADD CONSTRAINT `tbl_agenda_speaker_sch_pic_id_foreign` FOREIGN KEY (`sch_pic_id`) REFERENCES `tbl_schdetail` (`schdetail_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_agenda_speaker` ADD CONSTRAINT `tbl_agenda_speaker_sch_prog_id_foreign` FOREIGN KEY (`sch_prog_id`) REFERENCES `tbl_sch_prog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_agenda_speaker` ADD CONSTRAINT `tbl_agenda_speaker_univ_pic_id_foreign` FOREIGN KEY (`univ_pic_id`) REFERENCES `tbl_univ_pic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_asset_returned` ADD CONSTRAINT `tbl_asset_returned_asset_used_id_foreign` FOREIGN KEY (`asset_used_id`) REFERENCES `tbl_asset_used` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_asset_used` ADD CONSTRAINT `tbl_asset_used_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `tbl_asset` (`asset_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_asset_used` ADD CONSTRAINT `tbl_asset_used_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_bundling_dtl` ADD CONSTRAINT `tbl_bundling_dtl_bundling_id_foreign` FOREIGN KEY (`bundling_id`) REFERENCES `tbl_bundling` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_bundling_dtl` ADD CONSTRAINT `tbl_bundling_dtl_clientprog_id_foreign` FOREIGN KEY (`clientprog_id`) REFERENCES `tbl_client_prog` (`clientprog_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_client` ADD CONSTRAINT `tbl_client_eduf_id_foreign` FOREIGN KEY (`eduf_id`) REFERENCES `tbl_eduf_lead` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_client` ADD CONSTRAINT `tbl_client_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `tbl_events` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_client` ADD CONSTRAINT `tbl_client_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `tbl_lead` (`lead_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_client` ADD CONSTRAINT `tbl_client_sch_id_foreign` FOREIGN KEY (`sch_id`) REFERENCES `tbl_sch` (`sch_id`) ON UPDATE CASCADE;

ALTER TABLE `tbl_client_abrcountry` ADD CONSTRAINT `tbl_client_abrcountry_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `tbl_client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_client_abrcountry` ADD CONSTRAINT `tbl_client_abrcountry_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tbl_tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_client_acceptance` ADD CONSTRAINT `tbl_client_acceptance_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `tbl_client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_client_acceptance` ADD CONSTRAINT `tbl_client_acceptance_major_id_foreign` FOREIGN KEY (`major_id`) REFERENCES `tbl_major` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_client_acceptance` ADD CONSTRAINT `tbl_client_acceptance_univ_id_foreign` FOREIGN KEY (`univ_id`) REFERENCES `tbl_univ` (`univ_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_client_additional_info` ADD CONSTRAINT `tbl_client_additional_info_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `tbl_client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_client_event` ADD CONSTRAINT `tbl_client_event_child_id_foreign` FOREIGN KEY (`child_id`) REFERENCES `tbl_client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_client_event` ADD CONSTRAINT `tbl_client_event_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `tbl_client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_client_event` ADD CONSTRAINT `tbl_client_event_eduf_id_foreign` FOREIGN KEY (`eduf_id`) REFERENCES `tbl_eduf_lead` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_client_event` ADD CONSTRAINT `tbl_client_event_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `tbl_events` (`event_id`) ON UPDATE CASCADE;

ALTER TABLE `tbl_client_event` ADD CONSTRAINT `tbl_client_event_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `tbl_lead` (`lead_id`) ON UPDATE CASCADE;

ALTER TABLE `tbl_client_event` ADD CONSTRAINT `tbl_client_event_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `tbl_client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_client_event` ADD CONSTRAINT `tbl_client_event_partner_id_foreign` FOREIGN KEY (`partner_id`) REFERENCES `tbl_corp` (`corp_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_client_event_log_mail` ADD CONSTRAINT `tbl_client_event_log_mail_child_id_foreign` FOREIGN KEY (`child_id`) REFERENCES `tbl_client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_client_event_log_mail` ADD CONSTRAINT `tbl_client_event_log_mail_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `tbl_client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_client_event_log_mail` ADD CONSTRAINT `tbl_client_event_log_mail_clientevent_id_foreign` FOREIGN KEY (`clientevent_id`) REFERENCES `tbl_client_event` (`clientevent_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_client_event_log_mail` ADD CONSTRAINT `tbl_client_event_log_mail_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `tbl_events` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_client_lead_tracking` ADD CONSTRAINT `tbl_client_lead_tracking_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `tbl_client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_client_lead_tracking` ADD CONSTRAINT `tbl_client_lead_tracking_initialprogram_id_foreign` FOREIGN KEY (`initialprogram_id`) REFERENCES `tbl_initial_program_lead` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_client_lead_tracking` ADD CONSTRAINT `tbl_client_lead_tracking_reason_id_foreign` FOREIGN KEY (`reason_id`) REFERENCES `tbl_reason` (`reason_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_client_mentor` ADD CONSTRAINT `tbl_client_mentor_clientprog_id_foreign` FOREIGN KEY (`clientprog_id`) REFERENCES `tbl_client_prog` (`clientprog_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_client_mentor` ADD CONSTRAINT `tbl_client_mentor_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

ALTER TABLE `tbl_client_prog` ADD CONSTRAINT `tbl_client_prog_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `tbl_client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_client_prog` ADD CONSTRAINT `tbl_client_prog_clientevent_id_foreign` FOREIGN KEY (`clientevent_id`) REFERENCES `tbl_client_event` (`clientevent_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_client_prog` ADD CONSTRAINT `tbl_client_prog_eduf_lead_id_foreign` FOREIGN KEY (`eduf_lead_id`) REFERENCES `tbl_eduf_lead` (`id`) ON UPDATE CASCADE;

ALTER TABLE `tbl_client_prog` ADD CONSTRAINT `tbl_client_prog_empl_id_foreign` FOREIGN KEY (`empl_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

ALTER TABLE `tbl_client_prog` ADD CONSTRAINT `tbl_client_prog_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `tbl_lead` (`lead_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_client_prog` ADD CONSTRAINT `tbl_client_prog_partner_id_foreign` FOREIGN KEY (`partner_id`) REFERENCES `tbl_corp` (`corp_id`) ON UPDATE CASCADE;

ALTER TABLE `tbl_client_prog` ADD CONSTRAINT `tbl_client_prog_prog_id_foreign` FOREIGN KEY (`prog_id`) REFERENCES `tbl_prog` (`prog_id`) ON UPDATE CASCADE;

ALTER TABLE `tbl_client_prog` ADD CONSTRAINT `tbl_client_prog_reason_id_foreign` FOREIGN KEY (`reason_id`) REFERENCES `tbl_reason` (`reason_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_client_prog_log_mail` ADD CONSTRAINT `tbl_client_prog_log_mail_clientprog_id_foreign` FOREIGN KEY (`clientprog_id`) REFERENCES `tbl_client_prog` (`clientprog_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_client_relation` ADD CONSTRAINT `tbl_client_relation_child_id_foreign` FOREIGN KEY (`child_id`) REFERENCES `tbl_client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_client_relation` ADD CONSTRAINT `tbl_client_relation_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `tbl_client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_client_roles` ADD CONSTRAINT `tbl_client_roles_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `tbl_client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_client_roles` ADD CONSTRAINT `tbl_client_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `tbl_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_corp_partner_event` ADD CONSTRAINT `tbl_corp_partner_event_corp_id_foreign` FOREIGN KEY (`corp_id`) REFERENCES `tbl_corp` (`corp_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_corp_partner_event` ADD CONSTRAINT `tbl_corp_partner_event_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `tbl_events` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_corp_pic` ADD CONSTRAINT `tbl_corp_pic_corp_id_foreign` FOREIGN KEY (`corp_id`) REFERENCES `tbl_corp` (`corp_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_dreams_major` ADD CONSTRAINT `tbl_dreams_major_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `tbl_client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_dreams_major` ADD CONSTRAINT `tbl_dreams_major_major_id_foreign` FOREIGN KEY (`major_id`) REFERENCES `tbl_major` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_dreams_uni` ADD CONSTRAINT `tbl_dreams_uni_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `tbl_client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_dreams_uni` ADD CONSTRAINT `tbl_dreams_uni_univ_id_foreign` FOREIGN KEY (`univ_id`) REFERENCES `tbl_univ` (`univ_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_eduf_lead` ADD CONSTRAINT `tbl_eduf_lead_corp_id_foreign` FOREIGN KEY (`corp_id`) REFERENCES `tbl_corp` (`corp_id`) ON UPDATE CASCADE;

ALTER TABLE `tbl_eduf_lead` ADD CONSTRAINT `tbl_eduf_lead_intr_pic_foreign` FOREIGN KEY (`intr_pic`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_eduf_lead` ADD CONSTRAINT `tbl_eduf_lead_sch_id_foreign` FOREIGN KEY (`sch_id`) REFERENCES `tbl_sch` (`sch_id`) ON UPDATE CASCADE;

ALTER TABLE `tbl_eduf_review` ADD CONSTRAINT `tbl_eduf_review_eduf_id_foreign` FOREIGN KEY (`eduf_id`) REFERENCES `tbl_eduf_lead` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_eduf_review` ADD CONSTRAINT `tbl_eduf_review_reviewer_name_foreign` FOREIGN KEY (`reviewer_name`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_events` ADD CONSTRAINT `tbl_events_category_foreign` FOREIGN KEY (`category`) REFERENCES `tbl_prog` (`prog_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_event_pic` ADD CONSTRAINT `tbl_event_pic_empl_id_foreign` FOREIGN KEY (`empl_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_event_pic` ADD CONSTRAINT `tbl_event_pic_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `tbl_events` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_event_speaker` ADD CONSTRAINT `tbl_event_speaker_corp_pic_id_foreign` FOREIGN KEY (`corp_pic_id`) REFERENCES `tbl_corp_pic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_event_speaker` ADD CONSTRAINT `tbl_event_speaker_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `tbl_events` (`event_id`) ON UPDATE CASCADE;

ALTER TABLE `tbl_event_speaker` ADD CONSTRAINT `tbl_event_speaker_sch_pic_id_foreign` FOREIGN KEY (`sch_pic_id`) REFERENCES `tbl_schdetail` (`schdetail_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_event_speaker` ADD CONSTRAINT `tbl_event_speaker_univ_pic_id_foreign` FOREIGN KEY (`univ_pic_id`) REFERENCES `tbl_univ_pic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_followup` ADD CONSTRAINT `tbl_followup_clientprog_id_foreign` FOREIGN KEY (`clientprog_id`) REFERENCES `tbl_client_prog` (`clientprog_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_followup_client` ADD CONSTRAINT `tbl_followup_client_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `tbl_client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_followup_client` ADD CONSTRAINT `tbl_followup_client_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_initial_prog_sub_lead` ADD CONSTRAINT `tbl_initial_prog_sub_lead_initialprogram_id_foreign` FOREIGN KEY (`initialprogram_id`) REFERENCES `tbl_initial_program_lead` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_initial_prog_sub_lead` ADD CONSTRAINT `tbl_initial_prog_sub_lead_subprogram_id_foreign` FOREIGN KEY (`subprogram_id`) REFERENCES `tbl_sub_prog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_interest_prog` ADD CONSTRAINT `tbl_interest_prog_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `tbl_client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_interest_prog` ADD CONSTRAINT `tbl_interest_prog_prog_id_foreign` FOREIGN KEY (`prog_id`) REFERENCES `tbl_prog` (`prog_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_inv` ADD CONSTRAINT `tbl_inv_bundling_id_foreign` FOREIGN KEY (`bundling_id`) REFERENCES `tbl_bundling` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_inv` ADD CONSTRAINT `tbl_inv_clientprog_id_foreign` FOREIGN KEY (`clientprog_id`) REFERENCES `tbl_client_prog` (`clientprog_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_inv` ADD CONSTRAINT `tbl_inv_ref_id_foreign` FOREIGN KEY (`ref_id`) REFERENCES `tbl_referral` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_invb2b` ADD CONSTRAINT `tbl_invb2b_partnerprog_id_foreign` FOREIGN KEY (`partnerprog_id`) REFERENCES `tbl_partner_prog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_invb2b` ADD CONSTRAINT `tbl_invb2b_ref_id_foreign` FOREIGN KEY (`ref_id`) REFERENCES `tbl_referral` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_invb2b` ADD CONSTRAINT `tbl_invb2b_schprog_id_foreign` FOREIGN KEY (`schprog_id`) REFERENCES `tbl_sch_prog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_invdtl` ADD CONSTRAINT `tbl_invdtl_inv_id_foreign` FOREIGN KEY (`inv_id`) REFERENCES `tbl_inv` (`inv_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_invdtl` ADD CONSTRAINT `tbl_invdtl_invb2b_id_foreign` FOREIGN KEY (`invb2b_id`) REFERENCES `tbl_invb2b` (`invb2b_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_inv_attachment` ADD CONSTRAINT `tbl_inv_attachment_inv_id_foreign` FOREIGN KEY (`inv_id`) REFERENCES `tbl_inv` (`inv_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_inv_attachment` ADD CONSTRAINT `tbl_inv_attachment_invb2b_id_foreign` FOREIGN KEY (`invb2b_id`) REFERENCES `tbl_invb2b` (`invb2b_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_lead` ADD CONSTRAINT `tbl_lead_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `tbl_department` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_lead_bucket_params` ADD CONSTRAINT `tbl_lead_bucket_params_initialprogram_id_foreign` FOREIGN KEY (`initialprogram_id`) REFERENCES `tbl_initial_program_lead` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_lead_bucket_params` ADD CONSTRAINT `tbl_lead_bucket_params_param_id_foreign` FOREIGN KEY (`param_id`) REFERENCES `tbl_param_lead` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_mentor_ic` ADD CONSTRAINT `tbl_mentor_ic_clientprog_id_foreign` FOREIGN KEY (`clientprog_id`) REFERENCES `tbl_client_prog` (`clientprog_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_mentor_ic` ADD CONSTRAINT `tbl_mentor_ic_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

ALTER TABLE `tbl_menus` ADD CONSTRAINT `tbl_menus_mainmenu_id_foreign` FOREIGN KEY (`mainmenu_id`) REFERENCES `tbl_main_menus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_menusdtl` ADD CONSTRAINT `tbl_menusdtl_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `tbl_department` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_menusdtl` ADD CONSTRAINT `tbl_menusdtl_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `tbl_menus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_menus_user` ADD CONSTRAINT `tbl_menus_user_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `tbl_menus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_menus_user` ADD CONSTRAINT `tbl_menus_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_partner_agreement` ADD CONSTRAINT `tbl_partner_agreement_corp_id_foreign` FOREIGN KEY (`corp_id`) REFERENCES `tbl_corp` (`corp_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_partner_agreement` ADD CONSTRAINT `tbl_partner_agreement_corp_pic_foreign` FOREIGN KEY (`corp_pic`) REFERENCES `tbl_corp_pic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_partner_agreement` ADD CONSTRAINT `tbl_partner_agreement_empl_id_foreign` FOREIGN KEY (`empl_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_partner_prog` ADD CONSTRAINT `tbl_partner_prog_corp_id_foreign` FOREIGN KEY (`corp_id`) REFERENCES `tbl_corp` (`corp_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_partner_prog` ADD CONSTRAINT `tbl_partner_prog_empl_id_foreign` FOREIGN KEY (`empl_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_partner_prog` ADD CONSTRAINT `tbl_partner_prog_prog_id_foreign` FOREIGN KEY (`prog_id`) REFERENCES `tbl_prog` (`prog_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_partner_prog` ADD CONSTRAINT `tbl_partner_prog_reason_id_foreign` FOREIGN KEY (`reason_id`) REFERENCES `tbl_reason` (`reason_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_partner_prog_attachment` ADD CONSTRAINT `tbl_partner_prog_attachment_partner_prog_id_foreign` FOREIGN KEY (`partner_prog_id`) REFERENCES `tbl_partner_prog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_partner_prog_partner` ADD CONSTRAINT `tbl_partner_prog_partner_corp_id_foreign` FOREIGN KEY (`corp_id`) REFERENCES `tbl_corp` (`corp_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_partner_prog_partner` ADD CONSTRAINT `tbl_partner_prog_partner_partnerprog_id_foreign` FOREIGN KEY (`partnerprog_id`) REFERENCES `tbl_partner_prog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_partner_prog_sch` ADD CONSTRAINT `tbl_partner_prog_sch_partnerprog_id_foreign` FOREIGN KEY (`partnerprog_id`) REFERENCES `tbl_partner_prog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_partner_prog_sch` ADD CONSTRAINT `tbl_partner_prog_sch_sch_id_foreign` FOREIGN KEY (`sch_id`) REFERENCES `tbl_sch` (`sch_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_partner_prog_univ` ADD CONSTRAINT `tbl_partner_prog_univ_partnerprog_id_foreign` FOREIGN KEY (`partnerprog_id`) REFERENCES `tbl_partner_prog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_partner_prog_univ` ADD CONSTRAINT `tbl_partner_prog_univ_univ_id_foreign` FOREIGN KEY (`univ_id`) REFERENCES `tbl_univ` (`univ_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_pic_client` ADD CONSTRAINT `tbl_pic_client_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `tbl_client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_pic_client` ADD CONSTRAINT `tbl_pic_client_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_prog` ADD CONSTRAINT `tbl_prog_main_prog_id_foreign` FOREIGN KEY (`main_prog_id`) REFERENCES `tbl_main_prog` (`id`);

ALTER TABLE `tbl_prog` ADD CONSTRAINT `tbl_prog_sub_prog_id_foreign` FOREIGN KEY (`sub_prog_id`) REFERENCES `tbl_sub_prog` (`id`);

ALTER TABLE `tbl_program_buckets_params` ADD CONSTRAINT `tbl_program_buckets_params_initialprogram_id_foreign` FOREIGN KEY (`initialprogram_id`) REFERENCES `tbl_initial_program_lead` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_program_buckets_params` ADD CONSTRAINT `tbl_program_buckets_params_param_id_foreign` FOREIGN KEY (`param_id`) REFERENCES `tbl_param_lead` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_program_lead_library` ADD CONSTRAINT `tbl_program_lead_library_leadbucket_id_foreign` FOREIGN KEY (`leadbucket_id`) REFERENCES `tbl_lead_bucket_params` (`bucket_id`) ON UPDATE CASCADE;

ALTER TABLE `tbl_program_lead_library` ADD CONSTRAINT `tbl_program_lead_library_programbucket_id_foreign` FOREIGN KEY (`programbucket_id`) REFERENCES `tbl_program_buckets_params` (`bucket_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_purchase_dtl` ADD CONSTRAINT `tbl_purchase_dtl_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `tbl_purchase_request` (`purchase_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_purchase_request` ADD CONSTRAINT `tbl_purchase_request_purchase_department_foreign` FOREIGN KEY (`purchase_department`) REFERENCES `tbl_department` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_purchase_request` ADD CONSTRAINT `tbl_purchase_request_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_receipt` ADD CONSTRAINT `tbl_receipt_inv_id_foreign` FOREIGN KEY (`inv_id`) REFERENCES `tbl_inv` (`inv_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_receipt` ADD CONSTRAINT `tbl_receipt_invb2b_id_foreign` FOREIGN KEY (`invb2b_id`) REFERENCES `tbl_invb2b` (`invb2b_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_receipt` ADD CONSTRAINT `tbl_receipt_invdtl_id_foreign` FOREIGN KEY (`invdtl_id`) REFERENCES `tbl_invdtl` (`invdtl_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_receipt_attachment` ADD CONSTRAINT `tbl_receipt_attachment_receipt_id_foreign` FOREIGN KEY (`receipt_id`) REFERENCES `tbl_receipt` (`receipt_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_referral` ADD CONSTRAINT `tbl_referral_empl_id_foreign` FOREIGN KEY (`empl_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_referral` ADD CONSTRAINT `tbl_referral_partner_id_foreign` FOREIGN KEY (`partner_id`) REFERENCES `tbl_corp` (`corp_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_referral` ADD CONSTRAINT `tbl_referral_prog_id_foreign` FOREIGN KEY (`prog_id`) REFERENCES `tbl_prog` (`prog_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_refund` ADD CONSTRAINT `tbl_refund_inv_id_foreign` FOREIGN KEY (`inv_id`) REFERENCES `tbl_inv` (`inv_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_refund` ADD CONSTRAINT `tbl_refund_invb2b_id_foreign` FOREIGN KEY (`invb2b_id`) REFERENCES `tbl_invb2b` (`invb2b_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_sales_target` ADD CONSTRAINT `tbl_sales_target_main_prog_id_foreign` FOREIGN KEY (`main_prog_id`) REFERENCES `tbl_main_prog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_sales_target` ADD CONSTRAINT `tbl_sales_target_prog_id_foreign` FOREIGN KEY (`prog_id`) REFERENCES `tbl_prog` (`prog_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_schdetail` ADD CONSTRAINT `tbl_schdetail_ibfk_1` FOREIGN KEY (`sch_id`) REFERENCES `tbl_sch` (`sch_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_sch_aliases` ADD CONSTRAINT `tbl_sch_aliases_sch_id_foreign` FOREIGN KEY (`sch_id`) REFERENCES `tbl_sch` (`sch_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_sch_curriculum` ADD CONSTRAINT `tbl_sch_curriculum_curriculum_id_foreign` FOREIGN KEY (`curriculum_id`) REFERENCES `tbl_curriculum` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_sch_curriculum` ADD CONSTRAINT `tbl_sch_curriculum_sch_id_foreign` FOREIGN KEY (`sch_id`) REFERENCES `tbl_sch` (`sch_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_sch_event` ADD CONSTRAINT `tbl_sch_event_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `tbl_events` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_sch_event` ADD CONSTRAINT `tbl_sch_event_sch_id_foreign` FOREIGN KEY (`sch_id`) REFERENCES `tbl_sch` (`sch_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_sch_prog` ADD CONSTRAINT `tbl_sch_prog_empl_id_foreign` FOREIGN KEY (`empl_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_sch_prog` ADD CONSTRAINT `tbl_sch_prog_prog_id_foreign` FOREIGN KEY (`prog_id`) REFERENCES `tbl_prog` (`prog_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_sch_prog` ADD CONSTRAINT `tbl_sch_prog_reason_id_foreign` FOREIGN KEY (`reason_id`) REFERENCES `tbl_reason` (`reason_id`);

ALTER TABLE `tbl_sch_prog` ADD CONSTRAINT `tbl_sch_prog_sch_id_foreign` FOREIGN KEY (`sch_id`) REFERENCES `tbl_sch` (`sch_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_sch_prog_attach` ADD CONSTRAINT `tbl_sch_prog_attach_schprog_id_foreign` FOREIGN KEY (`schprog_id`) REFERENCES `tbl_sch_prog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_sch_prog_partner` ADD CONSTRAINT `tbl_sch_prog_partner_corp_id_foreign` FOREIGN KEY (`corp_id`) REFERENCES `tbl_corp` (`corp_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_sch_prog_partner` ADD CONSTRAINT `tbl_sch_prog_partner_schprog_id_foreign` FOREIGN KEY (`schprog_id`) REFERENCES `tbl_sch_prog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_sch_prog_school` ADD CONSTRAINT `tbl_sch_prog_school_sch_id_foreign` FOREIGN KEY (`sch_id`) REFERENCES `tbl_sch` (`sch_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_sch_prog_school` ADD CONSTRAINT `tbl_sch_prog_school_schprog_id_foreign` FOREIGN KEY (`schprog_id`) REFERENCES `tbl_sch_prog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_sch_prog_univ` ADD CONSTRAINT `tbl_sch_prog_univ_schprog_id_foreign` FOREIGN KEY (`schprog_id`) REFERENCES `tbl_sch_prog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_sch_prog_univ` ADD CONSTRAINT `tbl_sch_prog_univ_univ_id_foreign` FOREIGN KEY (`univ_id`) REFERENCES `tbl_univ` (`univ_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_sch_visit` ADD CONSTRAINT `tbl_sch_visit_internal_pic_foreign` FOREIGN KEY (`internal_pic`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

ALTER TABLE `tbl_sch_visit` ADD CONSTRAINT `tbl_sch_visit_sch_id_foreign` FOREIGN KEY (`sch_id`) REFERENCES `tbl_sch` (`sch_id`) ON UPDATE CASCADE;

ALTER TABLE `tbl_sch_visit` ADD CONSTRAINT `tbl_sch_visit_school_pic_foreign` FOREIGN KEY (`school_pic`) REFERENCES `tbl_schdetail` (`schdetail_id`) ON UPDATE CASCADE;

ALTER TABLE `tbl_seasonal_lead` ADD CONSTRAINT `tbl_seasonal_lead_prog_id_foreign` FOREIGN KEY (`prog_id`) REFERENCES `tbl_prog` (`prog_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_sub_prog` ADD CONSTRAINT `tbl_sub_prog_main_prog_id_foreign` FOREIGN KEY (`main_prog_id`) REFERENCES `tbl_main_prog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_univ` ADD CONSTRAINT `tbl_univ_tag_foreign` FOREIGN KEY (`univ_country`) REFERENCES `tbl_tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_univ_event` ADD CONSTRAINT `tbl_univ_event_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `tbl_events` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_univ_event` ADD CONSTRAINT `tbl_univ_event_univ_id_foreign` FOREIGN KEY (`univ_id`) REFERENCES `tbl_univ` (`univ_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_univ_pic` ADD CONSTRAINT `tbl_univ_pic_univ_id_foreign` FOREIGN KEY (`univ_id`) REFERENCES `tbl_univ` (`univ_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_user_educations` ADD CONSTRAINT `tbl_user_educations_major_id_foreign` FOREIGN KEY (`major_id`) REFERENCES `tbl_major` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_user_educations` ADD CONSTRAINT `tbl_user_educations_univ_id_foreign` FOREIGN KEY (`univ_id`) REFERENCES `tbl_univ` (`univ_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_user_educations` ADD CONSTRAINT `tbl_user_educations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_user_roles` ADD CONSTRAINT `tbl_user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `tbl_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_user_roles` ADD CONSTRAINT `tbl_user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_user_subjects` ADD CONSTRAINT `tbl_user_subjects_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `tbl_subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_user_subjects` ADD CONSTRAINT `tbl_user_subjects_user_role_id_foreign` FOREIGN KEY (`user_role_id`) REFERENCES `tbl_user_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_user_type_detail` ADD CONSTRAINT `tbl_user_type_detail_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `tbl_department` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_user_type_detail` ADD CONSTRAINT `tbl_user_type_detail_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_user_type_detail` ADD CONSTRAINT `tbl_user_type_detail_user_type_id_foreign` FOREIGN KEY (`user_type_id`) REFERENCES `tbl_user_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_volunt` ADD CONSTRAINT `tbl_volunt_major_id_foreign` FOREIGN KEY (`major_id`) REFERENCES `tbl_major` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_volunt` ADD CONSTRAINT `tbl_volunt_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `tbl_position` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_volunt` ADD CONSTRAINT `tbl_volunt_univ_id_foreign` FOREIGN KEY (`univ_id`) REFERENCES `tbl_univ` (`univ_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `users` ADD CONSTRAINT `users_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `tbl_position` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
