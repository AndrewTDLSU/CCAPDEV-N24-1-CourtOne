*Assumptions: proxmox Linux VM already have all the required packages and /var/www/html and MariaDB is ready to use

In the Linux VM, paste the following commands:
sudo adduser <username> -> creates new user (username: filezilla1 - password: apdev123)
sudo chmod 777 /var/www/html -> makes the required directory editable by the created user

In FileZilla:
I. Install FileZilla: https://filezilla-project.org/
II. Open the FileZilla app, click ‘File’ then ‘Site Manager’ then set:
1. Protocol to SFTP
2. Host to ccscloud1.dlsu.edu.ph
3. Port to 60171
4. (username: filezilla1 - password: apdev123)
III. Once connected, you can browse your files on the left and the CCS Cloud
VM files on the right. Drag and drop the files you want to be uploaded to
the proper directory in the VM.

MariaDB:
Password: ryax9uDAFvCN (double check /var/www/html/CCAPDEV-N24-1-CourtOne/core/Database.php if the correct password is used)

mariadb -u root -p -e "CREATE DATABASE IF NOT EXISTS courtbooking CHARACTER SET utf8mb4;" -> create the database
mariadb -u root -p courtbooking < CCAPDEV-N24-1-CourtOne.sql -> imports the database

Check if database is successfully imported: 
Mariadb -u root -p
show databases;
show tables;
select * from <table_name>;
