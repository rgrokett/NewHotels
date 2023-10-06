
// UPDATE SCHEMA FOR LAT/LONG
//ALTER TABLE `new_hotels` ADD `lat` FLOAT(10,6) DEFAULT '0.0', ADD `lng` FLOAT(10,6) DEFAULT '0.0';


// UPDATE SCHEMA FOR state lat lng
ALTER TABLE `states_provinces` ADD `lat` FLOAT(10,6) DEFAULT '0.0', ADD `lng` FLOAT(10,6) DEFAULT '0.0';
