DROP TABLE IF EXISTS mailer CASCADE;
CREATE TABLE mailer (
  id         INT UNSIGNED                          NOT NULL PRIMARY KEY AUTO_INCREMENT,
  queue_name CHAR(16)                              NOT NULL,
  mail       TEXT                                  NOT NULL,
  created    TIMESTAMP DEFAULT now()               NOT NULL,
  status     ENUM('waiting', 'processing', 'sent') NOT NULL
);

