-- Create qualification_type table
CREATE TABLE IF NOT EXISTS swan_swa_qualification_type(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT, 
  name VARCHAR(100) NOT NULL, 
  PRIMARY KEY(id));

-- Insert the different types of qualifications into qualification_type table
INSERT INTO swan_swa_qualification_type
VALUES (1, "Powerboat Level 2"), (2, "Safety Boat"), (3, "Start Windsurfing Instructor"), (4, "Intermediate Windsurfing Instructor"), 
(5, "Advanced Windsurfing Instructor"), (6, "Senior Windsurfing Instructor"), (7, "Racing Windsurfing Instructor");

-- Add type_id to qualification table and make it a foreign key
ALTER TABLE swan_swa_qualification
ADD type_id INT UNSIGNED,
ADD CONSTRAINT fk_qualification_type_id FOREIGN KEY(type_id) REFERENCES swan_swa_qualification_type(id);

-- Update all the records to use type_id column rather than type column
UPDATE swan_swa_qualification
SET type_id = 1
WHERE type = "Powerboat Level 2";

UPDATE swan_swa_qualification 
SET type_id = 2
WHERE type = "Safety";

UPDATE swan_swa_qualification 
SET type_id = 3
WHERE type = "Start Windsurfing Instructor";

UPDATE swan_swa_qualification 
SET type_id = 4
WHERE type = "Intermediate Windsurfing Instructor";

UPDATE swan_swa_qualification 
SET type_id = 5
WHERE type = "Advanced Windsurfing Instructor";

UPDATE swan_swa_qualification 
SET type_id = 6
WHERE type = "Senior Windsurfing Instructor";

UPDATE swan_swa_qualification 
SET type_id = 7
WHERE type = "Racing Windsurfing Instructor";


ALTER TABLE swan_swa_qualification
-- remove the qualification_type column now that all the records use type_id
DROP COLUMN type,
-- set type_id to not null now that all the types have been converted to type_ids
MODIFY type_id INT UNSIGNED NOT NULL;


-- Improve the aprroved part of the qualification table
ALTER TABLE swan_swa_qualification
-- remove the NOT NULL from expiry_date
MODIFY expiry_date DATE,
-- add approved_on and approved_by columns
ADD approved_on DATE,
ADD approved_by INT UNSIGNED,
-- add foreign key
ADD CONSTRAINT fk_member_id FOREIGN KEY(approved_by) REFERENCES swan_swa_member(id);

-- set all current qualification expiry_dates to null
UPDATE swan_swa_qualification
SET expiry_date = null;

-- drop the approved column now that it uses the approved_on column
ALTER TABLE swan_swa_qualification
DROP COLUMN approved;



-- Create member_ability table to store whether a member can safety boat or instruct
CREATE TABLE IF NOT EXISTS swan_swa_member_ability(
	id INT NOT NULL AUTO_INCREMENT, 
    member_id INT NOT NULL, 
    safety_boat BOOLEAN NOT NULL, 
    instruct BOOLEAN NOT NULL, 
    PRIMARY KEY(id),
	CONSTRAINT `fk_member_id` FOREIGN KEY (`member_id`) REFERENCES `#__swa_member`(`id`)
);



-- Create t-shirt size table
CREATE TABLE IF NOT EXISTS `swan_swa_t-shirt_size`(id INT UNSIGNED NOT NULL AUTO_INCREMENT, name varchar(100) NOT NULL, PRIMARY KEY(id));

INSERT INTO `swan_swa_t-shirt_size`
VALUES (1, "Men's x-small"), (2, "Men's small"), (3, "Men's medium"), (4, "Men's large"), (5, "Men's x-large"),
(6, "Womens's x-small"), (7, "Womens's small"), (8, "Womens's medium"), (9, "Womens's large"), (10, "Womens's x-large");

-- Add t-shirt size and properties columns to ticket table
ALTER TABLE swan_swa_ticket
ADD `t-shirt_size_id` INT UNSIGNED,
ADD FOREIGN KEY(`t-shirt_size_id`) REFERENCES `swan_swa_t-shirt_size`(id),
-- properties will hold a JSON BLOB 
ADD properties BLOB;

-- DElETE T-shirt size column from member table as doing this on a per ticket basis
ALTER TABLE swan_swa_member
DROP COLUMN shirt;