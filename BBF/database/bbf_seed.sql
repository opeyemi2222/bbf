-- ============================================================
-- BBF — Seed Data
-- Run this AFTER bbf_schema.sql
-- Populates: events, news, gallery
-- ============================================================

USE `bbf_db`;

-- ─── EVENTS ──────────────────────────────────────────────────────────────────
INSERT INTO `events`
  (title, category, description, event_date, end_date, location, status, featured, capacity)
VALUES
  ('BBF Inaugural Fellowship Gathering',
   'Fellowship',
   'The inaugural gathering of BBF members — a time of worship, fellowship, introductions, and shared commitment to the purpose of the Fellowship.',
   '2026-10-01', '2026-10-01', 'To Be Confirmed', 'upcoming', 1, 100),

  ('BBF Constitution Adoption Ceremony',
   'Fellowship',
   'Formal adoption of the BBF Constitution and inauguration of the founding executive officers of the Fellowship.',
   '2026-10-01', '2026-10-01', 'To Be Confirmed', 'upcoming', 1, 50),

  ('Fellowship Hangout & Social',
   'Social',
   'A relaxed social gathering for members to connect, share experiences, build friendships, and enjoy wholesome recreation together.',
   '2026-11-01', '2026-11-01', 'To Be Confirmed', 'upcoming', 0, 80),

  ('Community Service Day',
   'Community Service',
   'BBF members come together to serve their local community — demonstrating the brigade spirit of service in action.',
   '2026-11-15', '2026-11-15', 'To Be Confirmed', 'upcoming', 0, 60),

  ('Personal Development Workshop',
   'Training',
   'A skills and personal development session for members — covering professional growth, Brigade advancement, and life skills.',
   '2026-12-06', '2026-12-06', 'To Be Confirmed', 'upcoming', 0, 40);

-- ─── NEWS ─────────────────────────────────────────────────────────────────────
INSERT INTO `news`
  (title, category, author, summary, content, status, featured, views, published_at)
VALUES
  ('Brigade Brotherhood Fellowship is Now Open for Membership',
   'Announcement', 'BBF Secretariat',
   'BBF officially opens membership registration. All current and former Boys Brigade members who hold a Brigade School Certificate are welcome to register.',
   'BBF officially opens membership registration. All current and former Boys Brigade members who hold a Brigade School Certificate are welcome to register. Visit the membership page to sign up today.',
   'published', 1, 0, '2026-09-01 09:00:00'),

  ('BBF Constitution Formally Adopted',
   'Announcement', 'BBF Secretariat',
   'The founding members of Brigade Brotherhood Fellowship formally adopted the BBF Constitution, establishing the governance framework for the Fellowship.',
   'The founding members of Brigade Brotherhood Fellowship formally adopted the BBF Constitution, establishing the governance framework for the Fellowship. The Constitution covers membership, leadership, conduct, and all matters of governance.',
   'published', 1, 0, '2026-09-01 10:00:00'),

  ('Inaugural Fellowship Gathering Announced',
   'Announcement', 'BBF Events Team',
   'The BBF inaugural fellowship gathering has been announced. Members are encouraged to register and invite fellow Boys Brigade members to attend.',
   'The BBF inaugural fellowship gathering has been announced for October 2026. Members are encouraged to register via the Events page and invite fellow Boys Brigade members to attend.',
   'published', 0, 0, '2026-09-05 09:00:00'),

  ('What is the Brigade School Certificate Requirement?',
   'News', 'BBF Secretariat',
   'BBF explains the Brigade School Certificate membership requirement — what it means, who qualifies, and the transition arrangement for existing members.',
   'The Brigade School Certificate is the minimum qualification required for BBF membership. Members who are currently working towards it may register during the transition period.',
   'published', 0, 0, '2026-09-08 09:00:00'),

  ('BBF Code of Conduct — What Every Member Needs to Know',
   'News', 'BBF Secretariat',
   'A summary of the BBF Code of Conduct for members — covering respectful behaviour, confidentiality, Christian conduct, and protection of Fellowship unity.',
   'Every BBF member is expected to uphold the Code of Conduct as outlined in Article 9 of the BBF Constitution. Key points include: respect for all members, confidentiality of proceedings, Christian conduct at all times, and protection of the Fellowships reputation.',
   'published', 0, 0, '2026-09-10 09:00:00'),

  ('Executive Officers Wanted — Nominations Now Open',
   'Announcement', 'BBF Secretariat',
   'Nominations are now open for all executive officer positions of BBF. Eligible members who meet the criteria in Article 12 are encouraged to indicate interest.',
   'Nominations are now open for all nine executive officer positions of BBF including Coordinator, Vice Coordinator, Secretary, Financial Secretary, Treasurer, Welfare Officer, Social/Event Coordinator, and Publicity/Media Officer.',
   'draft', 0, 0, NULL);

-- ─── GALLERY ─────────────────────────────────────────────────────────────────
INSERT INTO `gallery`
  (title, category, image, year, status, uploader_name)
VALUES
  ('Fellowship Gathering',           'Fellowship',       'placeholder-gallery.jpg', 2026, 'published', 'BBF Media'),
  ('Brigade Parade',                 'Parade',           'placeholder-gallery.jpg', 2026, 'published', 'BBF Media'),
  ('Personal Development Workshop',  'Training',         'placeholder-gallery.jpg', 2026, 'published', 'BBF Media'),
  ('Community Service Day',          'Community Service','placeholder-gallery.jpg', 2026, 'published', 'BBF Media'),
  ('BBF Social Event',               'Social',           'placeholder-gallery.jpg', 2026, 'pending',   'BBF Media'),
  ('Executive Officers Inauguration','Fellowship',       'placeholder-gallery.jpg', 2026, 'pending',   'BBF Media'),
  ('Outreach Activity',              'Outreach',         'placeholder-gallery.jpg', 2026, 'published', 'BBF Media'),
  ('Youth Members at BBF Event',     'Fellowship',       'placeholder-gallery.jpg', 2026, 'published', 'BBF Media'),
  ('Skills Development Session',     'Training',         'placeholder-gallery.jpg', 2026, 'published', 'BBF Media'),
  ('Thanksgiving Service',           'Fellowship',       'placeholder-gallery.jpg', 2026, 'published', 'BBF Media'),
  ('Sports and Recreation Day',      'Social',           'placeholder-gallery.jpg', 2026, 'published', 'BBF Media'),
  ('Members in BBF Fellowship',      'Fellowship',       'placeholder-gallery.jpg', 2026, 'published', 'BBF Media');
