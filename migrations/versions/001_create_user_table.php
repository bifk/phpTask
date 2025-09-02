<?php
    function up_001_create_user_table() {
        return "
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER GENERATED ALWAYS AS IDENTITY NOT NULL,
                username VARCHAR(255) NOT NULL UNIQUE,
                phone VARCHAR(12) NOT NULL UNIQUE,
                email VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL
            );

        CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
        CREATE INDEX IF NOT EXISTS idx_users_phone ON users(phone);
        ";
    }

    function down_001_create_user_table() {
        return "
            DROP TABLE IF EXISTS users;
            DROP INDEX IF EXISTS idx_users_email;
            DROP INDEX IF EXISTS idx_users_phone;
        ";
    }