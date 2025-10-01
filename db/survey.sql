

QUESTIONS: 

1-- Can the survey have flexibility for ex for different type of availability rules, show differents questions or response choices 
2-- Is availability controlled by admins or is it setup by the survey creator
3-- What are the roles for survey access and what can they do ? 
4-- Is the survey editable ? (From the user side and survey owner side)
5-- How about branching, a question that leads to another question ?
6-- Can the survey be preview?

-- Main Surveys table
CREATE TABLE IF NOT EXISTS `Surveys` (
  survey_id         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  title             VARCHAR(255) NOT NULL,
  description       TEXT NULL,
  survey_type       ENUM('anonymous', 'identified') NOT NULL DEFAULT 'anonymous',
  created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  start_date        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  end_date          DATETIME NULL DEFAULT NULL,
  max_responses     INT UNSIGNED NULL DEFAULT NULL,
  question          VARCHAR(255) 
  question_type    ENUM('text', 'textarea', 'single_choice', 'multiple_choice', 'rating', 'boolean', 'date', 'number','blob') NOT NULL, 
  is_manually_disabled BOOLEAN NOT NULL DEFAULT FALSE,
  
  -- Computed column for status based on dates and manual control , is manual control available for survey creator , admin 
  status AS (
    CASE 
      WHEN is_manually_disabled = TRUE THEN 'inactive'
      WHEN NOW() < start_date THEN 'inactive'
      WHEN NOW() => start_date THEN 'active'
      WHEN end_date IS NOT NULL AND NOW() > end_date THEN 'inactive'
      ELSE 'active'
    END
  ) STORED,
  
  INDEX idx_survey_dates (start_date, end_date),
  INDEX idx_survey_status (is_manually_disabled, start_date, end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--- for assigning roles to users for a survey
CREATE TABLE IF NOT EXISTS SurveyCollaborators (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  survey_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  role ENUM('owner', 'editor', 'viewer') NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (survey_id) REFERENCES Surveys(survey_id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
  UNIQUE KEY (survey_id, user_id)
);

-- Use JSON to define flexible availability rules 

-- Have an ui that generate dropdown and operator AND OR , the field for the dropowm comes from SurveyAvailabilityRules rule_type  (enum defined in SurveyAvailabilityRules table)


CREATE TABLE IF NOT EXISTS `SurveyAvailabilityRules` (
  id                INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  survey_id         INT UNSIGNED NOT NULL,
  rule_type         ENUM('native_language', 'native_variant', 'source_language', 'custom') NOT NULL,
  rules             JSON NOT NULL, -- All rule logic in one flexible JSON structure 
  is_enabled        BOOLEAN NOT NULL DEFAULT TRUE,
  created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (survey_id) REFERENCES Surveys(survey_id) ON DELETE CASCADE,
  INDEX idx_survey_rules (survey_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Survey questions
CREATE TABLE IF NOT EXISTS `SurveyQuestions` (
  question_id       INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  survey_id         INT UNSIGNED NOT NULL,
  question_text     TEXT NOT NULL,
  question_type     ENUM('text', 'textarea', 'single_choice', 'multiple_choice', 'rating', 'boolean', 'date', 'number') NOT NULL,
  is_required       BOOLEAN NOT NULL DEFAULT FALSE,
  order_index       INT NOT NULL DEFAULT 0,
  validation_rules  JSON NULL DEFAULT NULL, -- For custom validation
  created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (survey_id) REFERENCES Surveys(survey_id) ON DELETE CASCADE,
  INDEX idx_question_survey (survey_id),
  INDEX idx_question_order (survey_id, order_index)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Question options for choice-based questions
CREATE TABLE IF NOT EXISTS `SurveyQuestionOptions` (
  option_id         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  question_id       INT UNSIGNED NOT NULL,
  option_text       VARCHAR(255) NOT NULL,
  option_value      VARCHAR(100) NULL DEFAULT NULL, -- For programmatic values
  order_index       INT NOT NULL DEFAULT 0,
  is_enabled        BOOLEAN NOT NULL DEFAULT TRUE,
  created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (question_id) REFERENCES SurveyQuestions(question_id) ON DELETE CASCADE,
  INDEX idx_option_question (question_id),
  INDEX idx_option_order (question_id, order_index)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Survey response submissions / from the survey whe can get the questions
CREATE TABLE IF NOT EXISTS `SurveyResponses` (
  response_id       INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  survey_id         INT UNSIGNED NOT NULL,
  survey_option_id   INT UNSIGNED NOT  NULL 
  user_id           INT UNSIGNED NULL DEFAULT NULL, -- NULL for anonymous surveys
  singleResponseID  INT UNSIGNED NOT  NULL
  mutipleResponseID JSON NULL DEFAULT NULL
  response_text  TEXT NULL DEFAULT NULL,          
  submitted_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 
  
  FOREIGN KEY (survey_id) REFERENCES Surveys(survey_id) ON DELETE CASCADE,
  FOREIGN KEY (option_id) REFERENCES SurveyOptions(survey_option_id) ON DELETE CASCADE,

  FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE SET NULL,
  
  INDEX idx_response_survey (survey_id),
    INDEX idx_response_options (survey_option_id),
  INDEX idx_response_user (user_id),
  INDEX idx_response_submitted (submitted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Individual question responses
CREATE TABLE IF NOT EXISTS `QuestionResponses` (
  id                INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  response_id       INT UNSIGNED NOT NULL,
  question_id       INT UNSIGNED NOT NULL,
  response_text     TEXT NULL DEFAULT NULL,           -- For text responses
  response_number   DECIMAL(10,2) NULL DEFAULT NULL,  -- For numeric responses
  response_date     DATE NULL DEFAULT NULL,           -- For date responses
  response_boolean  BOOLEAN NULL DEFAULT NULL,        -- For boolean responses
  created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (response_id) REFERENCES SurveyResponses(response_id) ON DELETE CASCADE,
  FOREIGN KEY (question_id) REFERENCES SurveyQuestions(question_id) ON DELETE CASCADE,
  
  UNIQUE KEY unique_response_question (response_id, question_id),
  INDEX idx_question_responses (question_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Selected options for choice-based questions
CREATE TABLE IF NOT EXISTS `SelectedOptions` (
  id                INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  question_response_id INT UNSIGNED NOT NULL,
  option_id         INT UNSIGNED NOT NULL,
  custom_text       VARCHAR(255) NULL DEFAULT NULL, -- For "Other" options with custom text
  
  FOREIGN KEY (question_response_id) REFERENCES QuestionResponses(id) ON DELETE CASCADE,
  FOREIGN KEY (option_id) REFERENCES SurveyQuestionOptions(option_id) ON DELETE CASCADE,
  
  UNIQUE KEY unique_response_option (question_response_id, option_id),
  INDEX idx_selected_options (option_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; WHEN NOW() < start_date THEN 'inactive


###################### REFACTOR WITH MARIAM 
1. Find inconsistencies
2. Understand JSON  usage 
3. Why do we need to normalize tables



-- Main Surveys tables
CREATE TABLE IF NOT EXISTS `Surveys` (
 survey_id         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 title             VARCHAR(255) NOT NULL,
 description       TEXT NULL,
 survey_type       ENUM('anonymous', 'identified') NOT NULL DEFAULT 'anonymous',
 created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 start_date        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 end_date          DATETIME NULL DEFAULT NULL,
 max_responses     INT UNSIGNED NULL DEFAULT NULL,
 question_text     TEXT NOT NULL
 question_type    ENUM('text', 'textarea', 'single_choice', 'multiple_choice', 'rating', 'boolean', 'date', 'number','blob') NOT NULL,
is_required       BOOLEAN NOT NULL DEFAULT FALSE,
is_manually_disabled BOOLEAN NOT NULL DEFAULT FALSE,
  -- Computed column for status based on dates and manual control , is manual control available for survey creator , admin
 status AS (
   CASE
     WHEN is_manually_disabled = TRUE THEN 'inactive'
     WHEN NOW() < start_date THEN 'inactive'
     WHEN NOW() => start_date THEN 'active'
     WHEN end_date IS NOT NULL AND NOW() > end_date THEN 'inactive'
     ELSE 'active'
   END
) STORED,
 
 INDEX idx_survey_dates (start_date, end_date),
 INDEX idx_survey_status (is_manually_disabled, start_date, end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `SurveyAvailabilityRules` (
 id                INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 survey_id         INT UNSIGNED NOT NULL,
 rule_type         ENUM('native_language', 'native_variant', 'source_language', 'custom') NOT NULL,
 rules             JSON NOT NULL, -- All rule logic in one flexible JSON structure
 is_enabled        BOOLEAN NOT NULL DEFAULT TRUE,
 created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (survey_id) REFERENCES Surveys(survey_id) ON DELETE CASCADE,
 INDEX idx_survey_rules (survey_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




-- Question options for choice-based questions
CREATE TABLE IF NOT EXISTS `SurveyOptions` (
 option_id         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 survey_id         INT UNSIGNED NOT NULL,
 option_text       VARCHAR(255) NOT NULL,
 option_value      VARCHAR(100) NULL DEFAULT NULL, -- For programmatic values
 Attachment       Blob
 order_index       INT NOT NULL DEFAULT 0,
 is_enabled        BOOLEAN NOT NULL DEFAULT TRUE,
 created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (survey_id) REFERENCES Surveys(survey_id) ON DELETE CASCADE,


) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Survey response submissions / from the survey( 1 question survey)
– Table with users who have responded without the responses 
– attachment needed 
-- Individual question responses
CREATE TABLE IF NOT EXISTS `SurveyResponses` (
 response_id       INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 survey_id         INT UNSIGNED NOT NULL,
 survey_option_id   INT UNSIGNED NOT  NULL
 user_id           INT UNSIGNED NULL DEFAULT NULL, -- NULL for anonymous surveys
 singleResponseID  INT UNSIGNED NOT  NULL
 multipleResponseID JSON NULL DEFAULT NULL
 response_text  TEXT NULL DEFAULT NULL,  
 Attachment BLOB,       
 submitted_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (survey_id) REFERENCES Surveys(survey_id) ON DELETE CASCADE,
 FOREIGN KEY (option_id) REFERENCES SurveyOptions(survey_option_id) ON DELETE CASCADE,


 FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE SET NULL,
  INDEX idx_response_survey (survey_id),
   INDEX idx_response_options (survey_option_id),
 INDEX idx_response_user (user_id),
 INDEX idx_response_submitted (submitted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




corrected 
-- =========================
-- Main Surveys table
-- =========================
CREATE TABLE IF NOT EXISTS `Surveys` (
  survey_id              INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  title                  VARCHAR(255) NOT NULL,
  description            TEXT NULL,
  survey_type            ENUM('anonymous', 'identified') NOT NULL DEFAULT 'anonymous',
  created_at             DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at             DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  start_date             DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  end_date               DATETIME NULL DEFAULT NULL,
  max_responses          INT UNSIGNED NULL DEFAULT NULL,

  -- inline single question (by design: 1 survey = 1 question)
  question_text          TEXT NOT NULL,
  question_type          ENUM('text', 'textarea', 'single_choice', 'multiple_choice', 'rating', 'boolean', 'date', 'number','blob') NOT NULL,
  is_required            BOOLEAN NOT NULL DEFAULT FALSE,

  -- manual kill switch
  is_manually_disabled   BOOLEAN NOT NULL DEFAULT FALSE,

   -- survey status (will be managed by triggers)
  status                 ENUM('open', 'closed') NOT NULL DEFAULT 'open',

  -- helpful indexes
  INDEX idx_survey_dates (start_date, end_date),
  INDEX idx_survey_status (is_manually_disabled, start_date, end_date),

  -- basic consistency: end_date (if present) must be after start_date
  CHECK (end_date IS NULL OR end_date > start_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =========================
-- Availability Rules (JSON)
-- =========================
CREATE TABLE IF NOT EXISTS `SurveyAvailabilityRules` (
  id               INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  survey_id        INT UNSIGNED NOT NULL,
  rule_type        ENUM('native_language', 'native_variant', 'source_language', 'custom') NOT NULL,
  rules            JSON NOT NULL,  -- flexible rule logic
  is_enabled       BOOLEAN NOT NULL DEFAULT TRUE,
  created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (survey_id) REFERENCES Surveys(survey_id) ON DELETE CASCADE,
  INDEX idx_survey_rules (survey_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ==========================================
-- Options for choice-based questions
-- (supports blobs like image/audio/video)
-- ==========================================
CREATE TABLE IF NOT EXISTS `SurveyOptions` (
  option_id        INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  survey_id        INT UNSIGNED NOT NULL,
  option_text      VARCHAR(255) NOT NULL,
  option_value     VARCHAR(100) NULL DEFAULT NULL,   -- programmatic value / code
  Attachment       BLOB NULL,                        -- binary payload (image/video/audio)
  order_index      INT NOT NULL DEFAULT 0,
  is_enabled       BOOLEAN NOT NULL DEFAULT TRUE,
  created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (survey_id) REFERENCES Surveys(survey_id) ON DELETE CASCADE,
  INDEX idx_option_survey (survey_id),
  INDEX idx_option_order (survey_id, order_index)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ==========================================
-- Survey response submissions (1-question survey)
-- Supports:
--  - single-choice via survey_option_id
--  - multi-choice via multipleResponseID (JSON array of option_ids)
--  - free text via response_text
--  - blob via Attachment
-- ==========================================
CREATE TABLE IF NOT EXISTS `SurveyResponses` (
  response_id       INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  survey_id         INT UNSIGNED NOT NULL,

  -- For single-choice questions (pointing to a selected option)
  survey_option_id  INT UNSIGNED NULL DEFAULT NULL,

  -- User can be NULL when survey_type = 'anonymous'
  user_id           INT UNSIGNED NULL DEFAULT NULL,

  -- Legacy/aux fields you listed (kept but optional)
  singleResponseID  INT UNSIGNED NULL DEFAULT NULL,
  multipleResponseID JSON NULL DEFAULT NULL,      -- e.g., [3, 5, 7]

  -- For text/number/date/boolean/blob kinds of answers in a 1-question survey
  response_text     TEXT NULL DEFAULT NULL,
  Attachment        BLOB NULL,

  submitted_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (survey_id)        REFERENCES Surveys(survey_id) ON DELETE CASCADE,
  FOREIGN KEY (survey_option_id) REFERENCES SurveyOptions(option_id) ON DELETE SET NULL,
  FOREIGN KEY (user_id)          REFERENCES Users(user_id) ON DELETE SET NULL,

  INDEX idx_response_survey (survey_id),
  INDEX idx_response_option (survey_option_id),
  INDEX idx_response_user (user_id),
  INDEX idx_response_submitted (submitted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `SurveyRespondentsTracker ` (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  survey_id   INT UNSIGNED NOT NULL,
  user_id     INT UNSIGNED NOT NULL,
  responded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  -- enforce one response per user per survey
  UNIQUE KEY unique_user_survey (survey_id, user_id),

  FOREIGN KEY (survey_id) REFERENCES Surveys(survey_id) ON DELETE CASCADE,
  FOREIGN KEY (user_id)   REFERENCES Users(user_id) ON DELETE CASCADE,

  INDEX idx_survey_respondent (survey_id),
  INDEX idx_user_respondent (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- =========================
-- Main Surveys table
-- =========================
CREATE TABLE IF NOT EXISTS `Surveys` (
  survey_id              INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  title                  VARCHAR(255) NOT NULL,
  description            TEXT NULL,
  survey_type            ENUM('anonymous', 'identified') NOT NULL DEFAULT 'anonymous',
  created_at             DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at             DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  start_date             DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  end_date               DATETIME NULL DEFAULT NULL,
  max_responses          INT UNSIGNED NULL DEFAULT NULL,

  -- inline single question (by design: 1 survey = 1 question)
  question_text          TEXT NOT NULL,
  question_type          ENUM('text', 'textarea', 'single_choice', 'multiple_choice', 'rating', 'boolean', 'date', 'number','blob') NOT NULL,
  is_required            BOOLEAN NOT NULL DEFAULT FALSE,

  -- manual kill switch
  is_manually_disabled   BOOLEAN NOT NULL DEFAULT FALSE,

   -- survey status (will be managed by triggers)
  status                 ENUM('open', 'closed') NOT NULL DEFAULT 'open',

  -- helpful indexes
  INDEX idx_survey_dates (start_date, end_date),
  INDEX idx_survey_status (is_manually_disabled, start_date, end_date),

  -- basic consistency: end_date (if present) must be after start_date
  CHECK (end_date IS NULL OR end_date > start_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =========================
-- Availability Rules (JSON)
-- =========================
CREATE TABLE IF NOT EXISTS `SurveyAvailabilityRules` (
  id               INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  survey_id        INT UNSIGNED NOT NULL,
  rule_type        ENUM('native_language', 'native_variant', 'source_language', 'custom') NOT NULL,
  rules            JSON NOT NULL,  -- flexible rule logic
  is_enabled       BOOLEAN NOT NULL DEFAULT TRUE,
  created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (survey_id) REFERENCES Surveys(survey_id) ON DELETE CASCADE,
  INDEX idx_survey_rules (survey_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ==========================================
-- Options for choice-based questions
-- (supports blobs like image/audio/video)
-- ==========================================
CREATE TABLE IF NOT EXISTS `SurveyOptions` (
  option_id        INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  survey_id        INT UNSIGNED NOT NULL,
  option_text      VARCHAR(255) NOT NULL,
  option_value     VARCHAR(100) NULL DEFAULT NULL,   -- programmatic value / code
  Attachment       BLOB NULL,                        -- binary payload (image/video/audio)
  order_index      INT NOT NULL DEFAULT 0,
  is_enabled       BOOLEAN NOT NULL DEFAULT TRUE,
  created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (survey_id) REFERENCES Surveys(survey_id) ON DELETE CASCADE,
  INDEX idx_option_survey (survey_id),
  INDEX idx_option_order (survey_id, order_index)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ==========================================
-- Survey response submissions (1-question survey)
-- Supports:
--  - single-choice via survey_option_id
--  - multi-choice via multipleResponseID (JSON array of option_ids)
--  - free text via response_text
--  - blob via Attachment
-- ==========================================
CREATE TABLE IF NOT EXISTS `SurveyResponses` (
  response_id       INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  survey_id         INT UNSIGNED NOT NULL,

  -- For single-choice questions (pointing to a selected option)
  survey_option_id  INT UNSIGNED NULL DEFAULT NULL,

  -- User can be NULL when survey_type = 'anonymous'
  user_id           INT UNSIGNED NULL DEFAULT NULL,

  -- Legacy/aux fields you listed (kept but optional)
  singleResponseID  INT UNSIGNED NULL DEFAULT NULL,
  multipleResponseID JSON NULL DEFAULT NULL,      -- e.g., [3, 5, 7]

  -- For text/number/date/boolean/blob kinds of answers in a 1-question survey
  response_text     TEXT NULL DEFAULT NULL,
  Attachment        BLOB NULL,

  submitted_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (survey_id)        REFERENCES Surveys(survey_id) ON DELETE CASCADE,
  FOREIGN KEY (survey_option_id) REFERENCES SurveyOptions(option_id) ON DELETE SET NULL,
  FOREIGN KEY (user_id)          REFERENCES Users(user_id) ON DELETE SET NULL,

  INDEX idx_response_survey (survey_id),
  INDEX idx_response_option (survey_option_id),
  INDEX idx_response_user (user_id),
  INDEX idx_response_submitted (submitted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `SurveyRespondentsTracker ` (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  survey_id   INT UNSIGNED NOT NULL,
  user_id     INT UNSIGNED NOT NULL,
  responded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  -- enforce one response per user per survey
  UNIQUE KEY unique_user_survey (survey_id, user_id),

  FOREIGN KEY (survey_id) REFERENCES Surveys(survey_id) ON DELETE CASCADE,
  FOREIGN KEY (user_id)   REFERENCES Users(user_id) ON DELETE CASCADE,

  INDEX idx_survey_respondent (survey_id),
  INDEX idx_user_respondent (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
