CREATE TABLE "user" (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(64) NOT NULL,
    email VARCHAR(255) NOT NULL,
    fullname VARCHAR(128) NOT NULL,
    password CHAR(32) NOT NULL,
    date_created DATE NOT NULL,
    date_banned DATE NULL
);
CREATE INDEX "user_username" ON "user" ("username");
CREATE INDEX "user_email" ON "user" ("email");

CREATE TABLE "bug" (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    reporter_id INTEGER NOT NULL,
    developer_id INTEGER NULL,
    priority_id INTEGER NOT NULL,
    type_id INTEGER NOT NULL,
    resolution_id INTEGER NULL,
    summary TEXT,
    description TEXT,
    date_created DATE NOT NULL,
    date_resolved DATE,
    date_closed DATE,
    date_deleted DATE
);
CREATE INDEX "bug_date_created" ON "bug" ("date_created");
CREATE INDEX "bug_date_resolved" ON "bug" ("date_resolved");
CREATE INDEX "bug_date_closed" ON "bug" ("date_closed");
CREATE INDEX "bug_date_deleted" ON "bug" ("date_deleted");

CREATE TABLE "comment" (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    bug_id INTEGER NOT NULL,
    "comment" TEXT,
    date_created DATE NOT NULL,
    date_deleted DATE
);
CREATE INDEX "comment_bug_id" ON "comment" ("bug_id");
CREATE INDEX "comment_user_id" ON "comment" ("user_id");

CREATE TABLE "issue_type" (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    "type" VARCHAR(255)
);
CREATE INDEX "issue_type_type" ON "issue_type" ("type");
INSERT INTO "issue_type" VALUES (1, "Bug");
INSERT INTO "issue_type" VALUES (2, "Feature request");
INSERT INTO "issue_type" VALUES (3, "Documentation issue");

CREATE TABLE "resolution_type" (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    "resolution" VARCHAR(255)
);
CREATE INDEX "resolution_type_type" ON "resolution_type" ("resolution");
INSERT INTO "resolution_type" VALUES (1, "Open");
INSERT INTO "resolution_type" VALUES (2, "In progress");
INSERT INTO "resolution_type" VALUES (3, "Will not fix");
INSERT INTO "resolution_type" VALUES (4, "Bogus");
INSERT INTO "resolution_type" VALUES (5, "Resolved");

CREATE TABLE "priority_type" (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    "priority" VARCHAR(255)
);
CREATE INDEX "priority_type_type" ON "priority_type" ("priority");
INSERT INTO "priority_type" VALUES (1, "Trivial");
INSERT INTO "priority_type" VALUES (2, "Minor");
INSERT INTO "priority_type" VALUES (3, "Normal");
INSERT INTO "priority_type" VALUES (4, "Major");
INSERT INTO "priority_type" VALUES (5, "Critical");

CREATE TABLE "relation_type" (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    "relation" VARCHAR(255)
);
CREATE INDEX "relation_type_type" ON "relation_type" ("relation");
INSERT INTO "relation_type" VALUES (1, "depends on");
INSERT INTO "relation_type" VALUES (2, "relates to");
INSERT INTO "relation_type" VALUES (3, "is dependent on");
INSERT INTO "relation_type" VALUES (4, "duplicates");

CREATE TABLE "bug_relation" (
    "id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    "bug_id" INTEGER NOT NULL,
    "related_id" INTEGER NOT NULL,
    "relation_type" INTEGER NOT NULL
);
CREATE INDEX "bug_relation_bug" ON "bug_relation" ("bug_id");
CREATE INDEX "bug_relation_related" ON "bug_relation" ("related_id");
