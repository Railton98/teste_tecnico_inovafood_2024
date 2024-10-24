#!/usr/bin/env bash

mysql --user=root --password="$MYSQL_ROOT_PASSWORD" <<-EOSQL
    CREATE DATABASE IF NOT EXISTS \`inovafood\`;
    USE \`inovafood\`;

    CREATE TABLE IF NOT EXISTS \`checklists\` (
        \`id\` int NOT NULL AUTO_INCREMENT,
        \`title\` mediumtext NOT NULL,
        \`created_at\` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        \`updated_at\` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (\`id\`)
    );

    CREATE TABLE IF NOT EXISTS \`questions\` (
        \`id\` int NOT NULL AUTO_INCREMENT,
        \`id_checklist\` int NOT NULL,
        \`title\` mediumtext COLLATE utf8mb3_unicode_ci NOT NULL,
        \`type\` smallint NOT NULL DEFAULT 1,
        \`created_at\` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        \`updated_at\` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (\`id\`),
        FOREIGN KEY (\`id_checklist\`) REFERENCES \`checklists\` (\`id\`) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS \`alternatives\` (
        \`id\` int NOT NULL AUTO_INCREMENT,
        \`id_question\` int NOT NULL,
        \`title\` mediumtext NOT NULL,
        \`created_at\` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        \`updated_at\` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (\`id\`),
        FOREIGN KEY (\`id_question\`) REFERENCES \`questions\` (\`id\`) ON DELETE CASCADE
    );
EOSQL
