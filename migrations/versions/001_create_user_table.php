<?php
    function up_001_create_user_table() {
        return "
            CREATE TABLE IF NOT EXISTS users (
                name VARCHAR(255) NOT NULL,
                phone VARCHAR(12) NOT NULL UNIQUE,
                email VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL
            );

        CREATE INDEX idx_users_email ON users(email);
        CREATE INDEX idx_users_phone ON users(phone);
        ";
    }

    function down_001_create_user_table() {
        return "
            DROP TABLE IF EXISTS `user`;
            DROP INDEX IF EXISTS idx_user_email;
            DROP INDEX IF EXISTS idx_users_phone;
        ";
    }