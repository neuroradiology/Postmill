<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190304211437 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE submissions_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE submission_votes_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE comments_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE comment_votes_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE bans_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE forums_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE notifications_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE access_token_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE client_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE refresh_token_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE message_threads_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE message_replies_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE forum_categories_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE wiki_pages_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE auth_code_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE users (id BIGINT NOT NULL, preferred_theme_id UUID DEFAULT NULL, username TEXT NOT NULL, normalized_username TEXT NOT NULL, password TEXT NOT NULL, email TEXT DEFAULT NULL, normalized_email TEXT DEFAULT NULL, created TIMESTAMP(0) WITH TIME ZONE NOT NULL, last_seen TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, admin BOOLEAN DEFAULT \'false\' NOT NULL, locale TEXT NOT NULL, night_mode BOOLEAN NOT NULL, show_custom_stylesheets BOOLEAN DEFAULT \'true\' NOT NULL, trusted BOOLEAN DEFAULT \'false\' NOT NULL, front_page TEXT DEFAULT \'default\' NOT NULL, open_external_links_in_new_tab BOOLEAN DEFAULT \'false\' NOT NULL, biography TEXT DEFAULT NULL, auto_fetch_submission_titles BOOLEAN DEFAULT \'true\' NOT NULL, enable_post_previews BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1483A5E94FC448E8 ON users (preferred_theme_id)');
        $this->addSql('CREATE UNIQUE INDEX users_username_idx ON users (username)');
        $this->addSql('CREATE UNIQUE INDEX users_normalized_username_idx ON users (normalized_username)');
        $this->addSql('COMMENT ON COLUMN users.preferred_theme_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE hidden_forums (user_id BIGINT NOT NULL, forum_id BIGINT NOT NULL, PRIMARY KEY(user_id, forum_id))');
        $this->addSql('CREATE INDEX IDX_9FEA4CBFA76ED395 ON hidden_forums (user_id)');
        $this->addSql('CREATE INDEX IDX_9FEA4CBF29CCBAD0 ON hidden_forums (forum_id)');
        $this->addSql('CREATE TABLE moderators (id UUID NOT NULL, forum_id BIGINT NOT NULL, user_id BIGINT NOT NULL, timestamp TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_580D16D329CCBAD0 ON moderators (forum_id)');
        $this->addSql('CREATE INDEX IDX_580D16D3A76ED395 ON moderators (user_id)');
        $this->addSql('CREATE UNIQUE INDEX moderator_forum_user_idx ON moderators (forum_id, user_id)');
        $this->addSql('COMMENT ON COLUMN moderators.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE submissions (id BIGINT NOT NULL, forum_id BIGINT NOT NULL, user_id BIGINT NOT NULL, title TEXT NOT NULL, url TEXT DEFAULT NULL, body TEXT DEFAULT NULL, timestamp TIMESTAMP(0) WITH TIME ZONE NOT NULL, image TEXT DEFAULT NULL, ip INET DEFAULT NULL, sticky BOOLEAN NOT NULL, ranking BIGINT NOT NULL, edited_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, moderated BOOLEAN DEFAULT \'false\' NOT NULL, user_flag SMALLINT DEFAULT 0 NOT NULL, locked BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3F6169F729CCBAD0 ON submissions (forum_id)');
        $this->addSql('CREATE INDEX IDX_3F6169F7A76ED395 ON submissions (user_id)');
        $this->addSql('CREATE INDEX submissions_ranking_id_idx ON submissions (ranking, id)');
        $this->addSql('COMMENT ON COLUMN submissions.ip IS \'(DC2Type:inet)\'');
        $this->addSql('CREATE TABLE submission_votes (id BIGINT NOT NULL, user_id BIGINT NOT NULL, submission_id BIGINT NOT NULL, upvote BOOLEAN NOT NULL, timestamp TIMESTAMP(0) WITH TIME ZONE NOT NULL, ip INET DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8C14006DA76ED395 ON submission_votes (user_id)');
        $this->addSql('CREATE INDEX IDX_8C14006DE1FD4933 ON submission_votes (submission_id)');
        $this->addSql('CREATE UNIQUE INDEX submission_user_vote_idx ON submission_votes (submission_id, user_id)');
        $this->addSql('COMMENT ON COLUMN submission_votes.ip IS \'(DC2Type:inet)\'');
        $this->addSql('CREATE TABLE comments (id BIGINT NOT NULL, user_id BIGINT NOT NULL, submission_id BIGINT NOT NULL, parent_id BIGINT DEFAULT NULL, body TEXT NOT NULL, timestamp TIMESTAMP(0) WITH TIME ZONE NOT NULL, soft_deleted BOOLEAN DEFAULT \'false\' NOT NULL, ip INET DEFAULT NULL, edited_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, moderated BOOLEAN DEFAULT \'false\' NOT NULL, user_flag SMALLINT DEFAULT 0 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5F9E962AA76ED395 ON comments (user_id)');
        $this->addSql('CREATE INDEX IDX_5F9E962AE1FD4933 ON comments (submission_id)');
        $this->addSql('CREATE INDEX IDX_5F9E962A727ACA70 ON comments (parent_id)');
        $this->addSql('COMMENT ON COLUMN comments.ip IS \'(DC2Type:inet)\'');
        $this->addSql('CREATE TABLE comment_votes (id BIGINT NOT NULL, user_id BIGINT NOT NULL, comment_id BIGINT NOT NULL, upvote BOOLEAN NOT NULL, timestamp TIMESTAMP(0) WITH TIME ZONE NOT NULL, ip INET DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F811E23EA76ED395 ON comment_votes (user_id)');
        $this->addSql('CREATE INDEX IDX_F811E23EF8697D13 ON comment_votes (comment_id)');
        $this->addSql('CREATE UNIQUE INDEX comment_user_vote_idx ON comment_votes (comment_id, user_id)');
        $this->addSql('COMMENT ON COLUMN comment_votes.ip IS \'(DC2Type:inet)\'');
        $this->addSql('CREATE TABLE user_bans (id UUID NOT NULL, user_id BIGINT NOT NULL, banned_by_id BIGINT NOT NULL, reason TEXT NOT NULL, banned BOOLEAN NOT NULL, timestamp TIMESTAMP(0) WITH TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B18D6BE5A76ED395 ON user_bans (user_id)');
        $this->addSql('CREATE INDEX IDX_B18D6BE5386B8E7 ON user_bans (banned_by_id)');
        $this->addSql('COMMENT ON COLUMN user_bans.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE bans (id BIGINT NOT NULL, user_id BIGINT DEFAULT NULL, banned_by_id BIGINT NOT NULL, ip INET NOT NULL, reason TEXT DEFAULT NULL, timestamp TIMESTAMP(0) WITH TIME ZONE NOT NULL, expiry_date TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CB0C272CA76ED395 ON bans (user_id)');
        $this->addSql('CREATE INDEX IDX_CB0C272C386B8E7 ON bans (banned_by_id)');
        $this->addSql('COMMENT ON COLUMN bans.ip IS \'(DC2Type:inet)\'');
        $this->addSql('CREATE TABLE forums (id BIGINT NOT NULL, category_id BIGINT DEFAULT NULL, theme_id UUID DEFAULT NULL, name TEXT NOT NULL, normalized_name TEXT NOT NULL, title TEXT NOT NULL, description TEXT DEFAULT NULL, sidebar TEXT DEFAULT NULL, created TIMESTAMP(0) WITH TIME ZONE NOT NULL, featured BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FE5E5AB812469DE2 ON forums (category_id)');
        $this->addSql('CREATE INDEX IDX_FE5E5AB859027487 ON forums (theme_id)');
        $this->addSql('CREATE INDEX forum_featured_idx ON forums (featured)');
        $this->addSql('CREATE UNIQUE INDEX forums_name_idx ON forums (name)');
        $this->addSql('CREATE UNIQUE INDEX forums_normalized_name_idx ON forums (normalized_name)');
        $this->addSql('COMMENT ON COLUMN forums.theme_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE notifications (id INT NOT NULL, user_id BIGINT NOT NULL, comment_id BIGINT DEFAULT NULL, thread_id BIGINT DEFAULT NULL, reply_id BIGINT DEFAULT NULL, submission_id BIGINT DEFAULT NULL, notification_type TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6000B0D3A76ED395 ON notifications (user_id)');
        $this->addSql('CREATE INDEX IDX_6000B0D3F8697D13 ON notifications (comment_id)');
        $this->addSql('CREATE INDEX IDX_6000B0D3E2904019 ON notifications (thread_id)');
        $this->addSql('CREATE INDEX IDX_6000B0D38A0E4E7F ON notifications (reply_id)');
        $this->addSql('CREATE INDEX IDX_6000B0D3E1FD4933 ON notifications (submission_id)');
        $this->addSql('CREATE TABLE themes (id UUID NOT NULL, author_id BIGINT NOT NULL, name TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_154232DEF675F31B ON themes (author_id)');
        $this->addSql('CREATE UNIQUE INDEX themes_author_name_idx ON themes (author_id, name)');
        $this->addSql('COMMENT ON COLUMN themes.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE user_blocks (id UUID NOT NULL, blocker_id BIGINT NOT NULL, blocked_id BIGINT NOT NULL, comment TEXT DEFAULT NULL, timestamp TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_ABBF8E45548D5975 ON user_blocks (blocker_id)');
        $this->addSql('CREATE INDEX IDX_ABBF8E4521FF5136 ON user_blocks (blocked_id)');
        $this->addSql('CREATE UNIQUE INDEX user_blocks_blocker_blocked_idx ON user_blocks (blocker_id, blocked_id)');
        $this->addSql('COMMENT ON COLUMN user_blocks.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE access_token (id INT NOT NULL, client_id INT NOT NULL, user_id BIGINT DEFAULT NULL, token VARCHAR(255) NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B6A2DD685F37A13B ON access_token (token)');
        $this->addSql('CREATE INDEX IDX_B6A2DD6819EB6921 ON access_token (client_id)');
        $this->addSql('CREATE INDEX IDX_B6A2DD68A76ED395 ON access_token (user_id)');
        $this->addSql('CREATE TABLE client (id INT NOT NULL, random_id VARCHAR(255) NOT NULL, redirect_uris TEXT NOT NULL, secret VARCHAR(255) NOT NULL, allowed_grant_types TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN client.redirect_uris IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN client.allowed_grant_types IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE refresh_token (id INT NOT NULL, client_id INT NOT NULL, user_id BIGINT DEFAULT NULL, token VARCHAR(255) NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C74F21955F37A13B ON refresh_token (token)');
        $this->addSql('CREATE INDEX IDX_C74F219519EB6921 ON refresh_token (client_id)');
        $this->addSql('CREATE INDEX IDX_C74F2195A76ED395 ON refresh_token (user_id)');
        $this->addSql('CREATE TABLE message_threads (id BIGINT NOT NULL, sender_id BIGINT NOT NULL, receiver_id BIGINT NOT NULL, body TEXT NOT NULL, timestamp TIMESTAMP(0) WITH TIME ZONE NOT NULL, ip INET DEFAULT NULL, title TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FF0607D1F624B39D ON message_threads (sender_id)');
        $this->addSql('CREATE INDEX IDX_FF0607D1CD53EDB6 ON message_threads (receiver_id)');
        $this->addSql('COMMENT ON COLUMN message_threads.ip IS \'(DC2Type:inet)\'');
        $this->addSql('CREATE TABLE forum_log_entries (id UUID NOT NULL, forum_id BIGINT NOT NULL, user_id BIGINT NOT NULL, author_id BIGINT DEFAULT NULL, submission_id BIGINT DEFAULT NULL, ban_id UUID DEFAULT NULL, was_admin BOOLEAN NOT NULL, timestamp TIMESTAMP(0) WITH TIME ZONE NOT NULL, action_type TEXT NOT NULL, title TEXT DEFAULT NULL, reason TEXT DEFAULT NULL, locked BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_130108F029CCBAD0 ON forum_log_entries (forum_id)');
        $this->addSql('CREATE INDEX IDX_130108F0A76ED395 ON forum_log_entries (user_id)');
        $this->addSql('CREATE INDEX IDX_130108F0F675F31B ON forum_log_entries (author_id)');
        $this->addSql('CREATE INDEX IDX_130108F0E1FD4933 ON forum_log_entries (submission_id)');
        $this->addSql('CREATE INDEX IDX_130108F01255CD1D ON forum_log_entries (ban_id)');
        $this->addSql('COMMENT ON COLUMN forum_log_entries.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN forum_log_entries.ban_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE message_replies (id BIGINT NOT NULL, sender_id BIGINT NOT NULL, thread_id BIGINT NOT NULL, body TEXT NOT NULL, timestamp TIMESTAMP(0) WITH TIME ZONE NOT NULL, ip INET DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_30885D26F624B39D ON message_replies (sender_id)');
        $this->addSql('CREATE INDEX IDX_30885D26E2904019 ON message_replies (thread_id)');
        $this->addSql('COMMENT ON COLUMN message_replies.ip IS \'(DC2Type:inet)\'');
        $this->addSql('CREATE TABLE forum_categories (id BIGINT NOT NULL, name TEXT NOT NULL, normalized_name TEXT NOT NULL, title TEXT NOT NULL, description TEXT NOT NULL, sidebar TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX forum_categories_name_idx ON forum_categories (name)');
        $this->addSql('CREATE UNIQUE INDEX forum_categories_normalized_name_idx ON forum_categories (normalized_name)');
        $this->addSql('CREATE TABLE wiki_pages (id BIGINT NOT NULL, path TEXT NOT NULL, normalized_path TEXT NOT NULL, locked BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX wiki_pages_path_idx ON wiki_pages (path)');
        $this->addSql('CREATE UNIQUE INDEX wiki_pages_normalized_path_idx ON wiki_pages (normalized_path)');
        $this->addSql('CREATE TABLE forum_subscriptions (id UUID NOT NULL, user_id BIGINT NOT NULL, forum_id BIGINT NOT NULL, subscribed_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_ECF780C4A76ED395 ON forum_subscriptions (user_id)');
        $this->addSql('CREATE INDEX IDX_ECF780C429CCBAD0 ON forum_subscriptions (forum_id)');
        $this->addSql('CREATE UNIQUE INDEX forum_user_idx ON forum_subscriptions (forum_id, user_id)');
        $this->addSql('COMMENT ON COLUMN forum_subscriptions.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE auth_code (id INT NOT NULL, client_id INT NOT NULL, user_id BIGINT DEFAULT NULL, token VARCHAR(255) NOT NULL, redirect_uri TEXT NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5933D02C5F37A13B ON auth_code (token)');
        $this->addSql('CREATE INDEX IDX_5933D02C19EB6921 ON auth_code (client_id)');
        $this->addSql('CREATE INDEX IDX_5933D02CA76ED395 ON auth_code (user_id)');
        $this->addSql('CREATE TABLE wiki_revisions (id UUID NOT NULL, page_id BIGINT NOT NULL, user_id BIGINT NOT NULL, title TEXT NOT NULL, body TEXT NOT NULL, timestamp TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_82813BA1C4663E4 ON wiki_revisions (page_id)');
        $this->addSql('CREATE INDEX IDX_82813BA1A76ED395 ON wiki_revisions (user_id)');
        $this->addSql('COMMENT ON COLUMN wiki_revisions.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE forum_webhooks (id UUID NOT NULL, forum_id BIGINT NOT NULL, event TEXT NOT NULL, url TEXT NOT NULL, secret_token TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BE7FC23A29CCBAD0 ON forum_webhooks (forum_id)');
        $this->addSql('COMMENT ON COLUMN forum_webhooks.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE forum_bans (id UUID NOT NULL, forum_id BIGINT NOT NULL, user_id BIGINT NOT NULL, banned_by_id BIGINT NOT NULL, reason TEXT NOT NULL, banned BOOLEAN NOT NULL, timestamp TIMESTAMP(0) WITH TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8ED87FB29CCBAD0 ON forum_bans (forum_id)');
        $this->addSql('CREATE INDEX IDX_8ED87FBA76ED395 ON forum_bans (user_id)');
        $this->addSql('CREATE INDEX IDX_8ED87FB386B8E7 ON forum_bans (banned_by_id)');
        $this->addSql('COMMENT ON COLUMN forum_bans.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE theme_revisions (id UUID NOT NULL, theme_id UUID NOT NULL, parent_id UUID DEFAULT NULL, common_css TEXT DEFAULT NULL, day_css TEXT DEFAULT NULL, night_css TEXT DEFAULT NULL, append_to_default_style BOOLEAN DEFAULT \'true\' NOT NULL, comment TEXT DEFAULT NULL, modified TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4772F80859027487 ON theme_revisions (theme_id)');
        $this->addSql('CREATE INDEX IDX_4772F808727ACA70 ON theme_revisions (parent_id)');
        $this->addSql('COMMENT ON COLUMN theme_revisions.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN theme_revisions.theme_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN theme_revisions.parent_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E94FC448E8 FOREIGN KEY (preferred_theme_id) REFERENCES themes (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE hidden_forums ADD CONSTRAINT FK_9FEA4CBFA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE hidden_forums ADD CONSTRAINT FK_9FEA4CBF29CCBAD0 FOREIGN KEY (forum_id) REFERENCES forums (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE moderators ADD CONSTRAINT FK_580D16D329CCBAD0 FOREIGN KEY (forum_id) REFERENCES forums (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE moderators ADD CONSTRAINT FK_580D16D3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE submissions ADD CONSTRAINT FK_3F6169F729CCBAD0 FOREIGN KEY (forum_id) REFERENCES forums (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE submissions ADD CONSTRAINT FK_3F6169F7A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE submission_votes ADD CONSTRAINT FK_8C14006DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE submission_votes ADD CONSTRAINT FK_8C14006DE1FD4933 FOREIGN KEY (submission_id) REFERENCES submissions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AE1FD4933 FOREIGN KEY (submission_id) REFERENCES submissions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A727ACA70 FOREIGN KEY (parent_id) REFERENCES comments (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comment_votes ADD CONSTRAINT FK_F811E23EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comment_votes ADD CONSTRAINT FK_F811E23EF8697D13 FOREIGN KEY (comment_id) REFERENCES comments (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_bans ADD CONSTRAINT FK_B18D6BE5A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_bans ADD CONSTRAINT FK_B18D6BE5386B8E7 FOREIGN KEY (banned_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE bans ADD CONSTRAINT FK_CB0C272CA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE bans ADD CONSTRAINT FK_CB0C272C386B8E7 FOREIGN KEY (banned_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE forums ADD CONSTRAINT FK_FE5E5AB812469DE2 FOREIGN KEY (category_id) REFERENCES forum_categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE forums ADD CONSTRAINT FK_FE5E5AB859027487 FOREIGN KEY (theme_id) REFERENCES themes (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3F8697D13 FOREIGN KEY (comment_id) REFERENCES comments (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3E2904019 FOREIGN KEY (thread_id) REFERENCES message_threads (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D38A0E4E7F FOREIGN KEY (reply_id) REFERENCES message_replies (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3E1FD4933 FOREIGN KEY (submission_id) REFERENCES submissions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE themes ADD CONSTRAINT FK_154232DEF675F31B FOREIGN KEY (author_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_blocks ADD CONSTRAINT FK_ABBF8E45548D5975 FOREIGN KEY (blocker_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_blocks ADD CONSTRAINT FK_ABBF8E4521FF5136 FOREIGN KEY (blocked_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE access_token ADD CONSTRAINT FK_B6A2DD6819EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE access_token ADD CONSTRAINT FK_B6A2DD68A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE refresh_token ADD CONSTRAINT FK_C74F219519EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE refresh_token ADD CONSTRAINT FK_C74F2195A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message_threads ADD CONSTRAINT FK_FF0607D1F624B39D FOREIGN KEY (sender_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message_threads ADD CONSTRAINT FK_FF0607D1CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE forum_log_entries ADD CONSTRAINT FK_130108F029CCBAD0 FOREIGN KEY (forum_id) REFERENCES forums (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE forum_log_entries ADD CONSTRAINT FK_130108F0A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE forum_log_entries ADD CONSTRAINT FK_130108F0F675F31B FOREIGN KEY (author_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE forum_log_entries ADD CONSTRAINT FK_130108F0E1FD4933 FOREIGN KEY (submission_id) REFERENCES submissions (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE forum_log_entries ADD CONSTRAINT FK_130108F01255CD1D FOREIGN KEY (ban_id) REFERENCES forum_bans (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message_replies ADD CONSTRAINT FK_30885D26F624B39D FOREIGN KEY (sender_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message_replies ADD CONSTRAINT FK_30885D26E2904019 FOREIGN KEY (thread_id) REFERENCES message_threads (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE forum_subscriptions ADD CONSTRAINT FK_ECF780C4A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE forum_subscriptions ADD CONSTRAINT FK_ECF780C429CCBAD0 FOREIGN KEY (forum_id) REFERENCES forums (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auth_code ADD CONSTRAINT FK_5933D02C19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auth_code ADD CONSTRAINT FK_5933D02CA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE wiki_revisions ADD CONSTRAINT FK_82813BA1C4663E4 FOREIGN KEY (page_id) REFERENCES wiki_pages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE wiki_revisions ADD CONSTRAINT FK_82813BA1A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE forum_webhooks ADD CONSTRAINT FK_BE7FC23A29CCBAD0 FOREIGN KEY (forum_id) REFERENCES forums (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE forum_bans ADD CONSTRAINT FK_8ED87FB29CCBAD0 FOREIGN KEY (forum_id) REFERENCES forums (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE forum_bans ADD CONSTRAINT FK_8ED87FBA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE forum_bans ADD CONSTRAINT FK_8ED87FB386B8E7 FOREIGN KEY (banned_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE theme_revisions ADD CONSTRAINT FK_4772F80859027487 FOREIGN KEY (theme_id) REFERENCES themes (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE theme_revisions ADD CONSTRAINT FK_4772F808727ACA70 FOREIGN KEY (parent_id) REFERENCES theme_revisions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE hidden_forums DROP CONSTRAINT FK_9FEA4CBFA76ED395');
        $this->addSql('ALTER TABLE moderators DROP CONSTRAINT FK_580D16D3A76ED395');
        $this->addSql('ALTER TABLE submissions DROP CONSTRAINT FK_3F6169F7A76ED395');
        $this->addSql('ALTER TABLE submission_votes DROP CONSTRAINT FK_8C14006DA76ED395');
        $this->addSql('ALTER TABLE comments DROP CONSTRAINT FK_5F9E962AA76ED395');
        $this->addSql('ALTER TABLE comment_votes DROP CONSTRAINT FK_F811E23EA76ED395');
        $this->addSql('ALTER TABLE user_bans DROP CONSTRAINT FK_B18D6BE5A76ED395');
        $this->addSql('ALTER TABLE user_bans DROP CONSTRAINT FK_B18D6BE5386B8E7');
        $this->addSql('ALTER TABLE bans DROP CONSTRAINT FK_CB0C272CA76ED395');
        $this->addSql('ALTER TABLE bans DROP CONSTRAINT FK_CB0C272C386B8E7');
        $this->addSql('ALTER TABLE notifications DROP CONSTRAINT FK_6000B0D3A76ED395');
        $this->addSql('ALTER TABLE themes DROP CONSTRAINT FK_154232DEF675F31B');
        $this->addSql('ALTER TABLE user_blocks DROP CONSTRAINT FK_ABBF8E45548D5975');
        $this->addSql('ALTER TABLE user_blocks DROP CONSTRAINT FK_ABBF8E4521FF5136');
        $this->addSql('ALTER TABLE access_token DROP CONSTRAINT FK_B6A2DD68A76ED395');
        $this->addSql('ALTER TABLE refresh_token DROP CONSTRAINT FK_C74F2195A76ED395');
        $this->addSql('ALTER TABLE message_threads DROP CONSTRAINT FK_FF0607D1F624B39D');
        $this->addSql('ALTER TABLE message_threads DROP CONSTRAINT FK_FF0607D1CD53EDB6');
        $this->addSql('ALTER TABLE forum_log_entries DROP CONSTRAINT FK_130108F0A76ED395');
        $this->addSql('ALTER TABLE forum_log_entries DROP CONSTRAINT FK_130108F0F675F31B');
        $this->addSql('ALTER TABLE message_replies DROP CONSTRAINT FK_30885D26F624B39D');
        $this->addSql('ALTER TABLE forum_subscriptions DROP CONSTRAINT FK_ECF780C4A76ED395');
        $this->addSql('ALTER TABLE auth_code DROP CONSTRAINT FK_5933D02CA76ED395');
        $this->addSql('ALTER TABLE wiki_revisions DROP CONSTRAINT FK_82813BA1A76ED395');
        $this->addSql('ALTER TABLE forum_bans DROP CONSTRAINT FK_8ED87FBA76ED395');
        $this->addSql('ALTER TABLE forum_bans DROP CONSTRAINT FK_8ED87FB386B8E7');
        $this->addSql('ALTER TABLE submission_votes DROP CONSTRAINT FK_8C14006DE1FD4933');
        $this->addSql('ALTER TABLE comments DROP CONSTRAINT FK_5F9E962AE1FD4933');
        $this->addSql('ALTER TABLE notifications DROP CONSTRAINT FK_6000B0D3E1FD4933');
        $this->addSql('ALTER TABLE forum_log_entries DROP CONSTRAINT FK_130108F0E1FD4933');
        $this->addSql('ALTER TABLE comments DROP CONSTRAINT FK_5F9E962A727ACA70');
        $this->addSql('ALTER TABLE comment_votes DROP CONSTRAINT FK_F811E23EF8697D13');
        $this->addSql('ALTER TABLE notifications DROP CONSTRAINT FK_6000B0D3F8697D13');
        $this->addSql('ALTER TABLE hidden_forums DROP CONSTRAINT FK_9FEA4CBF29CCBAD0');
        $this->addSql('ALTER TABLE moderators DROP CONSTRAINT FK_580D16D329CCBAD0');
        $this->addSql('ALTER TABLE submissions DROP CONSTRAINT FK_3F6169F729CCBAD0');
        $this->addSql('ALTER TABLE forum_log_entries DROP CONSTRAINT FK_130108F029CCBAD0');
        $this->addSql('ALTER TABLE forum_subscriptions DROP CONSTRAINT FK_ECF780C429CCBAD0');
        $this->addSql('ALTER TABLE forum_webhooks DROP CONSTRAINT FK_BE7FC23A29CCBAD0');
        $this->addSql('ALTER TABLE forum_bans DROP CONSTRAINT FK_8ED87FB29CCBAD0');
        $this->addSql('ALTER TABLE users DROP CONSTRAINT FK_1483A5E94FC448E8');
        $this->addSql('ALTER TABLE forums DROP CONSTRAINT FK_FE5E5AB859027487');
        $this->addSql('ALTER TABLE theme_revisions DROP CONSTRAINT FK_4772F80859027487');
        $this->addSql('ALTER TABLE access_token DROP CONSTRAINT FK_B6A2DD6819EB6921');
        $this->addSql('ALTER TABLE refresh_token DROP CONSTRAINT FK_C74F219519EB6921');
        $this->addSql('ALTER TABLE auth_code DROP CONSTRAINT FK_5933D02C19EB6921');
        $this->addSql('ALTER TABLE notifications DROP CONSTRAINT FK_6000B0D3E2904019');
        $this->addSql('ALTER TABLE message_replies DROP CONSTRAINT FK_30885D26E2904019');
        $this->addSql('ALTER TABLE notifications DROP CONSTRAINT FK_6000B0D38A0E4E7F');
        $this->addSql('ALTER TABLE forums DROP CONSTRAINT FK_FE5E5AB812469DE2');
        $this->addSql('ALTER TABLE wiki_revisions DROP CONSTRAINT FK_82813BA1C4663E4');
        $this->addSql('ALTER TABLE forum_log_entries DROP CONSTRAINT FK_130108F01255CD1D');
        $this->addSql('ALTER TABLE theme_revisions DROP CONSTRAINT FK_4772F808727ACA70');
        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE submissions_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE submission_votes_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE comments_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE comment_votes_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE bans_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE forums_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE notifications_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE access_token_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE client_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE refresh_token_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE message_threads_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE message_replies_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE forum_categories_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE wiki_pages_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE auth_code_id_seq CASCADE');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE hidden_forums');
        $this->addSql('DROP TABLE moderators');
        $this->addSql('DROP TABLE submissions');
        $this->addSql('DROP TABLE submission_votes');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE comment_votes');
        $this->addSql('DROP TABLE user_bans');
        $this->addSql('DROP TABLE bans');
        $this->addSql('DROP TABLE forums');
        $this->addSql('DROP TABLE notifications');
        $this->addSql('DROP TABLE themes');
        $this->addSql('DROP TABLE user_blocks');
        $this->addSql('DROP TABLE access_token');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE refresh_token');
        $this->addSql('DROP TABLE message_threads');
        $this->addSql('DROP TABLE forum_log_entries');
        $this->addSql('DROP TABLE message_replies');
        $this->addSql('DROP TABLE forum_categories');
        $this->addSql('DROP TABLE wiki_pages');
        $this->addSql('DROP TABLE forum_subscriptions');
        $this->addSql('DROP TABLE auth_code');
        $this->addSql('DROP TABLE wiki_revisions');
        $this->addSql('DROP TABLE forum_webhooks');
        $this->addSql('DROP TABLE forum_bans');
        $this->addSql('DROP TABLE theme_revisions');
    }
}
