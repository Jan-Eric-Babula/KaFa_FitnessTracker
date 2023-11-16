/* ******************************************

SCHEMA CREATION

****************************************** */

DROP VIEW IF EXISTS vw_calories;
DROP VIEW IF EXISTS vw_weight;
DROP TABLE IF EXISTS weight;
DROP TABLE IF EXISTS calories;
DROP TABLE IF EXISTS reference_list;

CREATE TABLE weight (
  timestamp timestamp NOT NULL DEFAULT current_timestamp(),
  weight int NOT NULL
);

CREATE VIEW vw_weight AS
SELECT a.timestamp AS timestamp, a.weight AS weight, a.weight - b.weight AS diff, TIMESTAMPDIFF(SECOND ,b.timestamp,a.timestamp) AS dur  FROM
(SELECT timestamp, weight, ROW_NUMBER() OVER (ORDER BY (SELECT 1)) AS row FROM weight ORDER BY timestamp ASC) AS a
LEFT JOIN
(SELECT timestamp, weight, ROW_NUMBER() OVER (ORDER BY (SELECT 1)) AS row FROM weight ORDER BY timestamp ASC) AS b
ON a.row-1 = b.row
ORDER BY timestamp;

CREATE TABLE reference_list (
  id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  calories int NOT NULL,
  description nvarchar(255) NOT NULL,
  description_clean nvarchar(300) NOT NULL,
  deleted bool NOT NULL DEFAULT FALSE
);

CREATE TABLE calories (
  timestamp timestamp NOT NULL DEFAULT current_timestamp(),
  reference int NULL DEFAULT NULL,
  custom_calories int NULL DEFAULT NULL,
  custom_description nvarchar(255) NULL DEFAULT NULL,
  CONSTRAINT fk_calories_references FOREIGN KEY (reference) REFERENCES reference_list(id),
  CONSTRAINT chk_calories_validation CHECK ( reference IS NOT NULL XOR custom_calories IS NOT NULL )
);

CREATE VIEW vw_calories AS SELECT
    timestamp,
    CASE WHEN reference IS NULL THEN custom_calories ELSE calories END AS calories,
    CASE WHEN reference IS NULL THEN custom_description ELSE description END AS description
FROM calories c LEFT JOIN reference_list rl on rl.id = c.reference
ORDER BY timestamp;
